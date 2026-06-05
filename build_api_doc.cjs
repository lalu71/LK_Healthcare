// Build a Word doc listing every Doctor & Patient API for the LK Healthcare mobile app.
const fs = require('fs');
const path = require('path');

const docxPath = 'C:\\Users\\lalje\\AppData\\Roaming\\npm\\node_modules\\docx';
const {
  Document, Packer, Paragraph, TextRun, Table, TableRow, TableCell,
  AlignmentType, LevelFormat, HeadingLevel, BorderStyle, WidthType, ShadingType,
  PageBreak, TabStopType, TabStopPosition,
} = require(docxPath);

// ---------- helpers ----------
const BORDER = { style: BorderStyle.SINGLE, size: 6, color: "BBBBBB" };
const ALL_BORDERS = { top: BORDER, bottom: BORDER, left: BORDER, right: BORDER };

const P = (text, opts = {}) => new Paragraph({
  spacing: { after: 80 },
  ...opts,
  children: (Array.isArray(text) ? text : [new TextRun({ text, ...(opts.run || {}) })]),
});

const Mono = (text) => new TextRun({ text, font: "Consolas", size: 20 });
const Bold = (text) => new TextRun({ text, bold: true });
const Plain = (text) => new TextRun({ text });

const H1 = (text) => new Paragraph({
  heading: HeadingLevel.HEADING_1,
  spacing: { before: 320, after: 160 },
  children: [new TextRun({ text, bold: true, size: 32, color: "1F4E79" })],
});
const H2 = (text) => new Paragraph({
  heading: HeadingLevel.HEADING_2,
  spacing: { before: 240, after: 120 },
  children: [new TextRun({ text, bold: true, size: 26, color: "2E75B6" })],
});
const H3 = (text) => new Paragraph({
  heading: HeadingLevel.HEADING_3,
  spacing: { before: 200, after: 80 },
  children: [new TextRun({ text, bold: true, size: 22, color: "333333" })],
});

// Code block: light gray shaded paragraph with monospace font
const code = (text) => {
  const lines = text.split('\n');
  return lines.map((ln, i) => new Paragraph({
    spacing: { after: 0, line: 260 },
    shading: { fill: "F2F2F2", type: ShadingType.CLEAR },
    indent: { left: 120 },
    children: [new TextRun({ text: ln || ' ', font: "Consolas", size: 20 })],
  }));
};

const bullet = (text) => new Paragraph({
  numbering: { reference: "bullets", level: 0 },
  spacing: { after: 60 },
  children: typeof text === 'string'
    ? [new TextRun({ text })]
    : text,
});

// Build a 2-column "Field | Description" table for body params
const paramsTable = (rows) => {
  const headerCell = (txt) => new TableCell({
    borders: ALL_BORDERS,
    width: { size: 2340, type: WidthType.DXA },
    shading: { fill: "1F4E79", type: ShadingType.CLEAR },
    margins: { top: 80, bottom: 80, left: 120, right: 120 },
    children: [new Paragraph({ children: [new TextRun({ text: txt, bold: true, color: "FFFFFF" })] })],
  });
  const cell = (txt, w) => new TableCell({
    borders: ALL_BORDERS,
    width: { size: w, type: WidthType.DXA },
    margins: { top: 80, bottom: 80, left: 120, right: 120 },
    children: [new Paragraph({ children: [new TextRun({ text: txt, font: "Consolas", size: 20 })] })],
  });

  return new Table({
    width: { size: 9360, type: WidthType.DXA },
    columnWidths: [2340, 2340, 1560, 3120],
    rows: [
      new TableRow({
        tableHeader: true,
        children: [
          new TableCell({ borders: ALL_BORDERS, width: { size: 2340, type: WidthType.DXA },
            shading: { fill: "1F4E79", type: ShadingType.CLEAR },
            margins: { top: 80, bottom: 80, left: 120, right: 120 },
            children: [new Paragraph({ children: [new TextRun({ text: "Field", bold: true, color: "FFFFFF" })] })] }),
          new TableCell({ borders: ALL_BORDERS, width: { size: 2340, type: WidthType.DXA },
            shading: { fill: "1F4E79", type: ShadingType.CLEAR },
            margins: { top: 80, bottom: 80, left: 120, right: 120 },
            children: [new Paragraph({ children: [new TextRun({ text: "Type", bold: true, color: "FFFFFF" })] })] }),
          new TableCell({ borders: ALL_BORDERS, width: { size: 1560, type: WidthType.DXA },
            shading: { fill: "1F4E79", type: ShadingType.CLEAR },
            margins: { top: 80, bottom: 80, left: 120, right: 120 },
            children: [new Paragraph({ children: [new TextRun({ text: "Required", bold: true, color: "FFFFFF" })] })] }),
          new TableCell({ borders: ALL_BORDERS, width: { size: 3120, type: WidthType.DXA },
            shading: { fill: "1F4E79", type: ShadingType.CLEAR },
            margins: { top: 80, bottom: 80, left: 120, right: 120 },
            children: [new Paragraph({ children: [new TextRun({ text: "Note", bold: true, color: "FFFFFF" })] })] }),
        ],
      }),
      ...rows.map(r => new TableRow({
        children: [cell(r[0], 2340), cell(r[1], 2340), cell(r[2], 1560),
          new TableCell({ borders: ALL_BORDERS, width: { size: 3120, type: WidthType.DXA },
            margins: { top: 80, bottom: 80, left: 120, right: 120 },
            children: [new Paragraph({ children: [new TextRun({ text: r[3], size: 20 })] })] }),
        ],
      })),
    ],
  });
};

// Endpoint block: method + URL pill, role line, body, sample
function endpoint({ name, method, url, role, query, body, sample, notes }) {
  const out = [];

  out.push(H3(name));

  // Method + URL row (method as colored bold label)
  out.push(new Paragraph({
    spacing: { after: 80 },
    children: [
      new TextRun({ text: `[${method}] `, bold: true, color: methodColor(method), size: 24 }),
      new TextRun({ text: url, font: "Consolas", size: 22, bold: true }),
    ],
  }));

  if (role) {
    out.push(new Paragraph({
      spacing: { after: 80 },
      children: [
        new TextRun({ text: "Role: ", bold: true }),
        new TextRun({ text: role }),
      ],
    }));
  }

  if (notes) {
    out.push(new Paragraph({
      spacing: { after: 80 },
      children: [
        new TextRun({ text: "Note: ", bold: true, italics: true }),
        new TextRun({ text: notes, italics: true }),
      ],
    }));
  }

  if (query && query.length) {
    out.push(new Paragraph({ spacing: { after: 60 },
      children: [new TextRun({ text: "Query Params:", bold: true })] }));
    out.push(paramsTable(query));
    out.push(new Paragraph({ spacing: { after: 80 }, children: [new TextRun(" ")] }));
  }

  if (body && body.length) {
    out.push(new Paragraph({ spacing: { after: 60 },
      children: [new TextRun({ text: "Request Body (JSON):", bold: true })] }));
    out.push(paramsTable(body));
    out.push(new Paragraph({ spacing: { after: 80 }, children: [new TextRun(" ")] }));
  }

  if (sample) {
    out.push(new Paragraph({ spacing: { after: 60 },
      children: [new TextRun({ text: "Sample JSON:", bold: true })] }));
    out.push(...code(sample));
    out.push(new Paragraph({ spacing: { after: 120 }, children: [new TextRun(" ")] }));
  }

  return out;
}

function methodColor(m) {
  const map = { GET: "2E7D32", POST: "1565C0", PATCH: "EF6C00", PUT: "EF6C00", DELETE: "C62828" };
  return map[m] || "555555";
}

// ---------- build content ----------
const children = [];

// Title
children.push(new Paragraph({
  alignment: AlignmentType.CENTER,
  spacing: { after: 80 },
  children: [new TextRun({ text: "LK Healthcare — Mobile API Reference", bold: true, size: 40, color: "1F4E79" })],
}));
children.push(new Paragraph({
  alignment: AlignmentType.CENTER,
  spacing: { after: 240 },
  children: [new TextRun({ text: "Doctor & Patient endpoints for Flutter app", italics: true, size: 24, color: "666666" })],
}));

// Intro / common setup
children.push(H1("1. Setup & Common Headers"));
children.push(P([
  Bold("Base URL: "), Mono("http://<YOUR-HOST>/api/v1"),
]));
children.push(P([
  Plain("Local XAMPP (Android emulator): "), Mono("http://10.0.2.2/LK_Healthcare/public/api/v1"),
]));
children.push(P([
  Plain("Local XAMPP (real device on same Wi-Fi): "), Mono("http://<your-PC-LAN-IP>/LK_Healthcare/public/api/v1"),
]));

children.push(H2("Headers (har authenticated request me)"));
children.push(...code(
`Accept: application/json
Content-Type: application/json
Authorization: Bearer <TOKEN_FROM_LOGIN>`
));

children.push(H2("Token kaise milta hai?"));
children.push(bullet("/register ya /login call karne par response me data.token milega."));
children.push(bullet("Wo token har baad-wale request ke Authorization header me bhejna hai."));
children.push(bullet("/logout call karne par token revoke ho jata hai."));

// =============== AUTH ===============
children.push(new Paragraph({ children: [new PageBreak()] }));
children.push(H1("2. Authentication APIs (Public)"));

children.push(...endpoint({
  name: "2.1 Register (Patient signup)",
  method: "POST", url: "/api/v1/register",
  role: "Public — koi token nahi chahiye",
  body: [
    ["name", "string", "Yes", "Max 150 chars"],
    ["email", "string", "Yes", "Unique, valid email"],
    ["phone", "string", "No", "Max 20 chars"],
    ["password", "string", "Yes", "Min 6 chars"],
    ["password_confirmation", "string", "Yes", "Must match password"],
  ],
  sample:
`{
  "name": "Ramesh Kumar",
  "email": "ramesh@test.com",
  "phone": "9876543210",
  "password": "secret123",
  "password_confirmation": "secret123"
}`,
  notes: "Patient role automatic mil jata hai. Doctor account admin panel se banta hai.",
}));

children.push(...endpoint({
  name: "2.2 Login",
  method: "POST", url: "/api/v1/login",
  role: "Public",
  body: [
    ["email", "string", "Yes", "Registered email"],
    ["password", "string", "Yes", "User password"],
    ["device_name", "string", "No", "e.g. 'flutter-app'"],
  ],
  sample:
`{
  "email": "ramesh@test.com",
  "password": "secret123",
  "device_name": "flutter-app"
}`,
}));

children.push(...endpoint({
  name: "2.3 Get logged-in user",
  method: "GET", url: "/api/v1/user",
  role: "Authenticated (Bearer token)",
}));

children.push(...endpoint({
  name: "2.4 Logout",
  method: "POST", url: "/api/v1/logout",
  role: "Authenticated — current token revoke ho jayega",
}));

// =============== PATIENT ===============
children.push(new Paragraph({ children: [new PageBreak()] }));
children.push(H1("3. Patient APIs"));
children.push(P([Plain("Sabhi endpoints authenticated hain. Patient role wale users ke liye.")]));

children.push(H2("3.1 Patient Profile"));

children.push(...endpoint({
  name: "Apna profile dekho",
  method: "GET", url: "/api/v1/patient/me",
  role: "Patient",
}));

children.push(...endpoint({
  name: "Apna profile update karo",
  method: "PATCH", url: "/api/v1/patient/me",
  role: "Patient",
  body: [
    ["dob", "date (YYYY-MM-DD)", "No", "Date of birth"],
    ["gender", "string", "No", "male | female | other"],
    ["blood_group", "string", "No", "Max 5 chars, e.g. 'A+'"],
    ["allergies", "string", "No", "Max 500 chars"],
    ["medical_history", "string", "No", "Max 2000 chars"],
    ["emergency_contact", "string", "No", "Max 50 chars"],
    ["aadhaar_number", "string", "No", "Max 20 chars"],
  ],
  sample:
`{
  "dob": "1995-08-15",
  "gender": "male",
  "blood_group": "B+",
  "allergies": "Peanuts",
  "medical_history": "Asthma since 2018",
  "emergency_contact": "9999999999",
  "aadhaar_number": "1234-5678-9012"
}`,
}));

children.push(H2("3.2 Doctor browsing"));

children.push(...endpoint({
  name: "Specializations list",
  method: "GET", url: "/api/v1/specializations",
  role: "Authenticated",
}));

children.push(...endpoint({
  name: "All doctors (search + filter)",
  method: "GET", url: "/api/v1/doctors",
  role: "Authenticated",
  query: [
    ["q", "string", "No", "Doctor name search"],
    ["specialization_id", "int", "No", "Filter by specialization"],
    ["page", "int", "No", "Pagination (15 per page)"],
  ],
  sample: `GET /api/v1/doctors?q=ram&specialization_id=2&page=1`,
}));

children.push(...endpoint({
  name: "Single doctor detail",
  method: "GET", url: "/api/v1/doctors/{doctor_id}",
  role: "Authenticated",
  sample: `GET /api/v1/doctors/7`,
}));

children.push(...endpoint({
  name: "Doctor ke available slots",
  method: "GET", url: "/api/v1/doctors/{doctor_id}/slots",
  role: "Authenticated",
  query: [
    ["date", "string (YYYY-MM-DD)", "Yes", "Jis din ke slots chahiye"],
  ],
  sample: `GET /api/v1/doctors/7/slots?date=2026-05-20`,
}));

children.push(H2("3.3 Patient Appointments"));

children.push(...endpoint({
  name: "Meri appointments list",
  method: "GET", url: "/api/v1/my/appointments",
  role: "Patient",
  query: [
    ["status", "string", "No", "pending | confirmed | completed | cancelled"],
  ],
  sample: `GET /api/v1/my/appointments?status=pending`,
}));

children.push(...endpoint({
  name: "Appointment book karo",
  method: "POST", url: "/api/v1/appointments",
  role: "Patient",
  body: [
    ["doctor_id", "int", "Yes", "Doctor id"],
    ["appointment_date", "datetime", "Yes", "Future datetime (YYYY-MM-DD HH:MM:SS or ISO)"],
    ["reason", "string", "No", "Max 500 chars"],
  ],
  sample:
`{
  "doctor_id": 7,
  "appointment_date": "2026-05-20 14:30:00",
  "reason": "Fever and body ache"
}`,
}));

children.push(...endpoint({
  name: "Appointment cancel karo",
  method: "PATCH", url: "/api/v1/appointments/{appointment_id}/cancel",
  role: "Patient (sirf apni appointment)",
  sample: `PATCH /api/v1/appointments/15/cancel`,
}));

children.push(H2("3.4 Patient Prescriptions"));

children.push(...endpoint({
  name: "Meri saari prescriptions",
  method: "GET", url: "/api/v1/my/prescriptions",
  role: "Patient",
}));

children.push(...endpoint({
  name: "Single prescription dekho",
  method: "GET", url: "/api/v1/prescriptions/{prescription_id}",
  role: "Patient (owner) ya Doctor (issuer)",
}));

children.push(H2("3.5 Lab Tests"));

children.push(...endpoint({
  name: "Lab tests catalog",
  method: "GET", url: "/api/v1/lab/tests",
  role: "Authenticated",
  query: [
    ["q", "string", "No", "Test name search"],
  ],
}));

children.push(...endpoint({
  name: "Lab test book karo",
  method: "POST", url: "/api/v1/lab/book",
  role: "Patient",
  body: [
    ["lab_test_id", "int", "Yes", "From /lab/tests"],
    ["booking_date", "datetime", "Yes", "Future datetime"],
    ["notes", "string", "No", "Max 500 chars"],
  ],
  sample:
`{
  "lab_test_id": 3,
  "booking_date": "2026-05-22 09:00:00",
  "notes": "Fasting since 8 hours"
}`,
}));

children.push(...endpoint({
  name: "Meri lab bookings",
  method: "GET", url: "/api/v1/my/lab-bookings",
  role: "Patient",
}));

children.push(H2("3.6 Pharmacy"));

children.push(...endpoint({
  name: "Medicines list",
  method: "GET", url: "/api/v1/pharmacy/medicines",
  role: "Authenticated",
  query: [
    ["q", "string", "No", "Medicine name search"],
  ],
}));

children.push(...endpoint({
  name: "Order place karo",
  method: "POST", url: "/api/v1/pharmacy/orders",
  role: "Patient",
  body: [
    ["items", "array", "Yes", "Min 1 item"],
    ["items[].medicine_id", "int", "Yes", "From /pharmacy/medicines"],
    ["items[].quantity", "int", "Yes", "1 to 50"],
    ["delivery_address", "string", "Yes", "Max 500"],
    ["delivery_phone", "string", "Yes", "Max 20"],
    ["notes", "string", "No", "Max 500"],
  ],
  sample:
`{
  "items": [
    { "medicine_id": 12, "quantity": 2 },
    { "medicine_id": 7,  "quantity": 1 }
  ],
  "delivery_address": "House 23, MG Road, Lucknow 226001",
  "delivery_phone": "9876543210",
  "notes": "Call before delivery"
}`,
  notes: "Subtotal >= 500 par delivery free; nahi to Rs.40 delivery fee.",
}));

children.push(...endpoint({
  name: "Mere pharmacy orders",
  method: "GET", url: "/api/v1/my/pharmacy-orders",
  role: "Patient",
}));

children.push(H2("3.7 Medical Records"));

children.push(...endpoint({
  name: "Mere medical records",
  method: "GET", url: "/api/v1/my/medical-records",
  role: "Patient",
}));

children.push(...endpoint({
  name: "Medical record upload",
  method: "POST", url: "/api/v1/medical-records",
  role: "Patient",
  body: [
    ["title", "string", "Yes", "Max 200"],
    ["type", "string", "No", "Max 50, default 'other'"],
    ["description", "string", "No", "Max 1000"],
    ["record_date", "date", "No", "YYYY-MM-DD"],
    ["file", "file (multipart)", "Yes", "Max 10 MB"],
  ],
  notes: "Multipart/form-data bhejna hai (not JSON). Content-Type: multipart/form-data.",
}));

children.push(...endpoint({
  name: "Medical record delete",
  method: "DELETE", url: "/api/v1/medical-records/{record_id}",
  role: "Patient (sirf apna)",
}));

children.push(H2("3.8 Blood Bank"));

children.push(...endpoint({
  name: "Blood inventory",
  method: "GET", url: "/api/v1/blood/inventory",
  role: "Authenticated",
}));

children.push(...endpoint({
  name: "Donors list",
  method: "GET", url: "/api/v1/blood/donors",
  role: "Authenticated",
  query: [
    ["blood_group", "string", "No", "e.g. 'A+', 'O-'"],
  ],
}));

children.push(...endpoint({
  name: "Blood request banao",
  method: "POST", url: "/api/v1/blood/requests",
  role: "Authenticated",
  body: [
    ["patient_name", "string", "Yes", "Max 150"],
    ["blood_group", "string", "Yes", "Max 5"],
    ["units", "int", "Yes", "1 to 20"],
    ["hospital", "string", "No", "Max 200"],
    ["contact_phone", "string", "Yes", "Max 20"],
    ["needed_by", "date", "Yes", "Today ya future"],
    ["reason", "string", "No", "Max 500"],
  ],
  sample:
`{
  "patient_name": "Sita Devi",
  "blood_group": "O+",
  "units": 2,
  "hospital": "SGPGI Lucknow",
  "contact_phone": "9876543210",
  "needed_by": "2026-05-18",
  "reason": "Surgery"
}`,
}));

children.push(...endpoint({
  name: "Khud donor banno",
  method: "POST", url: "/api/v1/blood/donor",
  role: "Authenticated",
  body: [
    ["name", "string", "Yes", "Max 150"],
    ["blood_group", "string", "Yes", "Max 5"],
    ["phone", "string", "Yes", "Max 20"],
    ["city", "string", "Yes", "Max 100"],
    ["last_donated_at", "date", "No", "Today ya past"],
  ],
  sample:
`{
  "name": "Ramesh Kumar",
  "blood_group": "B+",
  "phone": "9876543210",
  "city": "Lucknow",
  "last_donated_at": "2026-02-10"
}`,
}));

children.push(...endpoint({
  name: "Meri blood requests",
  method: "GET", url: "/api/v1/my/blood-requests",
  role: "Authenticated",
}));

children.push(H2("3.9 Emergency SOS"));

children.push(...endpoint({
  name: "Emergency raise karo",
  method: "POST", url: "/api/v1/emergency",
  role: "Authenticated",
  body: [
    ["contact_name", "string", "Yes", "Max 150"],
    ["contact_phone", "string", "Yes", "Max 20"],
    ["location", "string", "Yes", "Max 500"],
    ["latitude", "number", "No", "-90 to 90"],
    ["longitude", "number", "No", "-180 to 180"],
    ["description", "string", "No", "Max 1000"],
  ],
  sample:
`{
  "contact_name": "Ramesh Kumar",
  "contact_phone": "9876543210",
  "location": "Hazratganj, Lucknow",
  "latitude": 26.8467,
  "longitude": 80.9462,
  "description": "Severe chest pain"
}`,
}));

children.push(...endpoint({
  name: "Meri emergency history",
  method: "GET", url: "/api/v1/my/emergency-requests",
  role: "Authenticated",
}));

children.push(H2("3.10 Notifications"));

children.push(...endpoint({
  name: "Saari notifications",
  method: "GET", url: "/api/v1/notifications",
  role: "Authenticated",
}));

children.push(...endpoint({
  name: "Sab read kar do",
  method: "POST", url: "/api/v1/notifications/read-all",
  role: "Authenticated",
}));

// =============== DOCTOR ===============
children.push(new Paragraph({ children: [new PageBreak()] }));
children.push(H1("4. Doctor APIs"));
children.push(P([Plain("Sabhi endpoints authenticated hain. Doctor role + linked Doctor profile honi chahiye.")]));

children.push(H2("4.1 Doctor Profile"));

children.push(...endpoint({
  name: "Apni profile",
  method: "GET", url: "/api/v1/doctor/me",
  role: "Doctor",
}));

children.push(...endpoint({
  name: "Profile update",
  method: "PATCH", url: "/api/v1/doctor/me",
  role: "Doctor",
  body: [
    ["specialization_id", "int", "No", "From /specializations"],
    ["qualification", "string", "No", "Max 200"],
    ["experience_years", "int", "No", "0 to 80"],
    ["consultation_fee", "number", "No", ">= 0"],
    ["bio", "string", "No", "Max 2000"],
    ["clinic_address", "string", "No", "Max 500"],
  ],
  sample:
`{
  "specialization_id": 2,
  "qualification": "MBBS, MD (Cardiology)",
  "experience_years": 12,
  "consultation_fee": 500,
  "bio": "Senior cardiologist with 12+ years experience.",
  "clinic_address": "City Heart Clinic, Hazratganj, Lucknow"
}`,
}));

children.push(H2("4.2 Doctor Availability (Slots banana)"));

children.push(...endpoint({
  name: "Apni saari availability",
  method: "GET", url: "/api/v1/doctor/availability",
  role: "Doctor",
}));

children.push(...endpoint({
  name: "Naya slot add karo",
  method: "POST", url: "/api/v1/doctor/availability",
  role: "Doctor",
  body: [
    ["day_of_week", "int", "Yes", "0 = Sunday ... 6 = Saturday"],
    ["start_time", "string (HH:MM)", "Yes", "24-hour format, e.g. '09:00'"],
    ["end_time", "string (HH:MM)", "Yes", "Must be after start_time"],
    ["slot_minutes", "int", "No", "5 to 240, default 30"],
  ],
  sample:
`{
  "day_of_week": 1,
  "start_time": "09:00",
  "end_time": "13:00",
  "slot_minutes": 30
}`,
}));

children.push(...endpoint({
  name: "Availability delete",
  method: "DELETE", url: "/api/v1/doctor/availability/{availability_id}",
  role: "Doctor (apni)",
}));

children.push(H2("4.3 Doctor Appointments"));

children.push(...endpoint({
  name: "Mere appointments (today/upcoming)",
  method: "GET", url: "/api/v1/doctor/appointments",
  role: "Doctor",
  query: [
    ["scope", "string", "No", "today (default) | upcoming | all"],
  ],
  sample: `GET /api/v1/doctor/appointments?scope=upcoming`,
}));

children.push(...endpoint({
  name: "Appointment status update",
  method: "PATCH", url: "/api/v1/appointments/{appointment_id}/status",
  role: "Doctor (apni appointment)",
  body: [
    ["status", "string", "Yes", "confirmed | completed | cancelled"],
  ],
  sample:
`{
  "status": "confirmed"
}`,
}));

children.push(H2("4.4 Doctor Prescriptions"));

children.push(...endpoint({
  name: "Maine jo prescriptions di",
  method: "GET", url: "/api/v1/doctor/prescriptions",
  role: "Doctor",
}));

children.push(...endpoint({
  name: "Single prescription dekho",
  method: "GET", url: "/api/v1/prescriptions/{prescription_id}",
  role: "Doctor (issuer) ya Patient (owner)",
}));

children.push(...endpoint({
  name: "Nayi prescription likho",
  method: "POST", url: "/api/v1/prescriptions",
  role: "Doctor (apni appointment ki)",
  body: [
    ["appointment_id", "int", "Yes", "Existing appointment id"],
    ["diagnosis", "string", "No", "Free text"],
    ["advice", "string", "No", "Free text"],
    ["follow_up_date", "date", "No", "YYYY-MM-DD"],
    ["items", "array", "Yes", "Min 1 medicine row"],
    ["items[].medicine_name", "string", "Yes", "Max 150"],
    ["items[].dosage", "string", "Yes", "Max 80, e.g. '1 tab'"],
    ["items[].frequency", "string", "Yes", "Max 80, e.g. 'TID'"],
    ["items[].duration", "string", "Yes", "Max 80, e.g. '5 days'"],
    ["items[].instructions", "string", "No", "Max 200"],
  ],
  sample:
`{
  "appointment_id": 12,
  "diagnosis": "Viral fever",
  "advice": "Rest for 3 days, plenty of fluids",
  "follow_up_date": "2026-05-22",
  "items": [
    {
      "medicine_name": "Paracetamol 500mg",
      "dosage": "1 tab",
      "frequency": "TID",
      "duration": "5 days",
      "instructions": "After food"
    },
    {
      "medicine_name": "Cetirizine 10mg",
      "dosage": "1 tab",
      "frequency": "OD at bedtime",
      "duration": "3 days",
      "instructions": "Avoid driving"
    }
  ]
}`,
}));

// =============== TESTING NOTES ===============
children.push(new Paragraph({ children: [new PageBreak()] }));
children.push(H1("5. Mobile se test karne ke tips"));

children.push(H2("Postman / Thunder Client se"));
children.push(bullet("Pehle POST /login chala ke 'data.token' copy karo."));
children.push(bullet("Bearer Token tab me wo token paste karo."));
children.push(bullet("Phir koi bhi protected endpoint chalao."));

children.push(H2("Flutter (Dio) example"));
children.push(...code(
`final dio = Dio(BaseOptions(
  baseUrl: 'http://10.0.2.2/LK_Healthcare/public/api/v1',
  headers: { 'Accept': 'application/json' },
));

// 1) Login
final r = await dio.post('/login', data: {
  'email': 'ramesh@test.com',
  'password': 'secret123',
  'device_name': 'flutter-app',
});
final token = r.data['data']['token'];

// 2) Use token
dio.options.headers['Authorization'] = 'Bearer \$token';

// 3) Call any API
final doctors = await dio.get('/doctors', queryParameters: { 'q': 'ram' });
final book = await dio.post('/appointments', data: {
  'doctor_id': 7,
  'appointment_date': '2026-05-20 14:30:00',
  'reason': 'Fever',
});`
));

children.push(H2("Common HTTP status codes"));
children.push(bullet("200 — OK"));
children.push(bullet("201 — Created (POST success)"));
children.push(bullet("401 — Token nahi/expire — dobara login karo"));
children.push(bullet("403 — Permission nahi (jaise patient ne doctor endpoint hit kiya)"));
children.push(bullet("404 — Resource ya patient/doctor profile nahi mila"));
children.push(bullet("422 — Validation fail (body fields galat)"));

// ---------- build document ----------
const doc = new Document({
  creator: "LK Healthcare",
  title: "LK Healthcare Mobile API Reference",
  styles: {
    default: { document: { run: { font: "Calibri", size: 22 } } },
    paragraphStyles: [
      { id: "Heading1", name: "Heading 1", basedOn: "Normal", next: "Normal", quickFormat: true,
        run: { size: 32, bold: true, font: "Calibri", color: "1F4E79" },
        paragraph: { spacing: { before: 320, after: 160 }, outlineLevel: 0 } },
      { id: "Heading2", name: "Heading 2", basedOn: "Normal", next: "Normal", quickFormat: true,
        run: { size: 26, bold: true, font: "Calibri", color: "2E75B6" },
        paragraph: { spacing: { before: 240, after: 120 }, outlineLevel: 1 } },
      { id: "Heading3", name: "Heading 3", basedOn: "Normal", next: "Normal", quickFormat: true,
        run: { size: 22, bold: true, font: "Calibri", color: "333333" },
        paragraph: { spacing: { before: 200, after: 80 }, outlineLevel: 2 } },
    ],
  },
  numbering: {
    config: [
      { reference: "bullets",
        levels: [{ level: 0, format: LevelFormat.BULLET, text: "•", alignment: AlignmentType.LEFT,
          style: { paragraph: { indent: { left: 720, hanging: 360 } } } }] },
    ],
  },
  sections: [{
    properties: {
      page: {
        size: { width: 12240, height: 15840 },
        margin: { top: 1440, right: 1440, bottom: 1440, left: 1440 },
      },
    },
    children,
  }],
});

Packer.toBuffer(doc).then(buf => {
  const out = path.join(__dirname, "LK_Healthcare_Mobile_API_Reference.docx");
  fs.writeFileSync(out, buf);
  console.log("Wrote:", out, "size:", buf.length, "bytes");
});

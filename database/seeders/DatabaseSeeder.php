<?php

namespace Database\Seeders;

use App\Models\Availability;
use App\Models\BloodInventory;
use App\Models\Doctor;
use App\Models\LabTest;
use App\Models\Medicine;
use App\Models\Patient;
use App\Models\Specialization;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(SpecializationSeeder::class);

        // Roles
        foreach (['admin', 'doctor', 'patient', 'pharmacist'] as $r) {
            Role::firstOrCreate(['name' => $r]);
        }

        // Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@lkhealthcare.in'],
            ['name' => 'System Admin', 'password' => Hash::make('password'), 'phone' => '+911800000000']
        );
        $admin->syncRoles(['admin']);
        
        // Pharmacist
        $pharmacist = User::firstOrCreate(
            ['email' => 'pharmacy@lkhealthcare.in'],
            ['name' => 'LK Pharmacy Store', 'password' => Hash::make('password'), 'phone' => '+911800111222']
        );
        $pharmacist->syncRoles(['pharmacist']);

        // Demo patient
        $pUser = User::firstOrCreate(
            ['email' => 'patient@lkhealthcare.in'],
            ['name' => 'Aarav Sharma', 'password' => Hash::make('password'), 'phone' => '+919999999991']
        );
        $pUser->syncRoles(['patient']);
        Patient::firstOrCreate(
            ['user_id' => $pUser->id],
            [
                'patient_id' => 'LK-PAT-' . date('Y') . '-0001',
                'dob' => '1995-06-15',
                'gender' => 'male',
                'blood_group' => 'O+',
                'emergency_contact' => '+919999900000',
                'allergies' => 'Penicillin',
            ]
        );

        // Demo doctors
        $specs = Specialization::pluck('id', 'name');
        $doctorData = [
            ['Anjali Verma', 'doctor.anjali@lkhealthcare.in', 'Cardiologist', 12, 1200, 'MBBS, MD (Cardiology)'],
            ['Rahul Mehta', 'doctor.rahul@lkhealthcare.in', 'Dermatologist', 9, 800, 'MBBS, MD (Dermatology)'],
            ['Priya Iyer', 'doctor.priya@lkhealthcare.in', 'Pediatrician', 7, 600, 'MBBS, DCH'],
            ['Vikram Singh', 'doctor.vikram@lkhealthcare.in', 'Orthopedist', 15, 1500, 'MBBS, MS (Ortho)'],
            ['Neha Gupta', 'doctor.neha@lkhealthcare.in', 'General Physician', 6, 500, 'MBBS'],
            ['Sanjay Rao', 'doctor.sanjay@lkhealthcare.in', 'Neurologist', 18, 1800, 'MBBS, DM (Neuro)'],
        ];
        foreach ($doctorData as [$name, $email, $spec, $exp, $fee, $qual]) {
            $u = User::firstOrCreate(['email' => $email], ['name' => $name, 'password' => Hash::make('password'), 'phone' => '+9199999'.rand(10000,99999)]);
            $u->syncRoles(['doctor']);
            $doc = Doctor::firstOrCreate(
                ['user_id' => $u->id],
                [
                    'specialization_id' => $specs[$spec] ?? $specs->first(),
                    'experience_years' => $exp,
                    'consultation_fee' => $fee,
                    'qualification' => $qual,
                    'is_active' => true,
                    'bio' => 'Board-certified '.$spec.' with '.$exp.' years of experience.',
                ]
            );
            // Default availability Mon-Fri 10-13 & 17-20
            foreach ([1,2,3,4,5] as $dow) {
                Availability::firstOrCreate([
                    'doctor_id' => $doc->id, 'day_of_week' => $dow, 'start_time' => '10:00:00', 'end_time' => '13:00:00',
                ], ['slot_minutes' => 30, 'is_active' => true]);
                Availability::firstOrCreate([
                    'doctor_id' => $doc->id, 'day_of_week' => $dow, 'start_time' => '17:00:00', 'end_time' => '20:00:00',
                ], ['slot_minutes' => 30, 'is_active' => true]);
            }
            // Saturday morning
            Availability::firstOrCreate([
                'doctor_id' => $doc->id, 'day_of_week' => 6, 'start_time' => '10:00:00', 'end_time' => '14:00:00',
            ], ['slot_minutes' => 30, 'is_active' => true]);
        }

        // Lab tests
        foreach ([
            ['Complete Blood Count (CBC)', 'Hematology', 400, 12],
            ['Lipid Profile', 'Chemistry', 600, 24],
            ['Thyroid T3, T4, TSH', 'Endocrine', 750, 24],
            ['Blood Sugar Fasting', 'Chemistry', 150, 6],
            ['HbA1c', 'Diabetes', 500, 12],
            ['Vitamin D', 'Nutrition', 1200, 24],
            ['Liver Function Test', 'Chemistry', 700, 24],
            ['X-Ray Chest', 'Imaging', 500, 4],
            ['ECG', 'Cardiology', 300, 2],
            ['Urine Routine', 'Microbiology', 250, 12],
        ] as [$n, $c, $p, $h]) {
            LabTest::firstOrCreate(['name'=>$n], ['category'=>$c,'price'=>$p,'duration_hours'=>$h,'is_active'=>true]);
        }

        // Medicines
        foreach ([
            ['Paracetamol 500mg', 'Painkiller', 'Cipla', 32.00, 500, 'strip'],
            ['Crocin Advance', 'Painkiller', 'GSK', 38.00, 300, 'strip'],
            ['Azithromycin 500mg', 'Antibiotic', 'Sun Pharma', 120.00, 200, 'strip'],
            ['Cetirizine 10mg', 'Anti-allergy', 'Cipla', 22.00, 400, 'strip'],
            ['Omeprazole 20mg', 'Antacid', 'Dr. Reddy', 45.00, 350, 'strip'],
            ['Metformin 500mg', 'Diabetes', 'Sun Pharma', 28.00, 300, 'strip'],
            ['Amlodipine 5mg', 'BP', 'Torrent', 36.00, 250, 'strip'],
            ['Vitamin D3 60K', 'Supplement', 'USV', 95.00, 150, 'strip'],
            ['Cough Syrup 100ml', 'Cold', 'Himalaya', 85.00, 100, 'bottle'],
            ['ORS Powder', 'Electrolyte', 'Reliance', 15.00, 500, 'strip'],
            ['Dolo 650', 'Painkiller', 'Micro Labs', 30.00, 600, 'strip'],
            ['Ibuprofen 400mg', 'Painkiller', 'Cipla', 28.00, 400, 'strip'],
        ] as [$n, $c, $m, $p, $s, $u]) {
            Medicine::firstOrCreate(['name'=>$n], [
                'category'=>$c,'manufacturer'=>$m,'price'=>$p,'stock'=>$s,'unit'=>$u,'is_active'=>true,
            ]);
        }

        // Blood inventory
        foreach (['A+'=>25,'A-'=>8,'B+'=>30,'B-'=>5,'O+'=>40,'O-'=>6,'AB+'=>12,'AB-'=>3] as $bg=>$u) {
            BloodInventory::firstOrCreate(['blood_group'=>$bg], ['units'=>$u]);
        }
    }
}

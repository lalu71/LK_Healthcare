<?php

use App\Http\Controllers\Api\AppointmentApiController;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\AvailabilityApiController;
use App\Http\Controllers\Api\BloodBankApiController;
use App\Http\Controllers\Api\DoctorApiController;
use App\Http\Controllers\Api\DoctorProfileApiController;
use App\Http\Controllers\Api\EmergencyApiController;
use App\Http\Controllers\Api\LabApiController;
use App\Http\Controllers\Api\MedicalRecordApiController;
use App\Http\Controllers\Api\NotificationApiController;
use App\Http\Controllers\Api\PasswordResetApiController;
use App\Http\Controllers\Api\PatientApiController;
use App\Http\Controllers\Api\PharmacyApiController;
use App\Http\Controllers\Api\PrescriptionApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes (v1) — used by the Flutter mobile app
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    // Public auth
    Route::post('/login', [AuthApiController::class, 'login']);
    Route::post('/register', [AuthApiController::class, 'register']);

    // Forgot password — emails reset link (web reset form completes the flow)
    Route::post('/forgot-password', [PasswordResetApiController::class, 'sendLink']);

    // Public contact form (no auth)
    Route::post('/contact', [\App\Http\Controllers\Api\ContactApiController::class, 'store']);

    // Authenticated
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', [AuthApiController::class, 'me']);
        Route::post('/logout', [AuthApiController::class, 'logout']);

        // Patient profile
        Route::get('/patient/me', [PatientApiController::class, 'me']);
        Route::patch('/patient/me', [PatientApiController::class, 'update']);

        // Doctor profile (own)
        Route::get('/doctor/me', [DoctorProfileApiController::class, 'me']);
        Route::patch('/doctor/me', [DoctorProfileApiController::class, 'update']);
        // One-shot update for mobile (multipart-friendly; updates user + doctor together)
        Route::post('/doctor/profile/update', [DoctorProfileApiController::class, 'updateProfile']);

        // Doctors directory (browseable)
        Route::get('/doctors', [DoctorApiController::class, 'index']);
        Route::get('/doctors/{doctor}', [DoctorApiController::class, 'show']);
        Route::get('/doctors/{doctor}/slots', [AvailabilityApiController::class, 'slots']);
        Route::get('/specializations', [DoctorApiController::class, 'specializations']);

        // Doctor availability (own)
        Route::get('/doctor/availability', [AvailabilityApiController::class, 'mine']);
        Route::post('/doctor/availability', [AvailabilityApiController::class, 'store']);
        Route::patch('/doctor/availability/{availability}', [AvailabilityApiController::class, 'update']);
        Route::delete('/doctor/availability/{availability}', [AvailabilityApiController::class, 'destroy']);

        // Patient appointments
        Route::get('/my/appointments', [AppointmentApiController::class, 'mine']);
        Route::post('/appointments', [AppointmentApiController::class, 'store']);
        Route::patch('/appointments/{appointment}/cancel', [AppointmentApiController::class, 'cancel']);

        // Doctor appointments
        Route::get('/doctor/all-appointments',[AppointmentApiController::class, 'allAppointments']);
        Route::get('/doctor/appointments', [AppointmentApiController::class, 'forDoctor']);
        Route::patch('/appointments/{appointment}/status', [AppointmentApiController::class, 'updateStatus']);

        // Prescriptions
        Route::get('/my/prescriptions', [PrescriptionApiController::class, 'mine']);
        Route::get('/doctor/prescriptions', [PrescriptionApiController::class, 'issued']);
        Route::get('/prescriptions/{prescription}', [PrescriptionApiController::class, 'show']);
        Route::post('/prescriptions', [PrescriptionApiController::class, 'store']);

        // Lab
        Route::get('/lab/tests', [LabApiController::class, 'tests']);
        Route::get('/my/lab-bookings', [LabApiController::class, 'myBookings']);
        Route::post('/lab/book', [LabApiController::class, 'book']);

        // Pharmacy
        Route::get('/pharmacy/medicines', [PharmacyApiController::class, 'medicines']);
        Route::get('/my/pharmacy-orders', [PharmacyApiController::class, 'myOrders']);
        Route::post('/pharmacy/orders', [PharmacyApiController::class, 'placeOrder']);

        // Medical records
        Route::get('/my/medical-records', [MedicalRecordApiController::class, 'index']);
        Route::post('/medical-records', [MedicalRecordApiController::class, 'store']);
        Route::delete('/medical-records/{medicalRecord}', [MedicalRecordApiController::class, 'destroy']);

        // Blood bank
        Route::get('/blood/inventory', [BloodBankApiController::class, 'inventory']);
        Route::get('/blood/donors', [BloodBankApiController::class, 'donors']);
        Route::get('/my/blood-requests', [BloodBankApiController::class, 'myRequests']);
        Route::post('/blood/requests', [BloodBankApiController::class, 'storeRequest']);
        Route::post('/blood/donor', [BloodBankApiController::class, 'registerDonor']);

        // Emergency SOS
        Route::get('/my/emergency-requests', [EmergencyApiController::class, 'mine']);
        Route::post('/emergency', [EmergencyApiController::class, 'store']);

        // Notifications
        Route::get('/notifications', [NotificationApiController::class, 'index']);
        Route::post('/notifications/read-all', [NotificationApiController::class, 'readAll']);
    });
});

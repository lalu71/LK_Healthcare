<?php

use App\Http\Controllers\Admin\AdminPublicController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\BloodBankController;
use App\Http\Controllers\DoctorAppointmentController;
use App\Http\Controllers\DoctorAvailabilityController;
use App\Http\Controllers\DoctorPrescriptionController;
use App\Http\Controllers\DoctorProfileController;
use App\Http\Controllers\EmergencyController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LabController;
use App\Http\Controllers\MedicalRecordController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PatientPrescriptionController;
use App\Http\Controllers\PatientProfileController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PharmacyController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicController;
use Illuminate\Support\Facades\Route;

/* ───────────── Public ───────────── */
Route::get('/', [PublicController::class, 'home']);
Route::get('/about', [PublicController::class, 'about'])->name('public.about');
Route::get('/services', [PublicController::class, 'services'])->name('public.services');
Route::get('/doctors', [PublicController::class, 'doctors'])->name('public.doctors');
Route::get('/contact', [PublicController::class, 'contact'])->name('public.contact');
Route::post('/contact', [PublicController::class, 'contactStore'])->name('public.contact.store');
Route::get('/language/{lang}', [PublicController::class, 'switchLang'])->name('lang.switch');

/* Emergency — public (guest & logged-in users both can request) */
Route::get('/emergency', [EmergencyController::class, 'create'])->name('emergency.create');
Route::post('/emergency', [EmergencyController::class, 'store'])->name('emergency.store');

/* ───────────── Dashboard ───────────── */
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');

    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read_all');

    /* Site review — any logged-in user (except admin) */
    Route::get('/reviews', [\App\Http\Controllers\ReviewController::class, 'create'])->name('reviews.create');
    Route::post('/reviews', [\App\Http\Controllers\ReviewController::class, 'store'])->name('reviews.store');

    /* Profile (account) */
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /* Blood bank — everyone */
    Route::get('/blood', [BloodBankController::class, 'index'])->name('blood.index');
    Route::post('/blood/request', [BloodBankController::class, 'request'])->name('blood.request');
    Route::post('/blood/donor', [BloodBankController::class, 'registerDonor'])->name('blood.donor');

    /* ───────── Patient ───────── */
    Route::prefix('patient')->name('patient.')->group(function () {
        Route::get('/profile', [PatientProfileController::class, 'edit'])->name('profile.edit');
        Route::post('/profile', [PatientProfileController::class, 'update'])->name('profile.update');

        Route::get('/book', [AppointmentController::class, 'create'])->name('book');
        Route::post('/book', [AppointmentController::class, 'store'])->name('book.store');
        Route::get('/doctors/{doctor}/slots', [AppointmentController::class, 'slots'])->name('doctor.slots');

        Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments.index');
        Route::delete('/appointments/{appointment}', [AppointmentController::class, 'destroy'])->name('appointments.destroy');

        Route::get('/prescriptions', [PatientPrescriptionController::class, 'index'])->name('prescriptions.index');
        Route::get('/prescriptions/{prescription}', [PatientPrescriptionController::class, 'show'])->name('prescriptions.show');
        Route::get('/prescriptions/{prescription}/pdf', [PatientPrescriptionController::class, 'pdf'])->name('prescriptions.pdf');

        Route::get('/records', [MedicalRecordController::class, 'index'])->name('records.index');
        Route::post('/records', [MedicalRecordController::class, 'store'])->name('records.store');
        Route::get('/records/{record}/download', [MedicalRecordController::class, 'download'])->name('records.download');
        Route::delete('/records/{record}', [MedicalRecordController::class, 'destroy'])->name('records.destroy');

        Route::get('/lab', [LabController::class, 'index'])->name('lab.index');
        Route::post('/lab/book', [LabController::class, 'book'])->name('lab.book');

        Route::get('/pharmacy', [PharmacyController::class, 'index'])->name('pharmacy.index');
        Route::post('/pharmacy/order', [PharmacyController::class, 'order'])->name('pharmacy.order');
    });

    /* ───────── Doctor ───────── */
    Route::prefix('doctor')->name('doctor.')->group(function () {
        Route::get('/profile', [DoctorProfileController::class, 'edit'])->name('profile.edit');
        Route::post('/profile', [DoctorProfileController::class, 'update'])->name('profile.update');

        Route::get('/appointments', [DoctorAppointmentController::class, 'index'])->name('appointments.index');
        Route::get('/appointments/{appointment}', [DoctorAppointmentController::class, 'show'])->name('appointments.show');
        Route::patch('/appointments/{appointment}/status', [AppointmentController::class, 'updateStatus'])->name('appointments.update-status');

        Route::get('/availability', [DoctorAvailabilityController::class, 'index'])->name('availability.index');
        Route::post('/availability', [DoctorAvailabilityController::class, 'store'])->name('availability.store');
        Route::patch('/availability/{availability}', [DoctorAvailabilityController::class, 'update'])->name('availability.update');
        Route::delete('/availability/{availability}', [DoctorAvailabilityController::class, 'destroy'])->name('availability.destroy');

        Route::get('/prescriptions', [DoctorPrescriptionController::class, 'index'])->name('prescriptions.index');
        Route::get('/prescriptions/create', [DoctorPrescriptionController::class, 'create'])->name('prescriptions.create');
        Route::post('/prescriptions', [DoctorPrescriptionController::class, 'store'])->name('prescriptions.store');
        Route::get('/prescriptions/{prescription}', [DoctorPrescriptionController::class, 'show'])->name('prescriptions.show');
    });

    /* ───────── Payments ───────── */
    Route::get('/pay/{type}/{id}', [PaymentController::class, 'show'])->name('payment.show');
    Route::post('/pay/{type}/{id}', [PaymentController::class, 'pay'])->name('payment.pay');
    Route::get('/receipt/{payment}', [PaymentController::class, 'receipt'])->name('payment.receipt');

    /* ───────── Pharmacist ───────── */
    Route::prefix('pharmacist')->name('pharmacist.')->middleware('role:pharmacist')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Pharmacist\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/prescriptions', [\App\Http\Controllers\Pharmacist\PrescriptionController::class, 'index'])->name('prescriptions.index');
        Route::get('/prescriptions/{prescription}', [\App\Http\Controllers\Pharmacist\PrescriptionController::class, 'show'])->name('prescriptions.show');
        Route::get('/prescriptions/{prescription}/pdf', [\App\Http\Controllers\Pharmacist\PrescriptionController::class, 'pdf'])->name('prescriptions.pdf');
        Route::get('/prescriptions/{prescription}/print', [\App\Http\Controllers\Pharmacist\PrescriptionController::class, 'print'])->name('prescriptions.print');
        Route::patch('/prescriptions/{prescription}/status', [\App\Http\Controllers\Pharmacist\PrescriptionController::class, 'updateStatus'])->name('prescriptions.update-status');
        Route::post('/prescriptions/{prescription}/payment', [\App\Http\Controllers\Pharmacist\PrescriptionController::class, 'recordPayment'])->name('prescriptions.payment');
        Route::post('/prescriptions/{prescription}/payment-link', [\App\Http\Controllers\Pharmacist\PrescriptionController::class, 'createPaymentLink'])->name('prescriptions.payment-link');
        Route::get('/prescriptions/{prescription}/payment-link/{linkId}/check', [\App\Http\Controllers\Pharmacist\PrescriptionController::class, 'checkPaymentLink'])->name('prescriptions.payment-link.check');
        Route::get('/inventory', [\App\Http\Controllers\Pharmacist\InventoryController::class, 'index'])->name('inventory.index');
        Route::patch('/inventory/{medicine}', [\App\Http\Controllers\Pharmacist\InventoryController::class, 'updateStock'])->name('inventory.update-stock');
    });

    /* ───────── Admin ───────── */
    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        Route::post('/site/toggle-shutdown', [\App\Http\Controllers\Admin\SiteController::class, 'toggleShutdown'])->name('site.toggle-shutdown');

        Route::get('/patients', [\App\Http\Controllers\Admin\PatientController::class, 'index'])->name('patients.index');
        Route::get('/patients/{patient}', [\App\Http\Controllers\Admin\PatientController::class, 'show'])->name('patients.show');
        Route::patch('/patients/{patient}/toggle-active', [\App\Http\Controllers\Admin\PatientController::class, 'toggleActive'])->name('patients.toggle-active');

        Route::get('/doctors', [\App\Http\Controllers\Admin\DoctorController::class, 'index'])->name('doctors.index');
        Route::get('/doctors/create', [\App\Http\Controllers\Admin\DoctorController::class, 'create'])->name('doctors.create');
        Route::post('/doctors', [\App\Http\Controllers\Admin\DoctorController::class, 'store'])->name('doctors.store');
        Route::patch('/doctors/{doctor}/toggle', [\App\Http\Controllers\Admin\DoctorController::class, 'toggle'])->name('doctors.toggle');
        Route::patch('/doctors/{doctor}/toggle-active', [\App\Http\Controllers\Admin\DoctorController::class, 'toggleActive'])->name('doctors.toggle-active');

        Route::get('/appointments', [\App\Http\Controllers\Admin\AppointmentController::class, 'index'])->name('appointments.index');

        Route::get('/lab', [\App\Http\Controllers\Admin\LabController::class, 'index'])->name('lab.index');
        Route::post('/lab/tests', [\App\Http\Controllers\Admin\LabController::class, 'storeTest'])->name('lab.tests.store');
        Route::patch('/lab/tests/{labTest}/toggle', [\App\Http\Controllers\Admin\LabController::class, 'toggleTest'])->name('lab.tests.toggle');
        Route::post('/lab/bookings/{booking}/result', [\App\Http\Controllers\Admin\LabController::class, 'uploadResult'])->name('lab.bookings.result');

        Route::get('/pharmacy', [\App\Http\Controllers\Admin\PharmacyController::class, 'index'])->name('pharmacy.index');
        Route::post('/pharmacy/medicines', [\App\Http\Controllers\Admin\PharmacyController::class, 'store'])->name('pharmacy.store');
        Route::patch('/pharmacy/medicines/{medicine}', [\App\Http\Controllers\Admin\PharmacyController::class, 'update'])->name('pharmacy.update');
        Route::delete('/pharmacy/medicines/{medicine}', [\App\Http\Controllers\Admin\PharmacyController::class, 'destroy'])->name('pharmacy.destroy');
        Route::patch('/pharmacy/orders/{order}', [\App\Http\Controllers\Admin\PharmacyController::class, 'updateOrder'])->name('pharmacy.orders.update');
        Route::post('/pharmacy/pharmacists', [\App\Http\Controllers\Admin\PharmacyController::class, 'storePharmacist'])->name('pharmacy.pharmacists.store');
        Route::patch('/pharmacy/pharmacists/{user}/toggle', [\App\Http\Controllers\Admin\PharmacyController::class, 'togglePharmacist'])->name('pharmacy.pharmacists.toggle');

        Route::get('/blood', [\App\Http\Controllers\Admin\BloodController::class, 'index'])->name('blood.index');
        Route::post('/blood/inventory', [\App\Http\Controllers\Admin\BloodController::class, 'updateInventory'])->name('blood.inventory.update');
        Route::patch('/blood/requests/{bloodRequest}', [\App\Http\Controllers\Admin\BloodController::class, 'updateRequest'])->name('blood.requests.update');

        Route::get('/emergency', [\App\Http\Controllers\Admin\EmergencyController::class, 'index'])->name('emergency.index');
        Route::patch('/emergency/{emergencyRequest}', [\App\Http\Controllers\Admin\EmergencyController::class, 'update'])->name('emergency.update');

        Route::get('/reviews', [\App\Http\Controllers\Admin\ReviewController::class, 'index'])->name('reviews.index');
        Route::patch('/reviews/{review}/toggle', [\App\Http\Controllers\Admin\ReviewController::class, 'toggle'])->name('reviews.toggle');
        Route::delete('/reviews/{review}', [\App\Http\Controllers\Admin\ReviewController::class, 'destroy'])->name('reviews.destroy');

        Route::get('/contacts', [\App\Http\Controllers\Admin\ContactController::class, 'index'])->name('contacts.index');
        Route::post('/contacts/{message}/reply', [\App\Http\Controllers\Admin\ContactController::class, 'reply'])->name('contacts.reply');
        Route::patch('/contacts/{message}/toggle', [\App\Http\Controllers\Admin\ContactController::class, 'toggle'])->name('contacts.toggle');
        Route::delete('/contacts/delete-all', [\App\Http\Controllers\Admin\ContactController::class, 'destroyAll'])->name('contacts.destroyAll');
        Route::delete('/contacts/{message}', [\App\Http\Controllers\Admin\ContactController::class, 'destroy'])->name('contacts.destroy');

        // Admin Public Path
        Route::get('/services',[AdminPublicController::class, 'index'])->name('services.index');
        Route::post('/services/store',[AdminPublicController::class, 'store'])->name('services.store');
        Route::post('/services/update/{id}',[AdminPublicController::class, 'update'])->name('services.update');
        Route::post('/services/delete/{id}',[AdminPublicController::class, 'destroy'])->name('services.delete');

        // Admin Specilists
        Route::get('/specilists',[\App\Http\Controllers\Admin\SpecilistController::class, 'index'])->name('specilists.index');
        Route::post('/specilists/store',[\App\Http\Controllers\Admin\SpecilistController::class, 'store'])->name('specilists.store');
        Route::post('/specilists/update/{id}',[\App\Http\Controllers\Admin\SpecilistController::class, 'update'])->name('specilists.update');
        Route::post('/specilists/delete/{id}',[\App\Http\Controllers\Admin\SpecilistController::class, 'destroy'])->name('specilists.delete');

        // Update site content
        Route::get('/site-content', [\App\Http\Controllers\Admin\AdminPublicController::class, 'site_content'])->name('lk_site_content');
        Route::put('/update_site_content',[\App\Http\Controllers\Admin\AdminPublicController::class, 'site_content_update'])->name('update_site_content');
    });

    /* Dev: role switcher */
    Route::get('/switch-role/{role}', function (string $role) {
        $roleObj = \Spatie\Permission\Models\Role::firstOrCreate(['name' => $role]);
        auth()->user()->syncRoles([$role]);
        return redirect()->route('dashboard');
    })->name('switch-role');
});

require __DIR__.'/auth.php';

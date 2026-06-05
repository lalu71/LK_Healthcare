<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Contracts\Http\Kernel;

class SmokeTest extends Command
{
    protected $signature = 'smoke:test';
    protected $description = 'Smoke test authenticated routes';

    public function handle(): int
    {
        $roles = [
            'patient' => 'patient@lkhealthcare.in',
            'doctor' => 'doctor.anjali@lkhealthcare.in',
            'admin' => 'admin@lkhealthcare.in',
        ];
        $routes = [
            '/dashboard', '/patient/book', '/patient/appointments',
            '/patient/prescriptions', '/patient/records', '/patient/lab', '/patient/pharmacy',
            '/doctor/appointments', '/doctor/availability', '/doctor/prescriptions',
            '/admin/patients', '/admin/doctors', '/admin/appointments',
            '/admin/lab', '/admin/pharmacy', '/admin/blood', '/admin/emergency', '/admin/contacts',
            '/blood', '/emergency',
        ];

        $fails = 0;
        foreach ($roles as $role => $email) {
            $this->info("=== {$role} ===");
            $user = User::where('email', $email)->first();
            if (!$user) continue;
            foreach ($routes as $u) {
                try {
                    auth()->login($user);
                    $req = \Illuminate\Http\Request::create($u, 'GET');
                    $req->setLaravelSession(app('session.store'));
                    ob_start();
                    $res = app(Kernel::class)->handle($req);
                    ob_end_clean();
                    $status = $res->getStatusCode();
                    $mark = $status === 200 ? '✓' : ($status === 302 ? '↪' : '✗');
                    if (!in_array($status, [200, 302])) $fails++;
                    fwrite(STDERR, sprintf("  %s %-28s %d\n", $mark, $u, $status));
                } catch (\Throwable $e) {
                    @ob_end_clean();
                    $fails++;
                    fwrite(STDERR, "  ✗ $u → ".substr($e->getMessage(), 0, 150)."\n");
                }
            }
        }
        $this->info("\nFailures: $fails");
        return $fails > 0 ? 1 : 0;
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Specialization;

class SpecializationSeeder extends Seeder
{
    public function run(): void
    {
        $specializations = [
            ['name' => 'General Physician', 'description' => 'Treats acute and chronic illnesses and provides preventive care.'],
            ['name' => 'Cardiologist', 'description' => 'Expert in diagnosing and treating diseases of the cardiovascular system.'],
            ['name' => 'Dermatologist', 'description' => 'Specializes in treating conditions related to the skin, hair, and nails.'],
            ['name' => 'Neurologist', 'description' => 'Specialized in treating disorders that affect the brain, spinal cord, and nerves.'],
            ['name' => 'Orthopedist', 'description' => 'Focuses on injuries and diseases of your bodys musculoskeletal system.'],
            ['name' => 'Pediatrician', 'description' => 'Medical doctor who manages the physical, behavioral, and mental care for children.'],
            ['name' => 'Dentist', 'description' => 'Treats problems related to teeth and gums.'],
        ];

        foreach ($specializations as $spec) {
            Specialization::firstOrCreate(['name' => $spec['name']], $spec);
        }
    }
}

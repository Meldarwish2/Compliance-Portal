<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PreDefinedProjects extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('email', 'admin@example.com')->first();

        // Create the Sama project and assign it to the admin
        $samaProject = Project::create([
            'name' => 'Sama',
            'description' => 'Pre-defined project for Sama',
            'status' => 'pending', 
            'type' => 'rating', 
        ]);
        $samaProject->users()->attach($admin);

        // Create the CSF project and assign it to the admin
        $csfProject = Project::create([
            'name' => 'CSF',
            'description' => 'Pre-defined project for CSF',
            'status' => 'pending', 
            'type' => 'compliance', 
        ]);
        $csfProject->users()->attach($admin);
    }
}

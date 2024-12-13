<?php

namespace Database\Seeders;

use App\Imports\StatementsImport;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

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
            'name' => 'SAMA CSF',
            'description' => 'Pre-defined project for SAMA CSF',
            'status' => 'pending', 
            'type' => 'rating', 
        ]);
        Excel::import(new StatementsImport($samaProject->id), public_path('statement_files/SAMA-CSF.xlsx'));
        $samaProject->users()->attach($admin);

        // Create the NCA project and assign it to the admin
        $NcaProject = Project::create([
            'name' => 'NCA',
            'description' => 'Pre-defined project for NCA',
            'status' => 'pending', 
            'type' => 'compliance', 
        ]);
        Excel::import(new StatementsImport($samaProject->id), public_path('statement_files/NCA.xlsx'));
        $NcaProject->users()->attach($admin);
    }
}

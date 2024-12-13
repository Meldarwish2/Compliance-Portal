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

        // Create the ISO project and assign it to the admin
        $ISOProject = Project::create([
            'name' => 'ISO-27001',
            'description' => 'Pre-defined project for ISO',
            'status' => 'pending', 
            'type' => 'accept_reject', 
        ]);
        Excel::import(new StatementsImport($ISOProject->id), public_path('statement_files/ISO-27001.xlsx'));
        $ISOProject->users()->attach($admin);

        // Create the PCI project and assign it to the admin
        $PCIProject = Project::create([
            'name' => 'PCI-DSS-v4.0',
            'description' => 'Pre-defined project for PCI-DSS-v4.0',
            'status' => 'pending', 
            'type' => 'accept_reject', 
        ]);
        Excel::import(new StatementsImport($PCIProject->id), public_path('statement_files/PCI-DSS-v4.0.xlsx'));
        $PCIProject->users()->attach($admin);
    }
}

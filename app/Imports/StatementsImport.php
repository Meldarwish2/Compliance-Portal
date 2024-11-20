<?php
namespace App\Imports;

use App\Models\Statement;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithStartRow;

class StatementsImport implements ToModel, WithStartRow
{
    protected $projectId;

    public function __construct($projectId)
    {
        $this->projectId = $projectId;
    }

    public function model(array $row)
    {
        // Assuming the first column (A) contains the statement content
        if (isset($row[0])) {
            return new Statement([
                'project_id' => $this->projectId,
                'content' => $row[0],
            ]);
        }
    }

    public function startRow(): int
    {
        return 1; // Start reading from the first row
    }
}
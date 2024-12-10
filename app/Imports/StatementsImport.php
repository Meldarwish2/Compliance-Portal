<?php
namespace App\Imports;

use App\Models\Statement;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StatementsImport implements ToModel, WithStartRow, WithHeadingRow
{
    protected $projectId;

    public function __construct($projectId)
    {
        $this->projectId = $projectId;
    }

    /**
     * Map each row to a Statement model dynamically based on CSV headers.
     */
    public function model(array $row)
    {
        // Ensure the row is not empty
        if (!empty($row)) {
            // Encode the entire row as JSON
            $content = json_encode($row);

            return new Statement([
                'project_id' => $this->projectId,
                'content' => $content,
                'status' => 'pending', // Default status
            ]);
        }
    }

    /**
     * Start reading from the row after the header.
     */
    public function startRow(): int
    {
        return 2; // Adjust if necessary
    }
}

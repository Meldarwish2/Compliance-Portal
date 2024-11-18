<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evidence extends Model
{
    protected $table='evidences';
    protected $fillable = ['file_name', 'file_path', 'status', 'project_id', 'uploaded_by'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    // An evidence belongs to a user (uploaded by)
    public function user()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}

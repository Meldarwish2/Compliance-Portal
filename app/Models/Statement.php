<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Statement extends Model
{
    protected $fillable = ['content', 'status', 'project_id'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}

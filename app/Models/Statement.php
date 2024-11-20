<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Statement extends Model
{
    protected $fillable = ['content', 'status', 'project_id','creator_role','created_by'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
    public function evidences()
    {
        return $this->hasMany(Evidence::class);
    }
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    
}

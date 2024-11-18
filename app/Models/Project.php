<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = ['name', 'description', 'status'];

    public function evidences()
    {
        return $this->hasMany(Evidence::class);
    }

    public function statements()
    {
        return $this->hasMany(Statement::class);
    }
    public function users()
{
    return $this->belongsToMany(User::class);  // Assuming a many-to-many relationship
}

}

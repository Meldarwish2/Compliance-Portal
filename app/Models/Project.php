<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Project extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    protected $fillable = ['name', 'description', 'status','type','parent_project_id'];

    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_PENDING = 'pending';
    const STATUS = [
        self::STATUS_APPROVED => 'Approved',
        self::STATUS_REJECTED => 'Rejected',
        self::STATUS_PENDING  => 'Pending',
    ];


    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function statements()
    {
        return $this->hasMany(Statement::class);
    }

    public function evidences()
    {
        return $this->hasMany(Evidence::class);
    }
    public function usersWithRoles()
    {
        return $this->belongsToMany(User::class)->withPivot('role'); // Ensure the 'role' field exists in the pivot table
    }

    public function parent()
    {
        return $this->belongsTo(Project::class, 'parent_project_id');
    }

    public function children()
    {
        return $this->hasMany(Project::class, 'parent_project_id');
    }
    
}

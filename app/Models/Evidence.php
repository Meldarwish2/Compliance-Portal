<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;


class Evidence extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    protected $table='evidences';
    protected $fillable = ['file_name', 'file_path', 'status', 'project_id','statement_id', 'uploaded_by'];

    
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_PENDING = 'pending';
    const STATUS = [
        self::STATUS_APPROVED => 'Approved',
        self::STATUS_REJECTED => 'Rejected',
        self::STATUS_PENDING  => 'Pending',
    ];
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    // An evidence belongs to a user (uploaded by)
    public function user()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
    public function statement()
    {
        return $this->belongsTo(Statement::class);
    }
}

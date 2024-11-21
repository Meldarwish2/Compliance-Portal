<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Audit extends Model
{
    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    private $_auditableTypesName = [
        'App\Models\User'            => 'User',
        'App\Models\Permission'      => 'Permission',
        'App\Models\Role'            => 'Role',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }

    public function audited()
    {
        return $this->belongsTo($this->auditable_type, 'auditable_id');
    }


    public function getAuditedDescriptionAttribute()
    {
        return $this->_auditableTypesName[$this->auditable_type] . " #{$this->auditable_id}";
    }
}

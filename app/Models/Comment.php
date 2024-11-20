<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;


class Comment extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    protected $fillable = ['content', 'statement_id', 'user_id', 'role'];

    public function statement()
    {
        return $this->belongsTo(Statement::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

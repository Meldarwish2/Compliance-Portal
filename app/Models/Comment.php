<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
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

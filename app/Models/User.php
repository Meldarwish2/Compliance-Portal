<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Laravel\Fortify\TwoFactorAuthenticatable;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory, Notifiable, HasRoles, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
    public function projects()
    {
        return $this->belongsToMany(Project::class);
    }
    public function evidences()
    {
        return $this->hasMany(Evidence::class);
    }
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function sendTwoFactorCode()
    {
        // Generate a random 6-digit code
        $code = random_int(100000, 999999);

        // Save the code in the `two_factor_secret` column
       
        $this->two_factor_secret = bcrypt($code);
        $this->save();
        // Send the code to the user's email
        Mail::to($this->email)->send(new \App\Mail\TwoFactorCodeMail($code));
    }

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $table = 'personal_info';
    protected $fillable = [
        'first_name',
        'last_name',
        'gender',
        'dob',
        'marital_status',
        'profession',
        'address',
        'phone',
        'email',
        'summary',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $guarded = [
        'id',
    ];

}

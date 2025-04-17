<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Education extends Model
{
    protected $table = 'education';
    protected $fillable = [
        'institute',
        'city',
        'degree',
        'field_of_study',
        'start_date',
        'end_date',
        'result',
        'sort_order',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $guarded = [
        'id',
    ];

}

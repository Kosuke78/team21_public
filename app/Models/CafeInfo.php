<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CafeInfo extends Model
{
    protected $table = 'cafe_infos';
    protected $dates =  ['created_at', 'updated_at'];
    protected $fillable = ['place_id', 'user_id', 'outlet_count', 'comment'];
}

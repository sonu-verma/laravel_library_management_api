<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserBook extends Model
{
    use HasFactory;
    protected $table = 'user_book';
    protected $fillable  = ['u_id','b_id','action_type'];
}

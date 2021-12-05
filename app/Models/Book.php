<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;
    protected $fillable = ['b_id','book_name','author','cover_image'];
    protected $primaryKey = 'b_id';

    public function user(){
        return $this->belongsToMany('App\Models\User', 'user_book', 'b_id', 'u_id')->withPivot(['action_type','created_at','updated_at']);;
    }
}

<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class LaravelUserModel extends Model
{
    protected $table = 'laravel_user';
    public $primaryKey ="user_id";
    public $timestamps = false;
//    protected $fillable=['name','pwd'];
    public $guarded = [];
}

<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Support\Str;

class User extends Model
{
    use HasFactory;
    //use Authenticatable, Authorizable, HasFactory;
    protected $keytype="string";

    public $incrementing = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name', 'email',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var string[]
     */
    protected $hidden = [
        'secret',
    ];

    protected static function boot() {
        parent::boot();

        static::creating(function($model){
            if(empty($model->id)){
                $model->id=(string) Str::uuid();
            }
        });
    }
}

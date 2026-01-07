<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class RefreshToken extends Model {
    public $incrementing = false;

    protected $keytype = 'string';

    protected $fillable =['id', 'user_id', 'token_hash', 'expires'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    protected static function boot(){
        parent::boot();

        static::creating(function($model) {
            if(empty($model->id)){
                $model->id=(string) Str::uuid();
            }
        });
    }
}
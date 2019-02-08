<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Moloquent;

class UserContact extends Moloquent
{
    protected $fillable = [
        'user_id', 'address', 'country',
    ];
    public function user() {
        return $this->belongsTo('App\User');
    }
}

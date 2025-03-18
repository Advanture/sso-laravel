<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OAuthUser extends Model
{
    const PROVIDER_GOOGLE = 'google';

    protected $fillable = [
        'user_id', 'provider', 'provider_id',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScanQrCode extends Model
{
    protected $fillable = ['value', 'user_id'];
}

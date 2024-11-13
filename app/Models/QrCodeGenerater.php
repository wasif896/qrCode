<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QrCodeGenerater extends Model
{
    protected $fillable = [
        'user_id',
        'linkName',
        'type',
        'value',
        'fgColor',
        'bgColor',
        'eyeColor',
        'logoImage',
        'eyeFrameShape',
        'eyeShape',
        'isDownload',
        'qrName',
    ];
}

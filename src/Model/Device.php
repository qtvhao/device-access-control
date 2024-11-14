<?php
namespace Qtvhao\DeviceAccessControl\Model;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    
    protected $fillable = [
        'user_id',
        'device_uuid',
        'device_type',
        'device_name'
    ];
}

<?php
namespace Qtvhao\DeviceAccessControl\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Device
 * @package Qtvhao\DeviceAccessControl\Model
 * @property int $id
 * @property int $user_id
 * @property string $device_uuid
 * @property string $device_type
 * @property string $device_name
 * @property \DateTime $last_accessed
 */
class Device extends Model
{
    
    protected $fillable = [
        'user_id',
        'device_uuid',
        'device_type',
        'device_name'
    ];
}

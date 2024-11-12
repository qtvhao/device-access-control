<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Qtvhao\DeviceAccessControl\Core\Enums\DeviceEnums;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->string('device_id')->unique(); // Unique identifier for each device
            $table->enum('device_type', [DeviceEnums::DEVICE_TYPE_WEB, DeviceEnums::DEVICE_TYPE_TABLET, DeviceEnums::DEVICE_TYPE_MOBILE]);
            $table->timestamps();

            $table->integer('user_id')->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('devices');
    }
};

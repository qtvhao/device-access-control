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
            $table->string('device_id')->index();
            $table->string('device_name')->nullable();
            $table->enum('device_type', [DeviceEnums::DEVICE_TYPE_WEB_BROWSER, DeviceEnums::DEVICE_TYPE_TABLET, DeviceEnums::DEVICE_TYPE_MOBILE]);
            $table->timestamp('last_accessed')->useCurrent();
            $table->timestamps();

            $table->integer('user_id')->unsigned();
            $table->unique(['user_id', 'device_type']);
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

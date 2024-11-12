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
            $table->string('id')->primary();
            $table->string('device_id')->unique(); // Unique identifier for each device
            $table->enum('device_type', [DeviceEnums::DEVICE_TYPE_WEB, DeviceEnums::DEVICE_TYPE_TABLET, DeviceEnums::DEVICE_TYPE_MOBILE]);
            $table->timestamps();

            // Foreign key to the users table
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
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

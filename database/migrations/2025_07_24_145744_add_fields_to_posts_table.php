<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->boolean('is_paid')->default(false)->after('link_preview_json')->nullable();
            $table->string('zip_code')->nullable()->after('is_paid');
            $table->string('country')->nullable()->after('zip_code');
            $table->string('state')->nullable()->after('country');
            $table->string('city')->nullable()->after('state');
            $table->decimal('latitude', 10, 7)->nullable()->after('city');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            // ✅ Add cta and external_link
            $table->string('cta')->nullable()->after('longitude');
            $table->string('external_link')->nullable()->after('cta');
        });
    }

    public function down()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn(['is_paid', 'zip_code', 'country', 'state', 'city']);
        });
    }
};

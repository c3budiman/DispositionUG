<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SettingSitus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
     public function up()
     {
         Schema::create('setting_situses', function (Blueprint $table) {
             $table->increments('id');
             $table->string('namaSitus');
             $table->string('slogan');
             $table->string('favicon');
             $table->string('logo');
             $table->string('alamatSitus');
             $table->timestamps();
         });
         DB::table('setting_situses')->insert([
             ['namaSitus' => 'UG - SISTEM MANAJEMEN SURAT', 'slogan' => 'Kemudahan mengatur surat masuk, kini bisa dilakukan secara daring melalui segala jenis perangkat.' , 'favicon' => '/images/logo.png', 'logo' => '/images/logo.png', 'alamatSitus' => 'localhost:8000'],
         ]);
     }

     public function down()
     {
         Schema::dropIfExists('setting_situses');
     }
}

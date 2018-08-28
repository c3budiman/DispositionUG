<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSuratMasuksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('surat_masuks', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('author')->nullable();
            $table->string('instansi_pengirim');
            $table->string('email');
            $table->string('perihal');
            $table->text('deskripsi');
            $table->text('lokasi');
            $table->integer('jumlah_file');
            $table->timestamp('tgl_disposisi')->nullable();
            $table->timestamps();
        });

        Schema::table('surat_masuks', function(Blueprint $kolom) {
          $kolom->foreign('author')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('surat_masuks');
    }
}

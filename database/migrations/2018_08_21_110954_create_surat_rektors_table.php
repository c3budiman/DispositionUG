<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSuratRektorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('surat_rektors', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('author')->nullable();
            $table->string('nomor');
            $table->string('tujuan');
            $table->string('email');
            $table->string('perihal');
            $table->text('deskripsi');
            $table->text('lokasi');
            $table->integer('jumlah_file');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('surat_rektors');
    }
}

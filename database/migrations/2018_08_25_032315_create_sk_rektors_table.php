<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSkRektorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sk_rektors', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('author')->nullable();
            $table->string('nomor_sk');
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
        Schema::drop('sk_rektors');
    }
}

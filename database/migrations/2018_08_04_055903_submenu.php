<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Submenu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('submenu', function (Blueprint $table) {
        $table->increments('id');
        $table->unsignedInteger('kepunyaan')->nullable();
        $table->string('nama');
        $table->string('link');
      });
      Schema::table('submenu', function(Blueprint $kolom){
      $kolom->foreign('kepunyaan')->references('id')->on('dashmenu')->onDelete('cascade')->onUpdate('cascade');
      });

      DB::table('submenu')->insert(
        array(
            'kepunyaan' => 4,
            'nama' => 'Menu Sidebar',
            'link' => '/sidebarsettings'
        )
      );
      DB::table('submenu')->insert(
        array(
            'kepunyaan' => 4,
            'nama' => 'Logo dan Favicon',
            'link' => '/logodanfavicon'
        )
      );
      DB::table('submenu')->insert(
        array(
            'kepunyaan' => 4,
            'nama' => 'Judul dan Slogan',
            'link' => '/juduldanslogan'
        )
      );
      DB::table('submenu')->insert(
        array(
            'kepunyaan' => 6,
            'nama' => 'Atur Surat',
            'link' => '/disposition'
        )
      );
      DB::table('submenu')->insert(
        array(
            'kepunyaan' => 6,
            'nama' => 'Tambah',
            'link' => '/disposition/add'
        )
      );
      DB::table('submenu')->insert(
        array(
            'kepunyaan' => 7,
            'nama' => 'Atur Surat',
            'link' => '/surat_rektor'
        )
      );
      DB::table('submenu')->insert(
        array(
            'kepunyaan' => 7,
            'nama' => 'Tambah',
            'link' => '/surat_rektor/add'
        )
      );
      DB::table('submenu')->insert(
        array(
            'kepunyaan' => 8,
            'nama' => 'Atur Surat',
            'link' => '/sk_rektor'
        )
      );
      DB::table('submenu')->insert(
        array(
            'kepunyaan' => 8,
            'nama' => 'Tambah',
            'link' => '/sk_rektor/add'
        )
      );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('submenu');
    }
}

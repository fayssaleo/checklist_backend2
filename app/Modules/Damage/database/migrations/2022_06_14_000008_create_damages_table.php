<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDamagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('damages', function (Blueprint $table) {

            $table->bigIncrements("id");

            $table->string("status")->default("on progress");
            $table->text("description")->nullable();

            $table->bigInteger('declaredBy_id')->unsigned()->nullable();
            $table->date("declaredAt")->nullable();
            $table->foreign('declaredBy_id')->references('id')->on('users')->onDelete('cascade');

            $table->bigInteger('confirmedBy_id')->unsigned()->nullable();
            $table->date("confirmedAt")->nullable();
            $table->foreign('confirmedBy_id')->references('id')->on('users')->onDelete('cascade');

            $table->bigInteger('closedBy_id')->unsigned()->nullable();
            $table->date("closedAt")->nullable();
            $table->foreign('closedBy_id')->references('id')->on('users')->onDelete('cascade');

            $table->bigInteger('revertedBy_id')->unsigned()->nullable();
            $table->date("revertedAt")->nullable();
            $table->integer("revertedTimes")->default(0);
            $table->foreign('revertedBy_id')->references('id')->on('users')->onDelete('cascade');

            $table->bigInteger('equipment_id')->unsigned()->nullable();
            $table->foreign('equipment_id')->references('id')->on('equipments')->onDelete('cascade');

            $table->bigInteger('damage_type_id')->unsigned()->nullable();
            $table->foreign('damage_type_id')->references('id')->on('damage_types')->onDelete('cascade');

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
        Schema::dropIfExists('damages');
    }
}

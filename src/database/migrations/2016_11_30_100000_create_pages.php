<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('req_role')->unsigned()->nullable();
            $table->foreign('req_role')->references('id')->on('roles');

            $table->integer('parent_id')->nullable();
            $table->foreign('parent_id')->references('id')->on('pages');

            $table->integer('website_id')->unsigned();
            $table->foreign('website_id')->references('id')->on('websites')->onDelete('cascade');

            // websites can have different layouts, like public webpages, private, etc.
            // So this is to allow us to specify on the page level which layout
            // it works with making the template build process a lot easier.
            $table->string('layout')->nullable();

            $table->string('slug');
            $table->string('url', 2083)->nullable(); // this is being derived automatically
            $table->string('title');
            $table->json('meta')->nullable();
            $table->boolean('dynamic_url')->default(false);

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['website_id', 'url']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('pages');
    }
}

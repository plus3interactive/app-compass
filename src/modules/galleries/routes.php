<?php
Route::group([
    // 'prefix' => 'cp',
    'namespace' => 'P3in\Controllers'
], function() {

    Route::resource('galleries', 'CpGalleriesController');

});
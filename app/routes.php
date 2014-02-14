<?php

Route::group(array('before' => 'PowergateAPIAuth'), function() {

    Route::resource('domains', 'DomainsController', array('only' => array('index', 'store', 'update', 'destroy')));
    Route::resource('records', 'RecordsController', array('only' => array('index', 'store', 'update', 'destroy')));
    Route::resource('supermasters', 'SupermastersController', array('only' => array('index', 'store', 'update', 'destroy')));

});

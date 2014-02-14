<?php

Route::group(array('before' => 'PowergateAPIAuth'), function() {

    Route::resource('domains', 'DomainsController', array('only' => array('show', 'index', 'store', 'update', 'destroy')));
    Route::resource('records', 'RecordsController', array('only' => array('show', 'index', 'store', 'update', 'destroy')));
    Route::resource('supermasters', 'SupermastersController', array('only' => array('show', 'index', 'store', 'update', 'destroy')));
});

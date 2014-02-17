<?php

Route::group(array('before' => 'PowergateAPIAuth'), function() {

    // Standard API URI's (endpoints)
    Route::resource('domains', 'DomainsController', array('only' => array('show', 'index', 'store', 'update', 'destroy')));
    Route::get('domains/{id}/records', 'DomainsController@domainRecords');
    Route::resource('records', 'RecordsController', array('only' => array('show', 'index', 'store', 'update', 'destroy')));
    Route::get('records/{id}/domain', 'RecordsController@recordDomain');

    // Catch-all route to catch invalid API URI's
    Route::any('{path?}', function($path) {
        return Response::json([
                    'error' => true,
                    'message' => 'Invalid request, see https://github.com/bobsta63/powergate/blob/master/README.md for API endpoint URI\'s'], 404);
    })->where('path', '.+');

});

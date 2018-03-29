<?php

Route::get('/oauth/clients', '\Laravel\Passport\Http\Controllers\ClientController@forUser')->middleware(['web', 'auth', 'hashed_passport']);
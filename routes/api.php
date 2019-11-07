<?php

Route::getRoutes()->getByName('passport.token')->middleware('hashed_passport');

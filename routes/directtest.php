<?php

// Direct test route for attorney dashboard
Route::get('/attorney-test', function() {
    return view('dashboard.attorney.index');
});

?>

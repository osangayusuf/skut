<?php

use App\Mail\RideConfirmationMail;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/mail', function() {
        $customerName = 'John Doe';
        $skutType = 'Glide x400';
        $duration = '1';
        $bookingDate = '22 August 2025';
        $bookingTime = '1:00 PM';
        $amount = '12.00';
        Mail::to(["osangayusuf@gmail.com"])->queue(new RideConfirmationMail($customerName, $skutType, $duration, $bookingDate, $bookingTime, $amount));
    return new RideConfirmationMail($customerName, $skutType, $duration, $bookingDate, $bookingTime, $amount);

});

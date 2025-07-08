<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;


Route::get('/test-mail', function () {
    Mail::raw('This is a test email from Laravel.', function ($message) {
        $message->to('sheikjob888@gmail.com')
                ->subject('Test Email');
    });

    return 'Test mail sent!';
});

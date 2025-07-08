<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\ExcelController;



Route::get('/test-mail', function () {
    Mail::raw('This is a test email from Laravel.', function ($message) {
        $message->to('sheikjob888@gmail.com')
                ->subject('Test Email');
    });

    return 'Test mail sent!';
});
Route::get('/', [ExcelController::class, 'processExcelFiles']);

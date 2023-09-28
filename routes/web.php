<?php

use App\Domain\Faculty\Models\Faculty;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('faculty/email/verify/{faculty}/{hash}', function ($facultyId, $hash) {
    $faculty = Faculty::findOrFail($facultyId);
    $faculty->markEmailAsVerified();

    return redirect()->route('filament.faculty.auth.login');
})->name('faculty.verification.verify');

<?php

use App\Domain\Asset\Models\Asset;
use App\Domain\Faculty\Models\Faculty;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

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

Route::get('/download-singlefile', function () {
    /** @var Asset */
    $asset = Asset::where('slug', request('asset'))->firstorFail();

    if ($asset->file) {
        $url = Storage::disk('s3')->temporaryUrl($asset->file, now()->addMinutes(30));

        $filename = $asset->name . '.' . $asset->file_type;

        $redirect = request('redirect') ?? null;

        return view('filament.pages.download', compact('url', 'filename', 'redirect'));
    }

    abort(404);
})->name('download.single-file');

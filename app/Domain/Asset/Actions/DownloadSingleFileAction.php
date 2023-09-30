<?php

declare(strict_types=1);

namespace App\Domain\Asset\Actions;

use App\Domain\Asset\Models\Asset;
use App\Domain\Faculty\Models\Faculty;
use App\Domain\Folder\DataTransferObjects\DownloadData;
use App\Domain\Folder\Models\Folder;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Livewire\Redirector;
use Illuminate\Http\RedirectResponse;

class DownloadSingleFileAction
{
    public function execute(Asset $asset, DownloadData $downloadData)
    {
        if ($asset->file && Storage::disk('s3')->exists($asset->file)) {

            if ($downloadData->downloadlink) {

                $tmpDowloadLink = $downloadData->downloadlink;
            } else {

                $url = auth()->user() instanceof Faculty ? '/faculty/documents/' : '/admin/documents/';

                $tmpDowloadLink = URL::temporarySignedRoute(
                    'download.single-file',
                    now()->addMinutes(30),
                    [
                        'asset' => $asset->slug,
                        'redirect' => url($url . $asset->folder_id),
                    ]
                );
            }

            return redirect($tmpDowloadLink);
        }

        throw new FileNotFoundException();
    }
}

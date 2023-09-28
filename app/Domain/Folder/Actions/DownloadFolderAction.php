<?php

declare(strict_types=1);

namespace App\Domain\Folder\Actions;

use App\Domain\Asset\Models\Asset;
use App\Domain\Folder\DataTransferObjects\DownloadData;
use App\Domain\Folder\Exceptions\AssetDownloadException;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class DownloadFolderAction
{
    public function execute(DownloadData $downloadData): string
    {
        // Name of the zip file to create
        $zipFilename = uniqid() . '.zip';

        // Create a ZipArchive instance to create the zip file
        $zip = new ZipArchive();

        // Create a temporary file to store the zip file
        $zipFile = tempnam(sys_get_temp_dir(), 'download-') . '.zip';

        // Open the zip file for writing
        $zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        // Download each directory from S3 and add its contents to the zip file
        $directoryPath = $downloadData->directories['0'];
        $prefix = rtrim($directoryPath, '/') . '/'; // Add trailing slash to prefix to include subdirectories

        $totalFilesToDownload = 0;

        foreach (Storage::disk('s3')->allFiles($prefix) as $key => $path) {

            //check asset status if save as draft
            $asset = Asset::where('file', $path)->first();

            // Get the file contents from S3
            /** @var string */
            $fileContents = Storage::disk('s3')->get($path);
            // Add the file to the ZIP archive
            $zip->addFromString($path, $fileContents);

            $totalFilesToDownload = $totalFilesToDownload + 1;
        }

        // Close the zip file
        $zip->close();

        if ($totalFilesToDownload == 0) {

            throw new AssetDownloadException();
        }
        // Store the zip file in an S3 bucket for later download

        /** @var String */
        $zipFileContents = file_get_contents($zipFile);

        $filesize = filesize($zipFile);

        /** @var String */
        $zipFileContents = file_get_contents($zipFile);

        Storage::disk('s3')->put('tmp-download/' . $zipFilename, $zipFileContents);

        // Get a temporary URL for the zip file that will expire after 60 minutes
        $url = Storage::disk('s3')->temporaryUrl(
            'tmp-download/' . $zipFilename,
            now()->addMinutes(60)
        );


        return $url;
    }
}

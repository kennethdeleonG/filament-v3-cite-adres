<?php

declare(strict_types=1);

namespace App\Support\Concerns;

trait CustomPagination
{
    //folders
    public bool $loadMoreFoldersReached = false;
    public mixed $folderList;
    public int $folderCount = 0;
    public int $folderPage = 1;

    //assets
    public bool $loadMoreAssetsReached = false;
    public mixed $assetList;
    public int $assetCount = 0;
    public int $assetPage = 1;
    public bool $showAssetList = false;
}

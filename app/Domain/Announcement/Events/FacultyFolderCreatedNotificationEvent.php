<?php

declare(strict_types=1);

namespace App\Domain\Announcement\Events;

use App\Domain\Folder\DataTransferObjects\FolderData;
use App\Domain\Folder\Models\Folder;
use Illuminate\Queue\SerializesModels;

class FacultyFolderCreatedNotificationEvent
{
    use SerializesModels;

    public FolderData $folderData;
    public Folder $folder;

    public function __construct(
        Folder $folder,
        FolderData $folderData
    ) {
        $this->folder = $folder;
        $this->folderData = $folderData;
    }
}

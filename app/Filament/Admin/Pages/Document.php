<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;

class Document extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.admin.document';
}

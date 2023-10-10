<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;

class ExportCollection implements FromCollection
{
    /**
     * ExportCollection constructor.
     *
     * @param Collection<int, \Illuminate\Database\Eloquent\Model> $data
     */
    public function __construct(
        protected Collection $data
    ) {
    }

    /** @return Collection<int, \Illuminate\Database\Eloquent\Model> */
    public function collection(): Collection
    {
        return $this->data;
    }
}

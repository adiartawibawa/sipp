<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;

class Schools extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $navigationLabel = 'Manajemen Sekolah';

    protected static ?string $slug = 'schools';

    protected static ?string $navigationGroup = 'Manajemen Data';

    protected static ?int $sort = 1;

    // public static function canAccess(): bool
    // {
    //     return auth()->user()?->can('access schools cluster') ?? false;
    // }
}

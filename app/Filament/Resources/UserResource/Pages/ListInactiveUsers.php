<?php

declare(strict_types=1);

namespace App\Filament\Resources\UserResource\Pages;

use Illuminate\Database\Eloquent\Builder;

class ListInactiveUsers extends ListUsers
{
    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()->onlyInactive();
    }
}

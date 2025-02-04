<?php

declare(strict_types=1);

namespace App\Concerns;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

trait ModelAsOptions
{
    public static function cachedList(): Collection
    {
        $key = Str::of(__CLASS__)
            ->classBasename()
            ->pluralStudly()
            ->snake()
            ->value();

        return Cache::remember($key, MINUTE_IN_SECONDS, function () {
            return static::query()
                ->with('category')
                ->get()
                ->keyBy('id');
        });
    }

    public static function allAsOptions(): Collection
    {
        return static::cachedList()
            ->groupBy('category_id')
            ->map->pluck('name', 'id');
    }
}

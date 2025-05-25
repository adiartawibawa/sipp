<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public string $site_name;
    public string $site_description;
    public string $timezone;
    public string $locale;
    public bool $maintenance_mode;
    public string $date_format;
    public string $time_format;

    public static function group(): string
    {
        return 'general';
    }

    public static function defaults(): array
    {
        return [
            'site_name' => 'My Application',
            'site_description' => 'Description of your application',
            'timezone' => 'Asia/Jakarta',
            'locale' => 'id',
            'maintenance_mode' => false,
            'date_format' => 'd/m/Y',
            'time_format' => 'H:i',
        ];
    }

    public static function encrypted(): array
    {
        return []; // Tidak ada data yang perlu dienkripsi
    }
}

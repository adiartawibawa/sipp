<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class SiteAppearance extends Settings
{

    public string $primary_color;
    public string $secondary_color;
    public bool $dark_mode;
    public ?string $logo_path;
    public ?string $favicon_path;
    public string $font_family;
    public bool $enable_animations;
    public ?string $custom_css;

    public static function group(): string
    {
        return 'appearance';
    }

    public static function defaults(): array
    {
        return [
            'primary_color' => '#3b82f6',
            'secondary_color' => '#64748b',
            'dark_mode' => false,
            'logo_path' => null,
            'favicon_path' => null,
            'font_family' => 'Inter',
            'enable_animations' => true,
            'custom_css' => null,
        ];
    }

    public function getCssVariables(): array
    {
        return [
            '--primary' => $this->primary_color,
            '--secondary' => $this->secondary_color,
            '--font-family' => "'{$this->font_family}', sans-serif",
        ];
    }
}

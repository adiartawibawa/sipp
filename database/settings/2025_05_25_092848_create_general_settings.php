<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $defaults = (new \App\Settings\GeneralSettings())->defaults();

        foreach ($defaults as $key => $value) {
            $this->migrator->add("general.{$key}", $value);
        }
    }
};

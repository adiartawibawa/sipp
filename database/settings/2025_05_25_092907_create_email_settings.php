<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $defaults = (new \App\Settings\EmailSettings())->defaults();

        foreach ($defaults as $key => $value) {
            $this->migrator->add("email.{$key}", $value);
        }
    }
};

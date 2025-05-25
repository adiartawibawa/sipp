<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class EmailSettings extends Settings
{
    public string $from_address;
    public string $from_name;
    public string $mailer;
    public string $host;
    public int $port;
    public string $username;
    public string $password;
    public ?string $encryption;
    public ?string $test_address;

    public static function group(): string
    {
        return 'email';
    }

    public static function defaults(): array
    {
        return [
            'from_address' => 'noreply@example.com',
            'from_name' => 'My Application',
            'mailer' => 'smtp',
            'host' => 'sandbox.smtp.mailtrap.io',
            'port' => 2525,
            'username' => '',
            'password' => '',
            'encryption' => 'tls',
            'test_address' => 'test@example.com',
        ];
    }

    // public static function encrypted(): array
    // {
    //     return [
    //         'password'
    //     ];
    // }

    public function toMailConfig(): array
    {
        $config = [
            'default' => $this->mailer,
            'from' => [
                'address' => $this->from_address,
                'name' => $this->from_name,
            ],
            'mailers' => [
                $this->mailer => [
                    'transport' => $this->mailer,
                    'host' => $this->host,
                    'port' => $this->port,
                    'encryption' => $this->encryption ?: null,
                    'username' => $this->username,
                    'password' => $this->password,
                ],
            ],
        ];

        // Add additional mailers if needed
        // if ($this->mailer === 'mailgun') {
        //     $config['mailers']['mailgun'] = [
        //         'transport' => 'mailgun',
        //         // Add other mailgun specific config
        //     ];
        // }

        return $config;
    }
}

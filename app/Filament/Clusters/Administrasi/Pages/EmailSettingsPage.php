<?php

namespace App\Filament\Clusters\Administrasi\Pages;

use App\Filament\Clusters\Administrasi;
use App\Settings\EmailSettings;
use Filament\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class EmailSettingsPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static string $view = 'filament.clusters.administrasi.pages.email-settings-page';

    protected static ?string $cluster = Administrasi::class;

    protected static ?string $navigationLabel = 'Email Settings';

    protected static ?string $navigationGroup = 'System Configuration';

    public ?array $data = [];

    public function mount(): void
    {
        $settings = app(EmailSettings::class);
        $this->form->fill($settings->toArray());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Sender Information')
                    ->schema([
                        TextInput::make('from_address')
                            ->email()
                            ->required()
                            ->maxLength(255),

                        TextInput::make('from_name')
                            ->required()
                            ->maxLength(255),
                    ])->columns(2),

                Section::make('Mail Configuration')
                    ->schema([
                        Select::make('mailer')
                            ->options([
                                'smtp' => 'SMTP',
                                'sendmail' => 'Sendmail',
                                'mailgun' => 'Mailgun',
                                'ses' => 'Amazon SES',
                                'postmark' => 'Postmark',
                            ])
                            ->required()
                            ->live(),

                        TextInput::make('host')
                            ->label('SMTP Host')
                            ->required(fn($get) => $get('mailer') === 'smtp')
                            ->visible(fn($get) => $get('mailer') === 'smtp'),

                        TextInput::make('port')
                            ->label('SMTP Port')
                            ->numeric()
                            ->required(fn($get) => $get('mailer') === 'smtp')
                            ->visible(fn($get) => $get('mailer') === 'smtp'),

                        TextInput::make('username')
                            ->label('SMTP Username')
                            ->visible(fn($get) => $get('mailer') === 'smtp'),

                        TextInput::make('password')
                            ->label('SMTP Password')
                            ->password()
                            ->visible(fn($get) => $get('mailer') === 'smtp'),

                        Select::make('encryption')
                            ->label('Encryption')
                            ->options([
                                'tls' => 'TLS',
                                'ssl' => 'SSL',
                                '' => 'None',
                            ])
                            ->visible(fn($get) => $get('mailer') === 'smtp'),
                    ]),

                Section::make('Test Configuration')
                    ->schema([
                        TextInput::make('test_address')
                            ->label('Test Email Address')
                            ->email(),
                    ]),
            ])
            ->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Save Settings')
                ->action('save')
                ->color('primary'),

            Action::make('test')
                ->label('Send Test Email')
                ->color('gray')
                ->action('sendTestEmail')
                ->visible(fn() => !empty($this->data['test_address'] ?? null)),
        ];
    }

    public function save(): void
    {
        try {
            $data = $this->form->getState();

            DB::transaction(function () use ($data) {
                /** @var EmailSettings $settings */
                $settings = app(EmailSettings::class);
                $settings->fill($data);
                $settings->save();

                // Apply runtime configuration
                config(['mail' => $settings->toMailConfig()]);
            });

            Notification::make()
                ->title('Email settings saved successfully')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Failed to save email settings')
                ->body($e->getMessage())
                ->danger()
                ->send();

            logger()->error('Email settings save failed: ' . $e->getMessage(), [
                'exception' => $e,
                'settings_data' => $this->data
            ]);
        }
    }

    public function sendTestEmail(): void
    {
        try {
            $testAddress = $this->data['test_address'] ?? null;

            if (empty($testAddress)) {
                throw new \Exception('Test email address is required');
            }

            $mailer = $this->data['mailer'] ?? config('mail.default');

            Mail::mailer($mailer)
                ->to($testAddress)
                ->send(new \App\Mail\TestMail());

            Notification::make()
                ->title('Test email sent successfully')
                ->body('Test email was sent to ' . $testAddress)
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Failed to send test email')
                ->body($e->getMessage())
                ->danger()
                ->send();

            logger()->error('Test email failed: ' . $e->getMessage(), [
                'exception' => $e,
                'test_address' => $this->data['test_address'] ?? null
            ]);
        }
    }
}

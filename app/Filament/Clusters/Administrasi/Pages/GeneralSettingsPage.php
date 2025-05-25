<?php

namespace App\Filament\Clusters\Administrasi\Pages;

use App\Filament\Clusters\Administrasi;
use App\Settings\GeneralSettings;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class GeneralSettingsPage extends Page implements HasForms, HasActions
{
    use InteractsWithForms, InteractsWithActions;

    protected static ?string $navigationIcon = 'heroicon-o-cog';

    protected static string $view = 'filament.clusters.administrasi.pages.general-settings-page';

    protected static ?string $cluster = Administrasi::class;

    protected static ?string $navigationLabel = 'General Settings';

    protected static ?string $navigationGroup = 'System Configuration';

    protected static ?int $navigationSort = 0;

    public ?array $data = [];

    protected ?GeneralSettings $settings = null;

    public function mount(): void
    {
        $this->settings = app(GeneralSettings::class);

        $this->form->fill([
            'site_name' => $this->settings->site_name,
            'site_description' => $this->settings->site_description,
            'timezone' => $this->settings->timezone,
            'maintenance_mode' => $this->settings->maintenance_mode,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Site Information')
                    ->schema([
                        TextInput::make('site_name')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('site_description')
                            ->maxLength(255),

                        TextInput::make('timezone')
                            ->required()
                            ->hintIcon('heroicon-o-globe-alt')
                            ->hintIconTooltip('Server time: ' . now()->format('Y-m-d H:i:s'))
                            ->live(),
                    ])->columns(1),

                Section::make('System')
                    ->schema([
                        Toggle::make('maintenance_mode')
                            ->label('Enable Maintenance Mode')
                            ->helperText('When enabled, only administrators can access the site'),
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
                ->color('primary')
                ->icon('heroicon-o-check-circle'),

            Action::make('reset')
                ->label('Reset Default')
                ->color('danger')
                ->icon('heroicon-o-arrow-path')
                ->action('resetSettings')
                ->requiresConfirmation()
                ->modalDescription('Are you sure you want to reset to default settings?'),
        ];
    }

    public function save(): void
    {
        try {
            $data = $this->form->getState();

            DB::transaction(function () use ($data) {
                $settings = $this->settings ?? app(GeneralSettings::class);
                $settings->fill($data);
                $settings->save();
            });

            Notification::make()
                ->title('Settings saved successfully')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Failed to save settings')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function resetSettings(): void
    {
        try {
            DB::transaction(function () {
                $settings = $this->settings ?? app(GeneralSettings::class);
                $defaults = (new GeneralSettings())->toArray();
                $settings->fill($defaults);
                $settings->save();
                $this->form->fill($defaults);
            });

            Notification::make()
                ->title('Settings reset to default')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Failed to reset settings')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}

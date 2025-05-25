<x-filament-panels::page>
    <x-filament-panels::form wire:submit="save">
        {{ $this->form }}
    </x-filament-panels::form>

    @script
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('timezoneHelper', () => ({
                    timezone: @js($this->data['timezone'] ?? config('app.timezone')),

                    init() {
                        this.$watch('timezone', (value) => {
                            if (value) {
                                const now = new Date();
                                const options = {
                                    timeZone: value,
                                    weekday: 'short',
                                    year: 'numeric',
                                    month: 'short',
                                    day: 'numeric',
                                    hour: '2-digit',
                                    minute: '2-digit',
                                    second: '2-digit'
                                };
                                const formatted = now.toLocaleString('en-US', options);
                                this.$el.querySelector('[data-timezone-helper]').textContent =
                                    `Server time in ${value}: ${formatted}`;
                            }
                        });
                    }
                }));
            });
        </script>
    @endscript
</x-filament-panels::page>

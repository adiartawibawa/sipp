<x-filament-panels::page>
    <x-filament-panels::form wire:submit="save">
        {{ $this->form }}
    </x-filament-panels::form>

    @script
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('colorPreview', () => ({
                    primaryColor: @js($this->data['primary_color'] ?? '#3b82f6'),
                    secondaryColor: @js($this->data['secondary_color'] ?? '#64748b'),
                    darkMode: @js($this->data['dark_mode'] ?? false),

                    init() {
                        // Update preview when colors change
                        this.$watch('primaryColor', (value) => {
                            if (value) {
                                document.documentElement.style.setProperty('--primary', value);
                            }
                        });

                        this.$watch('secondaryColor', (value) => {
                            if (value) {
                                document.documentElement.style.setProperty('--secondary', value);
                            }
                        });

                        // Toggle dark mode preview
                        this.$watch('darkMode', (value) => {
                            document.documentElement.classList.toggle('dark', value);
                        });
                    }
                }));
            });
        </script>
    @endscript
</x-filament-panels::page>

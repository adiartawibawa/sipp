<x-filament-panels::page>
    <x-filament-panels::form wire:submit="save">
        {{ $this->form }}
    </x-filament-panels::form>

    @script
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('mailSettings', () => ({
                    mailer: @js($this->data['mailer'] ?? 'smtp'),
                    testAddress: @js($this->data['test_address'] ?? ''),
                    isSaving: false,
                    isTesting: false,

                    init() {
                        // Initialize visibility
                        this.updateFieldVisibility();

                        // Watch for mailer changes
                        this.$watch('mailer', () => this.updateFieldVisibility());

                        // Handle Livewire events
                        Livewire.on('save-started', () => {
                            this.isSaving = true;
                        });
                        Livewire.on('save-finished', () => {
                            this.isSaving = false;
                        });
                        Livewire.on('test-started', () => {
                            this.isTesting = true;
                        });
                        Livewire.on('test-finished', () => {
                            this.isTesting = false;
                        });
                    },

                    updateFieldVisibility() {
                        const isSmtp = this.mailer === 'smtp';
                        const fields = ['host', 'port', 'username', 'password', 'encryption'];

                        fields.forEach(field => {
                            const element = document.querySelector(`[name="data.${field}"]`);
                            if (element) {
                                element.closest('.fi-field').style.display = isSmtp ? 'block' :
                                    'none';
                            }
                        });
                    },

                    canTest() {
                        return this.testAddress && this.testAddress.includes('@');
                    }
                }));
            });
        </script>
    @endscript

    @push('styles')
        <style>
            .fi-field {
                transition: all 0.2s ease;
                overflow: hidden;
            }

            .fi-field[style*="display: none"] {
                opacity: 0;
                height: 0;
                margin: 0;
                padding: 0;
                border: none;
            }

            .test-button:disabled {
                opacity: 0.5;
                cursor: not-allowed;
            }
        </style>
    @endpush
</x-filament-panels::page>

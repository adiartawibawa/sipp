<x-filament::section>
    <x-slot name="heading">
        Riwayat Kondisi
    </x-slot>

    <div class="space-y-2">
        @foreach ($room->conditions->sortByDesc('checked_at') as $condition)
            @php
                $isLatest = $loop->first; // Mark first item as latest since we sorted by date
                $color = match (true) {
                    $condition->percentage >= 80 => 'success',
                    $condition->percentage >= 50 => 'warning',
                    default => 'danger',
                };
            @endphp

            <x-filament::card class="border border-gray-200 dark:border-gray-700 rounded-lg relative" hoverable>

                <div class="flex justify-between items-start p-2">
                    <div class="space-y-1 flex items-start">
                        <div>
                            <h3 class="font-medium text-gray-900 dark:text-gray-100">
                                {{ ucfirst(str_replace('_', ' ', $condition->condition)) }}
                            </h3>
                            @if ($condition->notes)
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $condition->notes }}
                                </p>
                            @endif
                        </div>
                    </div>

                    <div class="text-right">
                        <div class="flex items-center justify-end space-x-1">
                            <span
                                class="text-sm font-medium text-{{ $color }}-600 dark:text-{{ $color }}-400">
                                {{ $condition->percentage }}%
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            <x-filament::icon icon="heroicon-o-calendar" class="h-3 w-3 inline mr-1" />
                            {{ $condition->checked_at->format('d M Y') }}
                        </p>
                    </div>
                </div>
            </x-filament::card>
        @endforeach
    </div>
</x-filament::section>

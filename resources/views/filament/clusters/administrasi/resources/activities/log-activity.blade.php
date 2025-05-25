<div class="p-6 space-y-4">
    <!-- Header dengan styling Filament -->
    <div class="flex items-center gap-x-3">
        <h3 class="text-lg font-semibold leading-6 text-gray-950 dark:text-white">
            Activity Details
        </h3>
    </div>

    <!-- Grid untuk informasi utama -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <!-- Action -->
        <div>
            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Action</h4>
            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                {{ $activity->description }}
            </p>
        </div>

        <!-- Model -->
        <div>
            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Model</h4>
            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                {{ class_basename($activity->subject_type) }} (ID: {{ $activity->subject_id }})
            </p>
        </div>

        <!-- Performed By -->
        <div>
            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Performed By</h4>
            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                {{ $activity->causer?->name ?? 'System' }}
            </p>
        </div>
    </div>

    <!-- Tanggal -->
    <div>
        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Date & Time</h4>
        <p class="mt-1 text-sm text-gray-900 dark:text-white">
            {{ $activity->created_at->format('Y-m-d H:i:s') }}
        </p>
    </div>

    @if ($activity->event === 'updated')
        <!-- Tabel Perubahan -->
        <div class="mt-4">
            <h4 class="mb-2 text-sm font-medium text-gray-500 dark:text-gray-400">Changes</h4>
            <div class="overflow-x-auto border border-gray-200 rounded-lg dark:border-gray-700">
                <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th
                                class="px-4 py-2 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-400">
                                Attribute
                            </th>
                            <th
                                class="px-4 py-2 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-400">
                                Old Value
                            </th>
                            <th
                                class="px-4 py-2 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-400">
                                New Value
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                        @foreach ($activity->changes['attributes'] as $attribute => $newValue)
                            <tr>
                                <td class="px-4 py-2 text-sm text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ Str::title($attribute) }}
                                </td>
                                <td class="px-4 py-2 text-sm text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $activity->changes['old'][$attribute] ?? '-' }}
                                </td>
                                <td class="px-4 py-2 text-sm text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $newValue }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>

@props(['state' => []])
@if ($state)
    <div class="flex flex-wrap gap-1">
        @foreach ($state as $media)
            <a href="{{ $media->getUrl() }}" target="_blank" class="hover:opacity-75">
                <img src="{{ $media->getUrl('thumb') }}" alt="Bukti kondisi" class="w-10 h-10 object-cover rounded">
            </a>
        @endforeach
    </div>
@endif

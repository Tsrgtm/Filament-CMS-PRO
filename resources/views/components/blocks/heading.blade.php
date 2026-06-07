@php
    $level = $level ?? 2;
    $classMap = [
        1 => 'text-4xl font-extrabold mb-6 mt-8',
        2 => 'text-3xl font-bold mb-4 mt-6',
        3 => 'text-2xl font-bold mb-3 mt-5',
        4 => 'text-xl font-semibold mb-2 mt-4',
    ];
    $classes = $classMap[$level] ?? $classMap[2];
@endphp

<h{{ $level }} class="cms-block-heading {{ $classes }} text-gray-900 dark:text-white">
    {{ $text }}
</h{{ $level }}>

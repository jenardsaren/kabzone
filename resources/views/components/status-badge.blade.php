@props(['status'])

@php
    $normalizedStatus = $status instanceof \BackedEnum ? $status->value : $status;

    $classes = match ($normalizedStatus) {
        'completed', 'active' => 'bg-green-100 text-green-800',
        'cancelled', 'inactive' => 'bg-red-100 text-red-800',
        default => 'bg-yellow-100 text-yellow-800',
    };
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold '.$classes]) }}>
    {{ str((string) $normalizedStatus)->headline() }}
</span>

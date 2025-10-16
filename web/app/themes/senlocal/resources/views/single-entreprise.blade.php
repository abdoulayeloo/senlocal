@extends('layouts.app')
@props([
    'packSlug' => strtolower($entreprise['acf']['pack_premium'] ?? 'standard'),
    'availablePacks' => ['standard', 'silver', 'gold', 'platinum'],
])
@if (!in_array($packSlug, $availablePacks, true))
    @php($packSlug = 'standard')
@endif

@section('content')
    @while (have_posts())
        @php(the_post())
        @includeFirst(
            ['partials.content-single-' . $packSlug, 'partials.content-single-standard'],
            [
                'entreprise' => $entreprise,
                'opening' => $opening,
                'similar_entreprises' => $similar_entreprises,
            ]
        )
    @endwhile
@endsection

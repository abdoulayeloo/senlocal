@php
    $acf = $entreprise['acf'] ?? [];
    $displayName = $acf['raison_sociale'] ?? ($entreprise['title'] ?? '');
    $logo = $acf['logo'] ?? null;
    $categories = $entreprise['terms']['categorie'] ?? [];
    $cities = $entreprise['terms']['ville'] ?? [];
    $serviceTags = $entreprise['terms']['tags_service'] ?? [];
    $packBadge = $entreprise['pack_badge'] ?? null;
    $isVerified = !empty($acf['verifie']);
    $cityNames = array_map(static fn($city) => $city['name'] ?? '', $cities);
    $cityNames = array_values(array_filter($cityNames));

    $statusText = '';
    if (function_exists('sl_opening_badge')) {
        $statusText = sl_opening_badge($entreprise['id']);
    } elseif (!empty($opening['label'])) {
        $statusText = $opening['label'];
    }
    $isOpen = !empty($opening['open']);

    $shortDescription = $acf['description_courte'] ?? '';
    $longDescription = $acf['description_longue'] ?? '';
    $address = $acf['adresse'] ?? ($acf['localisation']['address'] ?? '');
    $map = $acf['localisation'] ?? null;
    $mapUrl = null;
    if (is_array($map) && isset($map['lat'], $map['lng'])) {
        $mapUrl = sprintf(
            'https://www.google.com/maps/search/?api=1&query=%s,%s',
            rawurlencode((string) $map['lat']),
            rawurlencode((string) $map['lng']),
        );
    }

    $phone = $acf['telephone'] ?? null;
    $phoneHref = $phone ? 'tel:' . preg_replace('/[^0-9+]/', '', $phone) : null;
    $email = $acf['email_contact'] ?? null;
    $emailHref = $email ? 'mailto:' . $email : null;
    $whatsappNumber = $acf['whatsapp'] ?? null;
    $whatsappLink = $acf['whatsapp_link'] ?? null;

    $socialRaw = is_array($acf['social_links'] ?? null) ? $acf['social_links'] : [];
    $socialRegistry = [
        'facebook' => ['label' => 'Facebook'],
        'instagram' => ['label' => 'Instagram'],
        'linkedin' => ['label' => 'LinkedIn'],
        'x' => ['label' => 'X'],
        'tiktok' => ['label' => 'TikTok'],
        'youtube' => ['label' => 'YouTube'],
        'telegram' => ['label' => 'Telegram'],
        'site_secondaire' => ['label' => __('Site web secondaire', 'senlocal')],
    ];
    if (!$whatsappLink && !empty($socialRaw['whatsapp'])) {
        $whatsappLink = $socialRaw['whatsapp'];
    }
    if (!$whatsappLink && $whatsappNumber) {
        $whatsappLink = 'https://wa.me/' . preg_replace('/[^0-9]/', '', $whatsappNumber);
    }
    $website = $acf['site_web'] ?? null;

    $socialLinks = [];
    foreach ($socialRegistry as $key => $meta) {
        if (empty($socialRaw[$key])) {
            continue;
        }
        $socialLinks[] = [
            'label' => $meta['label'],
            'url' => $socialRaw[$key],
        ];
    }
    if (!empty($website)) {
        $socialLinks = array_merge([['label' => __('Site web', 'senlocal'), 'url' => $website]], $socialLinks);
    }

    $paymentChoices = [
        'cash' => __('Espèces', 'senlocal'),
        'wave' => 'Wave',
        'om' => 'Orange Money',
        'free' => 'Free Money',
        'card' => __('Carte bancaire', 'senlocal'),
        'virement' => __('Virement bancaire', 'senlocal'),
    ];
    $payments = [];
    if (!empty($acf['paiement']) && is_array($acf['paiement'])) {
        foreach ($acf['paiement'] as $choice) {
            if (isset($paymentChoices[$choice])) {
                $payments[] = $paymentChoices[$choice];
            }
        }
    }
    $priceRange = $acf['prix'] ?? null;

    $views = isset($acf['stat_vues']) ? (int) $acf['stat_vues'] : null;
    $clicks = isset($acf['stat_clics']) ? (int) $acf['stat_clics'] : null;

    $hours = function_exists('sl_get_hours') ? sl_get_hours($entreprise['id']) : null;
    $dayLabels = [
        'lun' => __('Lundi', 'senlocal'),
        'mar' => __('Mardi', 'senlocal'),
        'mer' => __('Mercredi', 'senlocal'),
        'jeu' => __('Jeudi', 'senlocal'),
        'ven' => __('Vendredi', 'senlocal'),
        'sam' => __('Samedi', 'senlocal'),
        'dim' => __('Dimanche', 'senlocal'),
    ];
    $hoursRows = [];
    if (!empty($hours['days'])) {
        foreach ($dayLabels as $code => $label) {
            $row = $hours['days'][$code] ?? null;
            if (!$row || empty($row['open']) || empty($row['start']) || empty($row['end'])) {
                $hoursRows[$code] = __('Fermé', 'senlocal');
                continue;
            }
            $text = $row['start'] . ' - ' . $row['end'];
            if (!empty($row['pause_start']) && !empty($row['pause_end'])) {
                $text .= ' ' . sprintf(__('(pause %s-%s)', 'senlocal'), $row['pause_start'], $row['pause_end']);
            }
            $hoursRows[$code] = $text;
        }
    }
@endphp

<section class="bg-base-200 mb-5 py-16 lg:py-20">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
        <div class="flex flex-col items-start gap-4 lg:flex-row lg:justify-between">
            <div class="w-full lg:w-3/4 space-y-6">
                <div class="flex flex-wrap items-center gap-3">
                    @if (!empty($packBadge['label']))
                        <span
                            class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-semibold {{ esc_attr($packBadge['class'] ?? '') }}">
                            {{ esc_html($packBadge['label']) }}
                            @if (!empty($entreprise['pack_active']))
                                <span class="inline-flex h-2 w-2 rounded-full bg-green-500" aria-hidden="true"></span>
                            @endif
                        </span>
                    @endif
                    @if ($isVerified)
                        <span
                            class="inline-flex items-center gap-2 rounded-full bg-success/10 px-3 py-1 text-xs font-semibold text-success">
                            <svg aria-hidden="true" class="h-3.5 w-3.5" fill="none" stroke="currentColor"
                                stroke-width="2" viewBox="0 0 24 24">
                                <path d="M5 13l4 4L19 7" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            {{ __('Profil vérifié', 'senlocal') }}
                        </span>
                    @endif

                    @foreach ($categories as $category)
                        <a class="inline-flex items-center gap-2 rounded-full bg-base-200 px-3 py-1 text-xs font-semibold text-base-content/80 transition hover:bg-primary/10 hover:text-secondary"
                            href="{{ esc_url($category['link']) }}">
                            {{ esc_html($category['name']) }}
                        </a>
                    @endforeach
                </div>
                <div class="flex flex-col gap-6 sm:flex-row">
                    @if (!empty($logo['url']))
                        <div
                            class="flex h-34 w-34 flex-none items-center justify-center overflow-hidden rounded-2xl border border-base-200 bg-base-100 shadow-sm sm:h-48 sm:w-48">
                            <img class="h-full w-full object-contain" src="{{ esc_url($logo['url']) }}"
                                alt="{{ esc_attr($logo['alt'] ?? $displayName) }}">
                        </div>
                    @endif

                    <div class="space-y-4">
                        <h1 class="text-3xl font-semibold leading-tight text-base-content sm:text-4xl">
                            {{ esc_html($displayName) }}
                        </h1>

                        <div class="flex flex-wrap items-center gap-3 text-sm text-base-content/70">
                            <span class="inline-flex items-center gap-2">
                                <svg aria-hidden="true" class="h-4 w-4" fill="none" stroke="currentColor"
                                    stroke-width="1.8" viewBox="0 0 24 24">
                                    <path d="M12 21s-7-4.35-7-11a7 7 0 0114 0c0 6.65-7 11-7 11z" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                    <circle cx="12" cy="10" r="2.5" />
                                </svg>
                                @if (!empty($cityNames))
                                    {{ esc_html(implode(', ', $cityNames)) }}
                                @elseif (!empty($address))
                                    {{ esc_html($address) }}
                                @else
                                    {{ __('Localisation à préciser', 'senlocal') }}
                                @endif
                            </span>
                            @if (!empty($address) && !empty($cities))
                                <span aria-hidden="true">•</span>
                                <span>{{ esc_html($address) }}</span>
                            @endif
                        </div>

                        @if (!empty($statusText))
                            <div>
                                <span
                                    class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-sm font-medium {{ $isOpen ? 'bg-success/20 text-success' : 'bg-error/20 text-error' }}">
                                    <span
                                        class="inline-flex animate-pulse h-2 w-2 rounded-full {{ $isOpen ? 'bg-success' : 'bg-error' }}"
                                        aria-hidden="true"></span>
                                    {{ esc_html($statusText) }}
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
                @if (!empty($shortDescription))
                    <p class="prose mx-auto text-justify text-base leading-relaxed text-base-content/80">
                        {{ esc_html($shortDescription) }}
                    </p>
                @endif

                <div class="flex flex-wrap gap-3">
                    @if (!empty($phoneHref))
                        <a class="btn btn-secondary gap-2" href="{{ esc_url($phoneHref, ['tel']) }}">
                            <svg aria-hidden="true" class="h-4 w-4" fill="none" stroke="currentColor"
                                stroke-width="1.8" viewBox="0 0 24 24">
                                <path
                                    d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.45 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            {{ __('Appeler', 'senlocal') }}
                        </a>
                    @endif

                    @if (!empty($whatsappLink))
                        <a class="btn btn-success gap-2" href="{{ esc_url($whatsappLink) }}" target="_blank"
                            rel="noopener">
                            <svg aria-hidden="true" class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M12.04 2a10 10 0 00-8.64 15.15L2 22l4.96-1.31A10 10 0 1012.04 2zm0 2a8 8 0 015.7 13.6 8 8 0 01-9.36 1.27l-.67-.38-2.94.77.78-2.87-.44-.7A8 8 0 0112.04 4zm-3.37 3.22c-.21 0-.54.08-.83.38a1.18 1.18 0 00-.39.87 3.33 3.33 0 00.1.84 7.83 7.83 0 003.41 4.74 7.52 7.52 0 004.62 1.44 1 1 0 00.7-.32 1.75 1.75 0 00.38-.88 1.76 1.76 0 00.12-.44.37.37 0 00-.21-.34l-1.69-.8a.37.37 0 00-.38.05l-.68.51a.37.37 0 01-.39 0 6.37 6.37 0 01-1.77-1.3 6.63 6.63 0 01-1.18-1.71.37.37 0 010-.4l.45-.53a.37.37 0 000-.37l-.6-1.71a.37.37 0 00-.34-.24z" />
                            </svg>
                            {{ __('Écrire sur WhatsApp', 'senlocal') }}
                        </a>
                    @endif

                    @if (!empty($emailHref))
                        <a class="btn btn-outline btn-ghost gap-2" href="{{ esc_url($emailHref, ['mailto']) }}">
                            <svg aria-hidden="true" class="h-4 w-4" fill="none" stroke="currentColor"
                                stroke-width="1.8" viewBox="0 0 24 24">
                                <path d="M4 4h16a2 2 0 012 2v12a2 2 0 01-2 2H4a2 2 0 01-2-2V6a2 2 0 012-2zm0 0l8 7 8-7"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            {{ __('Envoyer un e-mail', 'senlocal') }}
                        </a>
                    @endif
                </div>
            </div>
            <aside class="sticky top-16 h-full flex-col gap-6 pt-6 lg:pl-6">
                <div class="space-y-6 rounded-3xl border border-base-200 bg-base-100 p-6 shadow-sm sm:p-8">
                    <div class="space-y-3">
                        <h2 class="text-lg font-semibold text-base-content">
                            {{ __('Infos rapides', 'senlocal') }}
                        </h2>
                        <div class="space-y-3 text-sm text-base-content/80">
                            @if (!empty($address))
                                <div class="flex items-start gap-3">
                                    <span
                                        class="mt-1 inline-flex h-6 w-6 flex-none items-center justify-center rounded-full bg-primary/10 text-secondary">
                                        <svg aria-hidden="true" class="h-4 w-4" fill="none" stroke="currentColor"
                                            stroke-width="1.8" viewBox="0 0 24 24">
                                            <path d="M12 21s-7-4.35-7-11a7 7 0 0114 0c0 6.65-7 11-7 11z"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                            <circle cx="12" cy="10" r="2.5" />
                                        </svg>
                                    </span>
                                    <div>
                                        <p class="font-medium text-base-content">{{ __('Adresse', 'senlocal') }}</p>
                                        <p>{{ esc_html($address) }}</p>
                                        @if (!empty($mapUrl))
                                            <a class="mt-1 inline-flex items-center gap-2 text-sm font-semibold text-secondary"
                                                href="{{ esc_url($mapUrl) }}" target="_blank" rel="noopener">
                                                {{ __('Ouvrir dans Google Maps', 'senlocal') }}
                                                <span aria-hidden="true">→</span>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            @if (!empty($phone))
                                <div class="flex items-start gap-3">
                                    <span
                                        class="mt-1 inline-flex h-6 w-6 flex-none items-center justify-center rounded-full bg-primary/10 text-secondary">
                                        <svg aria-hidden="true" class="h-4 w-4" fill="none" stroke="currentColor"
                                            stroke-width="1.8" viewBox="0 0 24 24">
                                            <path
                                                d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.45 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </span>
                                    <div>
                                        <p class="font-medium text-base-content">{{ __('Téléphone', 'senlocal') }}</p>
                                        <a class="text-secondary"
                                            href="{{ esc_url($phoneHref, ['tel']) }}">{{ esc_html($phone) }}</a>
                                    </div>
                                </div>
                            @endif

                            @if (!empty($whatsappNumber))
                                <div class="flex items-start gap-3">
                                    <span
                                        class="mt-1 inline-flex h-6 w-6 flex-none items-center justify-center rounded-full bg-success/10 text-success">
                                        <svg aria-hidden="true" class="h-4 w-4" fill="currentColor"
                                            viewBox="0 0 24 24">
                                            <path
                                                d="M12.04 2a10 10 0 00-8.64 15.15L2 22l4.96-1.31A10 10 0 1012.04 2zm0 2a8 8 0 015.7 13.6 8 8 0 01-9.36 1.27l-.67-.38-2.94.77.78-2.87-.44-.7A8 8 0 0112.04 4z" />
                                        </svg>
                                    </span>
                                    <div>
                                        <p class="font-medium text-base-content">{{ __('WhatsApp', 'senlocal') }}</p>
                                        <p>{{ esc_html($whatsappNumber) }}</p>
                                        @if (!empty($whatsappLink))
                                            <a class="inline-flex items-center gap-2 text-sm font-semibold text-success"
                                                href="{{ esc_url($whatsappLink) }}" target="_blank" rel="noopener">
                                                {{ __('Envoyer un message', 'senlocal') }}
                                                <span aria-hidden="true">→</span>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            @if (!empty($email))
                                <div class="flex items-start gap-3">
                                    <span
                                        class="mt-1 inline-flex h-6 w-6 flex-none items-center justify-center rounded-full bg-base-200 text-base-content/70">
                                        <svg aria-hidden="true" class="h-4 w-4" fill="none" stroke="currentColor"
                                            stroke-width="1.8" viewBox="0 0 24 24">
                                            <path
                                                d="M4 4h16a2 2 0 012 2v12a2 2 0 01-2 2H4a2 2 0 01-2-2V6a2 2 0 012-2zm0 0l8 7 8-7"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </span>
                                    <div>
                                        <p class="font-medium text-base-content">{{ __('E-mail', 'senlocal') }}</p>
                                        <a class="text-secondary"
                                            href="{{ esc_url($emailHref, ['mailto']) }}">{{ esc_html($email) }}</a>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    @if (!empty($payments) || !empty($priceRange))
                        <div class="space-y-3">
                            <h3 class="text-sm font-semibold uppercase tracking-wider text-base-content/70">
                                {{ __('Informations pratiques', 'senlocal') }}
                            </h3>
                            <div class="space-y-2 text-sm text-base-content/80">
                                @if (!empty($priceRange))
                                    <p>
                                        <span
                                            class="font-medium text-base-content">{{ __('Fourchette de prix :', 'senlocal') }}</span>
                                        {{ esc_html($priceRange) }}
                                    </p>
                                @endif
                                @if (!empty($payments))
                                    <p>
                                        <span
                                            class="font-medium text-base-content">{{ __('Paiements acceptés :', 'senlocal') }}</span>
                                        {{ esc_html(implode(' · ', $payments)) }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if (!empty($socialLinks))
                        <div class="space-y-3">
                            <h3 class="text-sm font-semibold uppercase tracking-wider text-base-content/70">
                                {{ __('Présence en ligne', 'senlocal') }}
                            </h3>
                            <div class="flex flex-wrap gap-3">
                                @foreach ($socialLinks as $link)
                                    <a class="inline-flex items-center gap-2 rounded-full bg-base-200 px-3 py-1 text-sm font-semibold text-base-content/80 transition hover:bg-primary/10 hover:text-secondary"
                                        href="{{ esc_url($link['url']) }}" target="_blank" rel="noopener">
                                        {{ esc_html($link['label']) }}
                                        <span aria-hidden="true">↗</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    {{-- @if (($views ?? 0) > 0 || ($clicks ?? 0) > 0)
                        <div class="space-y-3">
                            <h3 class="text-sm font-semibold uppercase tracking-wider text-base-content/70">
                                {{ __('Statistiques', 'senlocal') }}
                            </h3>
                            <div class="grid grid-cols-2 gap-4 text-center">
                                @if (($views ?? 0) > 0)
                                    <div class="rounded-2xl bg-base-200 px-4 py-3">
                                        <p class="text-2xl font-semibold text-base-content">
                                            {{ number_format_i18n((int) $views) }}</p>
                                        <p class="text-xs uppercase tracking-wide text-base-content/60">
                                            {{ __('vues', 'senlocal') }}</p>
                                    </div>
                                @endif
                                @if (($clicks ?? 0) > 0)
                                    <div class="rounded-2xl bg-base-200 px-4 py-3">
                                        <p class="text-2xl font-semibold text-base-content">
                                            {{ number_format_i18n((int) $clicks) }}</p>
                                        <p class="text-xs uppercase tracking-wide text-base-content/60">
                                            {{ __('clics', 'senlocal') }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif --}}
                </div>
            </aside>
        </div>
    </div>
</section>

@if (!empty($longDescription) || !empty($hoursRows) || !empty($serviceTags))
    <section class="bg-base-100 pb-16 lg:pb-24">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="grid gap-10 lg:grid-cols-[minmax(0,0.65fr),minmax(0,0.35fr)]">
                @if (!empty($longDescription))
                    <div
                        class="rounded-3xl border border-base-200 bg-base-100 p-6 shadow-sm prose text-justify max-w-none text-base-content prose-headings:font-semibold prose-a:text-secondary hover:prose-a:text-secondary/80 dark:prose-invert">
                        {!! wp_kses_post($longDescription) !!}
                    </div>
                @endif

                <div class="space-y-6">
                    @if (!empty($hoursRows))
                        <div class="rounded-3xl border border-base-200 bg-base-100 p-6 shadow-sm">
                            <h3 class="text-lg font-semibold text-base-content">
                                {{ __('Horaires d’ouverture', 'senlocal') }}
                            </h3>
                            <dl class="mt-4 space-y-3 text-sm text-base-content/80">
                                @foreach ($dayLabels as $code => $label)
                                    <div class="flex items-start justify-between gap-4">
                                        <dt class="font-medium text-base-content">{{ esc_html($label) }}</dt>
                                        <dd class="text-right">
                                            {{ esc_html($hoursRows[$code] ?? __('Fermé', 'senlocal')) }}</dd>
                                    </div>
                                @endforeach
                            </dl>
                            @if (!empty($hours['exceptions']))
                                <p class="mt-4 text-xs text-base-content/60">
                                    {{ __('Horaires susceptibles d’être modifiés les jours fériés.', 'senlocal') }}
                                </p>
                            @endif
                        </div>
                    @endif

                    @if (!empty($serviceTags))
                        <div class="rounded-3xl border border-base-200 bg-base-100 p-6 shadow-sm">
                            <h3 class="text-lg font-semibold text-base-content">
                                {{ __('Services proposés', 'senlocal') }}
                            </h3>
                            <div class="mt-4 flex flex-wrap gap-2">
                                @foreach ($serviceTags as $tag)
                                    <a class="inline-flex items-center gap-2 rounded-full bg-base-200 px-3 py-1 text-sm font-semibold text-base-content/80 transition hover:bg-primary/10 hover:text-secondary"
                                        href="{{ esc_url($tag['link']) }}">
                                        {{ esc_html($tag['name']) }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endif

@if (!empty($similar_entreprises))
    <section class="bg-base-200 py-16 lg:py-24">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                <div class="space-y-3">
                    <span
                        class="inline-flex items-center gap-2 rounded-full bg-base-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.3em] text-base-content/60">
                        {{ __('Recommandations', 'senlocal') }}
                    </span>
                    <h2 class="text-3xl font-semibold text-base-content sm:text-4xl">
                        {{ __('Entreprises similaires à découvrir', 'senlocal') }}
                    </h2>
                    <p class="max-w-2xl text-sm text-base-content/70">
                        {{ __('Continuez votre exploration locale avec ces adresses proches en style ou en services.', 'senlocal') }}
                    </p>
                </div>
            </div>

            <div class="mt-12 grid gap-6 md:grid-cols-2 xl:grid-cols-4">
                @foreach ($similar_entreprises as $similar)
                    <article
                        class="group flex h-full flex-col overflow-hidden rounded-3xl border border-base-200 bg-base-100 shadow-sm transition hover:-translate-y-1 hover:border-primary/50 hover:shadow-lg">
                        <a class="relative block aspect-[4/3] overflow-hidden"
                            href="{{ esc_url($similar['link']) }}">
                            @if (!empty($similar['thumb']))
                                <img class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                                    src="{{ esc_url($similar['thumb']) }}" alt="{{ esc_attr($similar['title']) }}">
                            @else
                                <div class="flex h-full items-center justify-center bg-base-200 text-base-content/40">
                                    <span
                                        class="text-xs uppercase tracking-[0.3em]">{{ __('Sans visuel', 'senlocal') }}</span>
                                </div>
                            @endif
                        </a>
                        <div class="flex flex-1 flex-col gap-4 p-6">
                            <div class="flex items-center justify-between gap-3">
                                @if (!empty($similar['badge']['label']))
                                    <span
                                        class="badge badge-outline text-xs font-semibold {{ esc_attr($similar['badge']['class'] ?? '') }}">
                                        {{ esc_html($similar['badge']['label']) }}
                                    </span>
                                @endif
                            </div>
                            <h3 class="text-lg font-semibold text-base-content">
                                <a class="transition hover:text-secondary" href="{{ esc_url($similar['link']) }}">
                                    {{ esc_html($similar['title']) }}
                                </a>
                            </h3>
                            <div class="mt-auto">
                                <a class="inline-flex items-center gap-2 text-sm font-semibold text-secondary transition hover:gap-3"
                                    href="{{ esc_url($similar['link']) }}">
                                    {{ __('Voir la fiche', 'senlocal') }}
                                    <span aria-hidden="true">→</span>
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
@endif

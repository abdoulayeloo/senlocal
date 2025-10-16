@extends('layouts.app')

@section('content')
    @php
        $primaryCta = $page_links['marketplace'] ?? ($page_links['services'] ?? ($page_links['vendors'] ?? '#explore'));
        $secondaryCta = $page_links['contact'] ?? ($page_links['about'] ?? '#contact');
        $featureHighlights =
            !empty($feature_cards) && is_array($feature_cards) ? array_slice($feature_cards, 0, 2) : [];
    @endphp
    <main class="bg-base-100 text-base-content">
        <section
            class="relative isolate overflow-hidden bg-gradient-to-br from-secondary via-secondary to-secondary/80 text-secondary-content"
            id="hero">
            <div aria-hidden="true" class="pointer-events-none absolute inset-0 opacity-60">
                <div class="absolute -top-24 right-1/4 h-72 w-72 rounded-full bg-white/20 blur-3xl"></div>
                <div class="absolute inset-x-0 bottom-0 h-64 bg-gradient-to-t from-primary/50"></div>
            </div>
            <div class="relative mx-auto max-w-7xl px-6 py-24 lg:py-32">
                <div class="grid items-center gap-16 lg:grid-cols-[minmax(0,1.1fr),minmax(0,0.9fr)]">
                    <div class="space-y-8">
                        <span
                            class="inline-flex items-center gap-2 rounded-full bg-primary-content/10 px-4 py-2 text-sm font-medium text-primary-content/80">
                            <span class="h-2 w-2 rounded-full bg-accent"></span>
                            {{ __('Le commerce local réinventé', 'sage') }}
                        </span>
                        <h1 class="text-4xl font-semibold leading-tight tracking-tight sm:text-5xl lg:text-6xl">
                            {{ esc_html(sprintf(__('%s', 'sage'), $hero['site_name'] ?? ($siteName ?? 'Senlocal'))) }}
                        </h1>
                        @if (!empty($hero['subtitle']))
                            <p class="max-w-xl text-lg leading-relaxed text-primary-content/80 sm:text-xl">
                                {{ esc_html($hero['subtitle']) }}
                            </p>
                        @endif
                        <div class="flex flex-col flex-wrap gap-3 sm:flex-row sm:items-center">
                            <a class="btn btn-primary btn-lg rounded-full shadow-lg shadow-primary/40"
                                href="{{ esc_url($primaryCta) }}">
                                {{ __('Découvrir maintenant', 'sage') }}
                            </a>
                            <a class="btn btn-outline btn-lg rounded-full border-primary-content/30 text-primary-content hover:border-primary-content hover:bg-primary-content/10"
                                href="{{ esc_url($secondaryCta) }}">
                                {{ __('Devenir partenaire', 'sage') }}
                            </a>
                        </div>
                        @if (!empty($stat_items))
                            <dl class="grid gap-4 pt-6 sm:grid-cols-2 lg:grid-cols-4">
                                @foreach ($stat_items as $item)
                                    <div
                                        class="rounded-3xl border border-primary-content/20 bg-primary-content/5 px-6 py-5 backdrop-blur transition hover:border-primary-content/40">
                                        <dt class="text-sm font-medium uppercase tracking-wide text-primary-content/70">
                                            {{ esc_html($item['label']) }}
                                        </dt>
                                        <dd class="mt-2 text-3xl font-semibold">
                                            {{ esc_html($item['value']) }}
                                        </dd>
                                    </div>
                                @endforeach
                            </dl>
                        @endif
                    </div>
                    <div class="relative">
                        <div class="absolute -top-6 -left-8 h-28 w-28 rounded-full bg-accent/40 blur-2xl lg:-left-16"></div>
                        <div class="absolute -bottom-10 -right-8 h-36 w-36 rounded-full bg-info/40 blur-3xl lg:-right-16">
                        </div>
                        <div
                            class="relative overflow-hidden rounded-[32px] border border-primary-content/15 bg-base-100 text-base-content shadow-2xl shadow-primary/40">
                            @if (!empty($hero['image']['url']))
                                <img class="h-80 w-full object-cover" src="{{ esc_url($hero['image']['url']) }}"
                                    alt="{{ esc_attr($hero['image']['alt'] ?? ($hero['site_name'] ?? ($siteName ?? ''))) }}">
                            @else
                                <div
                                    class="flex h-80 items-center justify-center bg-primary/10 text-center text-primary/80">
                                    <div>
                                        <span class="text-5xl font-semibold">Senlocal</span>
                                        <p class="mt-3 text-sm uppercase tracking-[0.35em] text-primary/60">
                                            {{ __('Marketplace communautaire', 'sage') }}
                                        </p>
                                    </div>
                                </div>
                            @endif
                            @if (!empty($featureHighlights))
                                <div class="space-y-3 border-t border-base-200 bg-base-100/80 p-6 backdrop-blur">
                                    <span class="text-xs font-semibold uppercase tracking-[0.35em] text-primary/70">
                                        {{ __('Points forts de la plateforme', 'sage') }}
                                    </span>
                                    <ul class="space-y-3 text-sm text-base-content/70">
                                        @foreach ($featureHighlights as $highlight)
                                            <li class="flex items-start gap-3">
                                                <span
                                                    class="mt-1 inline-flex h-2.5 w-2.5 flex-none rounded-full bg-primary"></span>
                                                <span>{{ esc_html($highlight['title']) }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>

        @if (!empty($feature_cards))
            <section class="bg-base-100 py-24" id="features">
                <div class="mx-auto max-w-7xl px-6">
                    <div class="flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
                        <div class="max-w-2xl space-y-4">
                            <span
                                class="inline-flex items-center gap-2 rounded-full bg-base-200 px-4 py-1 text-xs font-semibold uppercase tracking-[0.3em] text-base-content/70">
                                {{ __('Pourquoi Senlocal', 'sage') }}
                            </span>
                            <h2 class="text-3xl font-semibold leading-tight sm:text-4xl">
                                {{ __('Tout pour développer votre activité de proximité', 'sage') }}
                            </h2>
                            <p class="text-base text-base-content/70">
                                {{ __('Notre plateforme réunit commerçants, livreurs et clients fidèles grâce à des outils simples à utiliser.', 'sage') }}
                            </p>
                        </div>
                        @if (!empty($page_links['about']))
                            <a class="btn btn-ghost gap-2 text-base-content hover:text-primary"
                                href="{{ esc_url($page_links['about']) }}">
                                {{ __('Découvrir le fonctionnement', 'sage') }}
                                <span aria-hidden="true">→</span>
                            </a>
                        @endif
                    </div>
                    <div class="mt-14 grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                        @foreach ($feature_cards as $card)
                            <article
                                class="group relative overflow-hidden rounded-3xl border border-base-200 bg-base-100 p-8 shadow-sm transition hover:-translate-y-1 hover:border-primary/60 hover:shadow-lg">
                                <div aria-hidden="true"
                                    class="absolute inset-x-6 top-0 h-32 rounded-b-[48px] bg-gradient-to-br from-primary/10 via-transparent to-accent/10 opacity-0 transition group-hover:opacity-100">
                                </div>
                                <div class="relative space-y-5">
                                    <span
                                        class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-primary/10 text-sm font-semibold text-primary">
                                        {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                                    </span>
                                    <h3 class="text-xl font-semibold">
                                        {{ esc_html($card['title']) }}
                                    </h3>
                                    <p class="text-base text-base-content/70">
                                        {{ esc_html($card['description']) }}
                                    </p>
                                    @if (!empty($card['link']))
                                        <a class="inline-flex items-center gap-2 text-sm font-semibold text-primary transition group-hover:gap-3"
                                            href="{{ esc_url($card['link']) }}">
                                            {{ esc_html($card['link_label']) }}
                                            <span aria-hidden="true">→</span>
                                        </a>
                                    @endif
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        @if (!empty($featured_categories))
            <section class="bg-base-200 py-24" id="explore">
                <div class="mx-auto max-w-7xl px-6">
                    <div class="flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
                        <div class="max-w-2xl space-y-4">
                            <span
                                class="inline-flex items-center gap-2 rounded-full bg-base-100 px-4 py-1 text-xs font-semibold uppercase tracking-[0.3em] text-base-content/70">
                                {{ __('Sélections coup de cœur', 'sage') }}
                            </span>
                            <h2 class="text-3xl font-semibold sm:text-4xl">
                                {{ __('Découvrez ce que les habitants adorent', 'sage') }}
                            </h2>
                            <p class="text-base text-base-content/70">
                                {{ __('Parcourez nos catégories les plus actives pour trouver de nouveaux artisans, services et saveurs près de chez vous.', 'sage') }}
                            </p>
                        </div>
                        @if (($page_links['marketplace'] ?? false) || ($page_links['entreprise_archive'] ?? false))
                            <a class="btn btn-primary btn-outline"
                                href="{{ esc_url($page_links['marketplace'] ?? $page_links['entreprise_archive']) }}">
                                {{ __('Voir tout le marché', 'sage') }}
                            </a>
                        @endif
                    </div>
                    <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($featured_categories as $term)
                            <a class="group flex h-full flex-col justify-between rounded-3xl border border-base-200 bg-base-100 p-6 transition hover:-translate-y-1 hover:border-primary hover:shadow-xl"
                                href="{{ esc_url($term['link']) }}">
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between gap-4">
                                        <h3 class="text-lg font-semibold">{{ esc_html($term['name']) }}</h3>
                                        <span class="badge badge-primary badge-outline">
                                            {{ esc_html(sprintf(_n('%s article', '%s articles', $term['count'], 'sage'), number_format_i18n($term['count']))) }}
                                        </span>
                                    </div>
                                    @if (!empty($term['description']))
                                        <p class="text-sm text-base-content/70">
                                            {{ esc_html(wp_trim_words(wp_strip_all_tags($term['description']), 18, '…')) }}
                                        </p>
                                    @endif
                                </div>
                                <span
                                    class="mt-6 inline-flex items-center gap-2 text-sm font-semibold text-primary transition group-hover:gap-3">
                                    {{ __('Découvrir cette sélection', 'sage') }}
                                    <span aria-hidden="true">→</span>
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        @if (!empty($featured_entreprises))
            <section class="bg-base-100 py-24" id="vendors">
                <div class="mx-auto max-w-7xl px-6">
                    <div class="flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
                        <div class="max-w-2xl space-y-4">
                            <span
                                class="inline-flex items-center gap-2 rounded-full bg-base-200 px-4 py-1 text-xs font-semibold uppercase tracking-[0.3em] text-base-content/70">
                                {{ __('Commerçants à l’affiche', 'sage') }}
                            </span>
                            <h2 class="text-3xl font-semibold leading-tight sm:text-4xl">
                                {{ __('Rencontrez celles et ceux qui font vivre Senlocal', 'sage') }}
                            </h2>
                            <p class="text-base text-base-content/70">
                                {{ __('Des boulangers du matin aux réparateurs nocturnes, ces partenaires certifiés livrent avec passion et précision.', 'sage') }}
                            </p>
                        </div>
                        @if (!empty($page_links['entreprise_archive']))
                            <a class="btn btn-link text-primary" href="{{ esc_url($page_links['entreprise_archive']) }}">
                                {{ __('Voir tous les commerçants', 'sage') }}
                            </a>
                        @endif
                    </div>
                    <div class="mt-12 grid gap-8 lg:grid-cols-3">
                        @foreach ($featured_entreprises as $entreprise)
                            @php
                                $opening = $entreprise['opening'] ?? null;
                            @endphp
                            <article
                                class="group flex h-full flex-col overflow-hidden rounded-3xl border border-base-200 bg-base-100 shadow-sm transition hover:-translate-y-1 hover:border-primary/60 hover:shadow-xl">
                                <a class="relative block aspect-[4/3] w-full overflow-hidden"
                                    href="{{ esc_url($entreprise['link']) }}">
                                    @if (!empty($entreprise['thumbnail']))
                                        <img class="h-full w-full object-cover transition duration-700 group-hover:scale-105"
                                            src="{{ esc_url($entreprise['thumbnail']) }}"
                                            alt="{{ esc_attr($entreprise['title']) }}">
                                    @else
                                        <div
                                            class="flex h-full items-center justify-center bg-base-200 text-base-content/40">
                                            <span
                                                class="text-sm uppercase tracking-[0.3em]">{{ __('Aucune image', 'sage') }}</span>
                                        </div>
                                    @endif
                                    <span aria-hidden="true"
                                        class="absolute inset-0 bg-gradient-to-t from-base-100/80 via-transparent opacity-0 transition group-hover:opacity-100"></span>
                                </a>
                                <div class="flex flex-1 flex-col gap-4 p-6">
                                    <div class="flex flex-wrap items-center gap-2 text-xs font-semibold">
                                        @if (!empty($entreprise['badge']['label']))
                                            <span
                                                class="badge border-none text-xs font-semibold {{ esc_attr($entreprise['badge']['class'] ?? '') }}">
                                                {{ esc_html($entreprise['badge']['label']) }}
                                            </span>
                                        @endif
                                        @if (!empty($entreprise['verified']))
                                            <span
                                                class="inline-flex items-center gap-1 rounded-full bg-success/10 px-2 py-1 text-success">
                                                <svg aria-hidden="true" class="h-3 w-3" fill="none"
                                                    stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path d="M5 13l4 4L19 7" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                </svg>
                                                {{ __('Certifié', 'sage') }}
                                            </span>
                                        @endif
                                    </div>
                                    <h3 class="text-xl font-semibold">
                                        <a class="transition hover:text-primary"
                                            href="{{ esc_url($entreprise['link']) }}">
                                            {{ esc_html($entreprise['title']) }}
                                        </a>
                                    </h3>
                                    @if (!empty($entreprise['summary']))
                                        <p class="text-sm text-base-content/70">
                                            {{ esc_html($entreprise['summary']) }}
                                        </p>
                                    @endif
                                    <div class="space-y-2 text-xs text-base-content/60">
                                        @if (!empty($entreprise['categorie_label']))
                                            <div class="flex items-center gap-2">
                                                <span class="inline-block h-2 w-2 rounded-full bg-primary/60"></span>
                                                <span>{{ esc_html($entreprise['categorie_label']) }}</span>
                                            </div>
                                        @endif
                                        @if (!empty($entreprise['ville_label']))
                                            <div class="flex items-center gap-2">
                                                <span class="inline-block h-2 w-2 rounded-full bg-secondary/60"></span>
                                                <span>{{ esc_html($entreprise['ville_label']) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    @if (!empty($opening['label']))
                                        <div
                                            class="flex items-center gap-2 text-xs font-semibold {{ !empty($opening['open']) ? 'text-success' : 'text-base-content/60' }}">
                                            <svg aria-hidden="true" class="h-3.5 w-3.5" fill="none"
                                                stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path d="M12 8v4l2.5 1.5" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                                <circle cx="12" cy="12" r="9" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                            </svg>
                                            <span>{{ esc_html($opening['label']) }}</span>
                                        </div>
                                    @endif
                                    <div class="mt-auto">
                                        <a class="inline-flex items-center gap-2 text-sm font-semibold text-primary transition hover:gap-3"
                                            href="{{ esc_url($entreprise['link']) }}">
                                            {{ __('Voir le profil', 'sage') }}
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

        @if (!empty($testimonials))
            <section class="bg-base-200 py-24" id="stories">
                <div class="mx-auto max-w-7xl px-6">
                    <div class="mx-auto max-w-2xl text-center">
                        <span
                            class="inline-flex items-center gap-2 rounded-full bg-base-100 px-4 py-1 text-xs font-semibold uppercase tracking-[0.3em] text-base-content/70">
                            {{ __('Paroles de la communauté', 'sage') }}
                        </span>
                        <h2 class="mt-4 text-3xl font-semibold sm:text-4xl">
                            {{ __('Histoires de clients, livreurs et commerçants', 'sage') }}
                        </h2>
                        <p class="mt-4 text-base text-base-content/70">
                            {{ __('Des moments vécus qui prouvent que le commerce local prospère quand le voisinage se serre les coudes.', 'sage') }}
                        </p>
                    </div>
                    <div class="mt-14 grid gap-8 lg:grid-cols-3">
                        @foreach ($testimonials as $testimonial)
                            <article
                                class="flex h-full flex-col gap-6 rounded-3xl border border-base-300/80 bg-base-100 p-8 shadow-sm transition hover:-translate-y-1 hover:border-primary/60 hover:shadow-lg">
                                <div class="flex items-center gap-4">
                                    @if (!empty($testimonial['avatar']))
                                        <img class="h-12 w-12 rounded-full object-cover"
                                            src="{{ esc_url($testimonial['avatar']) }}"
                                            alt="{{ esc_attr($testimonial['title']) }}">
                                    @else
                                        <span
                                            class="flex h-12 w-12 items-center justify-center rounded-full bg-primary/10 text-base font-semibold text-primary">
                                            {{ esc_html($testimonial['initial']) }}
                                        </span>
                                    @endif
                                    <div>
                                        <h3 class="text-lg font-semibold">{{ esc_html($testimonial['title']) }}</h3>
                                        @if (!empty($testimonial['role']))
                                            <p class="text-sm text-base-content/60">{{ esc_html($testimonial['role']) }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                                <p class="text-base leading-relaxed text-base-content/70">
                                    “{{ esc_html($testimonial['content']) }}”
                                </p>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        <section class="bg-base-100 py-24" id="journal">
            <div class="mx-auto max-w-7xl px-6">
                <div class="flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
                    <div class="max-w-2xl space-y-4">
                        <span
                            class="inline-flex items-center gap-2 rounded-full bg-base-200 px-4 py-1 text-xs font-semibold uppercase tracking-[0.3em] text-base-content/70">
                            {{ __('Actualités Senlocal', 'sage') }}
                        </span>
                        <h2 class="text-3xl font-semibold leading-tight sm:text-4xl">
                            {{ __('Idées, récits et nouveautés de Senlocal', 'sage') }}
                        </h2>
                        <p class="text-base text-base-content/70">
                            {{ __('Suivez nos lancements produits, focus commerçants et extensions de livraison dans nos quartiers.', 'sage') }}
                        </p>
                    </div>
                    @if (!empty($page_links['marketplace']))
                        <a class="btn btn-ghost gap-2 text-base-content hover:text-primary"
                            href="{{ esc_url($page_links['marketplace']) }}">
                            {{ __('Explorer le marché', 'sage') }}
                            <span aria-hidden="true">→</span>
                        </a>
                    @endif
                </div>
                @if (!empty($latest_posts))
                    <div class="mt-12 grid gap-8 lg:grid-cols-3">
                        @foreach ($latest_posts as $article)
                            <article
                                class="group flex h-full flex-col overflow-hidden rounded-3xl border border-base-200 bg-base-100 shadow-sm transition hover:-translate-y-1 hover:border-primary/60 hover:shadow-lg">
                                @if (!empty($article['thumbnail']))
                                    <a class="relative block aspect-[4/3] overflow-hidden"
                                        href="{{ esc_url($article['link']) }}">
                                        <img class="h-full w-full object-cover transition duration-700 group-hover:scale-105"
                                            src="{{ esc_url($article['thumbnail']) }}"
                                            alt="{{ esc_attr($article['title']) }}">
                                    </a>
                                @endif
                                <div class="flex flex-1 flex-col gap-4 p-6">
                                    <div class="flex items-center gap-3 text-xs text-base-content/60">
                                        <time
                                            datetime="{{ esc_attr($article['date_iso']) }}">{{ esc_html($article['date']) }}</time>
                                        <span aria-hidden="true">•</span>
                                        <span>{{ esc_html($article['author']) }}</span>
                                    </div>
                                    <h3 class="text-xl font-semibold">
                                        <a class="transition hover:text-primary" href="{{ esc_url($article['link']) }}">
                                            {{ esc_html($article['title']) }}
                                        </a>
                                    </h3>
                                    @if (!empty($article['excerpt']))
                                        <p class="text-sm text-base-content/70">
                                            {{ esc_html($article['excerpt']) }}
                                        </p>
                                    @endif
                                    <a class="mt-auto inline-flex items-center gap-2 text-sm font-semibold text-primary transition hover:gap-3"
                                        href="{{ esc_url($article['link']) }}">
                                        {{ __('Lire la suite', 'sage') }}
                                        <span aria-hidden="true">→</span>
                                    </a>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @else
                    <div
                        class="mt-12 rounded-3xl border border-dashed border-base-200 p-10 text-center text-base-content/60">
                        {{ __('Aucun article pour le moment. Revenez vite pour découvrir les prochaines actualités de l’équipe Senlocal.', 'sage') }}
                    </div>
                @endif
            </div>
        </section>

        <section class="bg-gradient-to-br from-primary/5 via-secondary/5 to-primary/10 py-24" id="contact">
            <div class="mx-auto max-w-7xl px-6">
                <div class="grid gap-12 lg:grid-cols-[minmax(0,0.6fr),minmax(0,0.4fr)]">
                    <div class="space-y-6">
                        <span
                            class="inline-flex items-center gap-2 rounded-full bg-base-100 px-4 py-1 text-xs font-semibold uppercase tracking-[0.3em] text-base-content/70">
                            {{ __('Construisons ensemble', 'sage') }}
                        </span>
                        <h2 class="text-3xl font-semibold leading-tight sm:text-4xl">
                            {{ __('Expliquez-nous comment Senlocal peut soutenir votre projet de quartier', 'sage') }}
                        </h2>
                        <p class="text-base text-base-content/70">
                            {{ __('Notre équipe partenaires allie expertise locale et maîtrise du digital pour vous aider à lancer vos initiatives, événements et services de livraison.', 'sage') }}
                        </p>
                        <div class="grid gap-6 sm:grid-cols-2">
                            <div class="space-y-2 rounded-2xl border border-base-200 bg-base-100 p-6">
                                <h3 class="text-base font-semibold text-base-content">
                                    {{ __('Échanger avec l’équipe commerciale', 'sage') }}
                                </h3>
                                <p class="text-sm text-base-content/70">
                                    {{ __('Planifiez vos lancements, vos commandes groupées et vos campagnes.', 'sage') }}
                                </p>
                                <a class="inline-flex items-center gap-2 text-sm font-semibold text-primary"
                                    href="mailto:hello@senlocal.com">
                                    hello@senlocal.com
                                    <span aria-hidden="true">→</span>
                                </a>
                            </div>
                            <div class="space-y-2 rounded-2xl border border-base-200 bg-base-100 p-6">
                                <h3 class="text-base font-semibold text-base-content">{{ __('Service client', 'sage') }}
                                </h3>
                                <p class="text-sm text-base-content/70">
                                    {{ __('Du lundi au vendredi 8h00-20h00, week-end 9h00-18h00.', 'sage') }}
                                </p>
                                <a class="inline-flex items-center gap-2 text-sm font-semibold text-primary"
                                    href="tel:+221000000000">
                                    +221 00 000 00 00
                                    <span aria-hidden="true">→</span>
                                </a>
                            </div>
                        </div>
                        <div class="rounded-2xl border border-base-200 bg-base-100 p-6">
                            <h3 class="text-base font-semibold text-base-content">{{ __('Passez nous voir', 'sage') }}
                            </h3>
                            <p class="mt-2 text-sm text-base-content/70">
                                {{ __('Rue de la Corniche, Dakar — Ouvert du lundi au samedi', 'sage') }}
                            </p>
                        </div>
                    </div>
                    <div class="rounded-3xl border border-base-200 bg-base-100/90 p-8 shadow-lg backdrop-blur">
                        <h3 class="text-2xl font-semibold text-base-content">{{ __('Restez informés', 'sage') }}</h3>
                        <p class="mt-4 text-sm text-base-content/70">
                            {{ __('Recevez nos actualités : nouveaux partenaires, lancements exclusifs et extension de la livraison.', 'sage') }}
                        </p>
                        <form class="mt-8 space-y-4" action="{{ esc_url(admin_url('admin-ajax.php')) }}" method="post">
                            <label class="form-control w-full">
                                <span
                                    class="label-text text-sm text-base-content/70">{{ __('Nom complet', 'sage') }}</span>
                                <input class="input input-bordered w-full" type="text" name="newsletter_name"
                                    placeholder="{{ esc_attr(__('Votre nom', 'sage')) }}" required>
                            </label>
                            <label class="form-control w-full">
                                <span
                                    class="label-text text-sm text-base-content/70">{{ __('Adresse e-mail', 'sage') }}</span>
                                <input class="input input-bordered w-full" type="email" name="newsletter_email"
                                    placeholder="{{ esc_attr(__('vous@exemple.com', 'sage')) }}" required>
                            </label>
                            <input type="hidden" name="action" value="senlocal_newsletter_signup">
                            <button class="btn btn-primary btn-block" type="submit">
                                {{ __('S\'abonner', 'sage') }}
                            </button>
                            <p class="text-xs text-base-content/60">
                                {{ __('Nous respectons votre vie privée. Désinscription possible à tout moment.', 'sage') }}
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        @if (!empty($front_page_content))
            <section class="bg-base-200 py-24" id="about">
                <div class="mx-auto max-w-4xl px-6">
                    <div class="prose prose-lg max-w-none dark:prose-invert">
                        {!! $front_page_content !!}
                    </div>
                </div>
            </section>
        @endif
    </main>
@endsection

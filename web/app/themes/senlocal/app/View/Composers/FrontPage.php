<?php

namespace App\View\Composers;


/**
 * Front page composer.
 */
class FrontPage extends AbstractComposer
{
    use Cpt;

    /** @var array<int,string> */
    protected static $views = [
        'front-page',
    ];

    public function with(): array
    {
        $frontId = get_queried_object_id() ?: (int) get_option('page_on_front') ?: null;
        $pageLinks = $this->pageLinks();

        return [
            'hero' => $this->heroData($frontId),
            'page_links' => $pageLinks,
            'feature_cards' => $this->featureCards($pageLinks),
            'stat_items' => $this->statItems(),
            'featured_categories' => $this->featuredCategories(),
            'featured_entreprises' => $this->featuredEntreprises(),
            'testimonials' => $this->testimonials(),
            'latest_posts' => $this->latestPosts(),
            'front_page_content' => $frontId ? apply_filters('the_content', get_post_field('post_content', $frontId)) : '',
        ];
    }

    /**
     * Hero block information (title, excerpt, featured image).
     */
    protected function heroData(?int $frontId): array
    {
        $siteName = get_bloginfo('name');
        $description = get_bloginfo('description');
        $excerpt = $frontId ? get_post_field('post_excerpt', $frontId) : '';
        $subtitle = $excerpt ?: $description;
        $imageId = $frontId ? get_post_thumbnail_id($frontId) : 0;
        $imageUrl = $imageId ? wp_get_attachment_image_url($imageId, 'full') : null;
        $imageAlt = $imageId ? get_post_meta($imageId, '_wp_attachment_image_alt', true) : '';

        return [
            'site_name' => $siteName,
            'subtitle' => $subtitle,
            'image' => [
                'url' => $imageUrl,
                'alt' => $imageAlt ?: $siteName,
            ],
        ];
    }

    /**
     * Resolve important internal page links by slug.
     */
    protected function pageLinks(): array
    {
        $slugs = ['marketplace', 'vendors', 'services', 'about', 'contact'];
        $links = [];

        foreach ($slugs as $slug) {
            $page = get_page_by_path($slug);
            if ($page instanceof \WP_Post) {
                $links[$slug] = get_permalink($page);
            }
        }

        if (post_type_exists($this->entrepriseType())) {
            $archiveLink = get_post_type_archive_link($this->entrepriseType());
            if ($archiveLink) {
                $links['entreprise_archive'] = $archiveLink;
            }
        }

        return $links;
    }

    /**
     * Highlight cards presented under the hero.
     *
     * @param array<string,string> $pageLinks
     * @return array<int,array<string,string|null>>
     */
    protected function featureCards(array $pageLinks): array
    {
        return [
            [
                'title' => __('Découvrez les trésors locaux', 'sage'),
                'description' => __('Parcourez des collections sélectionnées de produits, d’artisanat et de services à la demande proposés par des vendeurs de votre région.', 'sage'),
                'link' => $pageLinks['marketplace'] ?? ($pageLinks['services'] ?? null),
                'link_label' => __('Explorer le marché', 'sage'),
            ],
            [
                'title' => __('Valorisez les vendeurs vérifiés', 'sage'),
                'description' => __('Chaque annonce est vérifiée pour sa qualité, son authenticité et son impact communautaire avant sa mise en ligne.', 'sage'),
                'link' => $pageLinks['vendors'] ?? ($pageLinks['about'] ?? null),
                'link_label' => __('Rencontrez nos vendeurs', 'sage'),
            ],
            [
                'title' => __('Livraison locale le jour même', 'sage'),
                'description' => __('Récupérez votre commande sur place ou planifiez une livraison écologique à travers la ville.', 'sage'),
                'link' => $pageLinks['contact'] ?? ($pageLinks['services'] ?? null),
                'link_label' => __('Voir les options de livraison', 'sage'),
            ],
        ];
        return [
            [
                'title' => __('Découvrez les trésors locaux', 'sage'),
                'description' => __('Parcourez des collections sélectionnées de produits, d’artisanat et de services à la demande proposés par des vendeurs de votre région.', 'sage'),
                'link' => $pageLinks['marketplace'] ?? ($pageLinks['services'] ?? null),
                'link_label' => __('Explorer le marché', 'sage'),
            ],
            [
                'title' => __('Valorisez les vendeurs vérifiés', 'sage'),
                'description' => __('Chaque annonce est vérifiée pour sa qualité, son authenticité et son impact communautaire avant sa mise en ligne.', 'sage'),
                'link' => $pageLinks['vendors'] ?? ($pageLinks['about'] ?? null),
                'link_label' => __('Rencontrez nos vendeurs', 'sage'),
            ],
            [
                'title' => __('Livraison locale le jour même', 'sage'),
                'description' => __('Récupérez votre commande sur place ou planifiez une livraison écologique à travers la ville.', 'sage'),
                'link' => $pageLinks['contact'] ?? ($pageLinks['services'] ?? null),
                'link_label' => __('Voir les options de livraison', 'sage'),
            ],
        ];
    }

    /**
     * Small highlight stats for the hero block.
     *
     * @return array<int,array<string,string>>
     */
    protected function statItems(): array
    {
        return [
            ['value' => __('250+', 'sage'), 'label' => __('Partenaires vérifiés', 'sage')],
            ['value' => __('12', 'sage'), 'label' => __('Communautés desservies', 'sage')],
            ['value' => __('4.9/5', 'sage'), 'label' => __('Note moyenne', 'sage')],
            ['value' => __('24h', 'sage'), 'label' => __('Délai de réponse du support', 'sage')],
        ];
    }

    /**
     * Featured taxonomies (entreprise catégorie preferred, fallback to blog category).
     *
     * @return array<int,array<string,mixed>>
     */
    protected function featuredCategories(int $limit = 6): array
    {
        $taxonomy = taxonomy_exists('categorie') ? 'categorie' : 'category';
        $terms = get_terms([
            'taxonomy' => $taxonomy,
            'hide_empty' => true,
            'number' => $limit,
            'orderby' => 'count',
            'order' => 'DESC',
        ]);

        if (is_wp_error($terms) || empty($terms)) {
            return [];
        }

        return array_map(function ($term) {
            return [
                'id' => (int) $term->term_id,
                'name' => $term->name,
                'description' => $term->description,
                'count' => (int) $term->count,
                'link' => get_term_link($term),
            ];
        }, $terms);
    }

    /**
     * Featured entreprises for the homepage grid.
     *
     * @return array<int,array<string,mixed>>
     */
    protected function featuredEntreprises(int $limit = 12): array
    {
        $query = EntrepriseQuery::featured(['limit' => $limit]);
        if (!$query->have_posts()) {
            return [];
        }

        $items = [];
        foreach ($query->posts as $post) {
            $meta = $this->entrepriseMeta($post->ID);
            $badge = Pack::badge($meta['acf']['pack_premium'] ?? null);
            $villeNames = array_map(fn($term) => $term['name'], $meta['terms']['ville'] ?? []);
            $categorieNames = array_map(fn($term) => $term['name'], $meta['terms']['categorie'] ?? []);
            $villeLabel = implode(', ', $villeNames);
            $categorieLabel = implode(', ', $categorieNames);
            $openingStatus = null;

            if (function_exists('sl_get_open_status') && function_exists('sl_time_h')) {
                $status = sl_get_open_status($post->ID);
                if (is_array($status) && !empty($status)) {
                    $open = !empty($status['open']);
                    if ($open) {
                        $until = isset($status['closes_at']) && $status['closes_at'] instanceof \DateTimeImmutable
                            ? sl_time_h($status['closes_at'])
                            : '';
                        $base = $status['label'] ?? __('Ouvert', 'sage');
                        $label = $until ? $base . ' • ' . sprintf(__('ferme à %s', 'sage'), $until) : $base;
                    } else {
                        $nextChange = isset($status['next_change']) && $status['next_change'] instanceof \DateTimeImmutable
                            ? sl_time_h($status['next_change'])
                            : null;
                        $base = $status['label'] ?? __('Fermé', 'sage');
                        $label = $nextChange ? $base . ' • ' . sprintf(__('ouvre à %s', 'sage'), $nextChange) : $base;
                    }
                    $openingStatus = [
                        'open' => $open,
                        'label' => $label,
                    ];
                }
            }

            $items[] = [
                'id' => $meta['id'],
                'title' => $meta['title'],
                'link' => $meta['link'],
                'thumbnail' => get_the_post_thumbnail_url($post->ID, 'medium_large') ?: null,
                'summary' => wp_trim_words(wp_strip_all_tags($meta['acf']['description_courte'] ?? ''), 28, '…'),
                'badge' => $badge,
                'pack' => $meta['acf']['pack_premium'] ?? 'standard',
                'ville' => $meta['terms']['ville'] ?? [],
                'categories' => $meta['terms']['categorie'] ?? [],
                'ville_names' => $villeNames,
                'categorie_names' => $categorieNames,
                'ville_label' => $villeLabel,
                'categorie_label' => $categorieLabel,
                'verified' => $meta['acf']['verifie'] ?? false,
                'opening' => $openingStatus,
            ];
        }

        return $items;
    }

    /**
     * Testimonials pulled from dedicated CPT.
     *
     * @return array<int,array<string,mixed>>
     */
    protected function testimonials(int $limit = 6): array
    {
        if (!post_type_exists('testimonial')) {
            return [];
        }

        $posts = get_posts([
            'post_type' => 'testimonial',
            'posts_per_page' => $limit,
            'post_status' => 'publish',
            'orderby' => 'menu_order',
            'order' => 'ASC',
        ]);

        if (empty($posts)) {
            return [];
        }

        return array_map(function ($post) {
            $title = get_the_title($post);
            $role = get_post_meta($post->ID, 'testimonial_role', true);

            return [
                'id' => $post->ID,
                'title' => $title,
                'role' => $role ?: null,
                'content' => wp_trim_words(wp_strip_all_tags($post->post_content), 40, '…'),
                'avatar' => get_the_post_thumbnail_url($post, 'thumbnail') ?: null,
                'initial' => $title ? mb_strtoupper(mb_substr($title, 0, 1)) : '',
            ];
        }, $posts);
    }

    /**
     * Latest editorial posts for the newsroom section.
     *
     * @return array<int,array<string,mixed>>
     */
    protected function latestPosts(int $limit = 3): array
    {
        $posts = get_posts([
            'posts_per_page' => $limit,
            'post_status' => 'publish',
        ]);

        if (empty($posts)) {
            return [];
        }

        return array_map(function ($post) {
            return [
                'id' => $post->ID,
                'title' => get_the_title($post),
                'link' => get_permalink($post),
                'date' => get_the_date('', $post),
                'date_iso' => get_the_date(DATE_ATOM, $post),
                'author' => get_the_author_meta('display_name', $post->post_author),
                'excerpt' => wp_trim_words(wp_strip_all_tags(get_the_excerpt($post)), 22, '…'),
                'thumbnail' => get_the_post_thumbnail_url($post, 'large') ?: null,
            ];
        }, $posts);
    }
}

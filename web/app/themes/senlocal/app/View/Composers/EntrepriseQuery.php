<?php

namespace App\View\Composers;

use WP_Query;

class EntrepriseQuery
{
    /**
     * Featured Entreprises for the homepage.
     * Options: limit, categorie (id or slug), ville (id or slug).
     */
    public static function featured(array $opts = []): WP_Query
    {
        $limit = (int)($opts['limit'] ?? 12);
        $tax_query = ['relation' => 'AND'];

        if (!empty($opts['categorie'])) {
            $tax_query[] = [
                'taxonomy' => 'categorie',
                'field'    => is_numeric($opts['categorie']) ? 'term_id' : 'slug',
                'terms'    => $opts['categorie'],
            ];
        }
        if (!empty($opts['ville'])) {
            $tax_query[] = [
                'taxonomy' => 'ville',
                'field'    => is_numeric($opts['ville']) ? 'term_id' : 'slug',
                'terms'    => $opts['ville'],
            ];
        }

        $args = [
            'post_type'      => 'entreprise',
            'posts_per_page' => $limit,
            'post_status'    => 'publish',
            'tax_query'      => $tax_query,
            'meta_query'     => Pack::metaQueryActivePremium(),
            'orderby'        => 'date', // will be prepended by Pack::filterOrderByPack
            'order'          => 'DESC',
            'sen_pack_order' => true,
            'no_found_rows'  => true,
        ];

        add_filter('posts_clauses', [Pack::class, 'filterOrderByPack'], 10, 2);
        $q = new WP_Query($args);
        remove_filter('posts_clauses', [Pack::class, 'filterOrderByPack'], 10);

        return $q;
    }

    /**
     * Similar Entreprises based on shared categorie + ville + tags_service.
     * Falls back progressively if not enough results.
     */
    public static function similar(int $post_id, int $limit = 8): WP_Query
    {
        $exclude = [$post_id];
        $cats = wp_get_post_terms($post_id, 'categorie', ['fields' => 'ids']);
        $villes = wp_get_post_terms($post_id, 'ville', ['fields' => 'ids']);
        $tags = wp_get_post_terms($post_id, 'tags_service', ['fields' => 'ids']);

        $combos = [
            // strict
            [['categorie',$cats], ['ville',$villes], ['tags_service',$tags]],
            // no tags
            [['categorie',$cats], ['ville',$villes]],
            // cat only
            [['categorie',$cats]],
            // ville only
            [['ville',$villes]],
        ];

        foreach ($combos as $combo) {
            $tax_query = ['relation' => 'AND'];
            foreach ($combo as [$tax,$ids]) {
                if (!empty($ids)) {
                    $tax_query[] = ['taxonomy' => $tax, 'field' => 'term_id', 'terms' => $ids];
                }
            }
            if (count($tax_query) === 1) continue; // nothing to filter

            $args = [
                'post_type'      => 'entreprise',
                'post__not_in'   => $exclude,
                'posts_per_page' => $limit,
                'post_status'    => 'publish',
                'tax_query'      => $tax_query,
                'orderby'        => 'date',
                'order'          => 'DESC',
                'sen_pack_order' => true,
                'no_found_rows'  => true,
            ];

            add_filter('posts_clauses', [Pack::class, 'filterOrderByPack'], 10, 2);
            $q = new WP_Query($args);
            remove_filter('posts_clauses', [Pack::class, 'filterOrderByPack'], 10);

            if ($q->have_posts()) {
                return $q;
            }
        }

        // Fallback: latest premium anywhere
        add_filter('posts_clauses', [Pack::class, 'filterOrderByPack'], 10, 2);
        $q = new WP_Query([
            'post_type'      => 'entreprise',
            'post__not_in'   => $exclude,
            'posts_per_page' => $limit,
            'post_status'    => 'publish',
            'meta_query'     => Pack::metaQueryActivePremium(),
            'orderby'        => 'date',
            'order'          => 'DESC',
            'sen_pack_order' => true,
            'no_found_rows'  => true,
        ]);
        remove_filter('posts_clauses', [Pack::class, 'filterOrderByPack'], 10);
        return $q;
    }
}

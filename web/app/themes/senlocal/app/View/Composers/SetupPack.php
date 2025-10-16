<?php

namespace App\View\Composers;

/**
 * Setup hooks for pack-aware ordering in archives and search.
 */
class SetupPack
{
    public static function register(): void
    {
        add_action('pre_get_posts', [self::class, 'boostArchiveOrder']);
        add_filter('posts_clauses', [Pack::class, 'filterOrderByPack'], 10, 2);
    }

    /**
     * For entreprise archives and search, enable pack ordering & sensible defaults.
     */
    public static function boostArchiveOrder(\WP_Query $q): void
    {
        if (is_admin() || !$q->is_main_query()) {
            return;
        }

        // Archive entreprise
        if ($q->is_post_type_archive('entreprise')) {
            $q->set('orderby', 'date');
            $q->set('order', 'DESC');
            $q->set('sen_pack_order', true);
            return;
        }

        // Taxonomy pages involving entreprise
        $taxes = ['categorie','ville','tags_service'];
        foreach ($taxes as $t) {
            if ($q->is_tax($t)) {
                $q->set('orderby', 'date');
                $q->set('order', 'DESC');
                $q->set('sen_pack_order', true);
                return;
            }
        }

        // Search results including entreprise
        if ($q->is_search()) {
            // Do not restrict to entreprise, but still boost pack when present
            $q->set('sen_pack_order', true);
        }
    }
}

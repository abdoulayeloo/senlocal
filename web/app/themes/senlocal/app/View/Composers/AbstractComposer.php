<?php

namespace App\View\Composers;

use Roots\Acorn\View\Composer;

/**
 * Base composer with small utilities shared by page-specific composers.
 */
abstract class AbstractComposer extends Composer
{
    /**
     * Build a simple pagination array from the global query.
     */
    protected function pagination(?\WP_Query $query = null): array
    {
        $q = $query ?: $GLOBALS['wp_query'] ?? null;
        if (!$q instanceof \WP_Query) {
            return [];
        }
        $current = max(1, (int) get_query_var('paged', 1));
        $total   = (int) $q->max_num_pages;
        return [
            'current' => $current,
            'total'   => $total,
            'has_prev'=> $current > 1,
            'has_next'=> $total > $current,
            'prev_link' => $current > 1 ? get_pagenum_link($current - 1) : null,
            'next_link' => $total > $current ? get_pagenum_link($current + 1) : null,
        ];
    }

    /**
     * Small helper to fetch a list of terms for a taxonomy with selection state.
     */
    protected function termsFor(string $taxonomy): array
    {
        $terms = get_terms([
            'taxonomy'   => $taxonomy,
            'hide_empty' => false,
        ]);
        if (is_wp_error($terms)) {
            return [];
        }
        $current = get_query_var($taxonomy);
        $selected = null;
        if ($current) {
            $selected_term = get_term_by(is_numeric($current) ? 'id' : 'slug', $current, $taxonomy);
            $selected = $selected_term ? (int) $selected_term->term_id : null;
        }
        return array_map(function($t) use ($selected){
            return [
                'id'    => (int) $t->term_id,
                'name'  => $t->name,
                'slug'  => $t->slug,
                'count' => (int) $t->count,
                'link'  => get_term_link($t),
                'selected' => $selected === (int) $t->term_id,
            ];
        }, $terms);
    }

    /**
     * Context flags handy in Blade.
     */
    protected function queryContext(): array
    {
        return [
            'is_front'   => is_front_page(),
            'is_home'    => is_home(),
            'is_page'    => is_page(),
            'is_single'  => is_single(),
            'is_archive' => is_archive(),
            'is_search'  => is_search(),
            'is_tax'     => is_tax(),
        ];
    }
}

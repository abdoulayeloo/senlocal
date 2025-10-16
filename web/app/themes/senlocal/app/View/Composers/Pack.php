<?php

namespace App\View\Composers;

/**
 * Pack utilities for Entreprise CPT.
 */
class Pack
{
    public const SLUGS = ['standard', 'silver', 'gold', 'platinum'];

    /** Map pack => rank (higher is better) */
    public static function rank(?string $pack): int
    {
        $pack = $pack ? strtolower($pack) : 'standard';
        switch ($pack) {
            case 'platinum': return 3;
            case 'gold':     return 2;
            case 'silver':   return 1;
            default:         return 0;
        }
    }

    /** Active if pack != standard AND within start/end (if defined) */
    public static function isActive(int $post_id): bool
    {
        $pack  = get_field('pack_premium', $post_id) ?: 'standard';
        if ($pack === 'standard') return false;

        $start = get_field('date_debut_pack', $post_id) ?: null; // Y-m-d
        $end   = get_field('date_fin_pack', $post_id) ?: null;   // Y-m-d
        $today = current_time('Y-m-d');

        if ($start && $today < $start) return false;
        if ($end && $today > $end)     return false;

        return true;
    }

    /** Returns a badge array suitable for Blade (label + classes) */
    public static function badge(?string $pack): array
    {
        $pack = $pack ? strtolower($pack) : 'standard';
        $map = [
            'standard' => ['label' => 'Standard', 'class' => 'bg-gray-100 text-gray-700'],
            'silver'   => ['label' => 'Premium Silver', 'class' => 'bg-slate-100 text-slate-800'],
            'gold'     => ['label' => 'Premium Gold', 'class' => 'bg-amber-100 text-amber-800'],
            'platinum' => ['label' => 'Premium Platinum', 'class' => 'bg-indigo-100 text-indigo-800'],
        ];
        return $map[$pack] ?? $map['standard'];
    }

    /** Eligible for homepage feature (Gold & Platinum active by default) */
    public static function eligibleHomepage(int $post_id): bool
    {
        $pack = get_field('pack_premium', $post_id) ?: 'standard';
        if (!in_array($pack, ['gold','platinum'], true)) return false;
        return self::isActive($post_id);
    }

    /** Meta query to ensure active premium window */
    public static function metaQueryActivePremium(): array
    {
        $today = current_time('Y-m-d');
        return [
            'relation' => 'AND',
            [
                'key'     => 'pack_premium',
                'value'   => 'standard',
                'compare' => '!='
            ],
            // start <= today OR empty
            [
                'relation' => 'OR',
                [
                    'key'     => 'date_debut_pack',
                    'value'   => $today,
                    'compare' => '<=',
                    'type'    => 'DATE',
                ],
                [
                    'key'     => 'date_debut_pack',
                    'compare' => 'NOT EXISTS',
                ],
                [
                    'key'     => 'date_debut_pack',
                    'value'   => '',
                    'compare' => '=',
                ],
            ],
            // end >= today OR empty
            [
                'relation' => 'OR',
                [
                    'key'     => 'date_fin_pack',
                    'value'   => $today,
                    'compare' => '>=',
                    'type'    => 'DATE',
                ],
                [
                    'key'     => 'date_fin_pack',
                    'compare' => 'NOT EXISTS',
                ],
                [
                    'key'     => 'date_fin_pack',
                    'value'   => '',
                    'compare' => '=',
                ],
            ],
        ];
    }

    /**
     * Add an ORDER BY FIELD(pack) clause to prioritize packs (platinum>gold>silver>standard).
     * Enable it by setting query var `sen_pack_order` truthy on WP_Query.
     */
    public static function filterOrderByPack(array $clauses, \WP_Query $query): array
    {
        if (!$query->get('sen_pack_order')) {
            return $clauses;
        }
        global $wpdb;
        $meta_alias = 'pm_pack_premium';

        // Join postmeta for pack_premium if not already present
        if (strpos($clauses['join'], $meta_alias) === false) {
            $clauses['join'] .= " LEFT JOIN {$wpdb->postmeta} AS {$meta_alias} ON ({$wpdb->posts}.ID = {$meta_alias}.post_id AND {$meta_alias}.meta_key = 'pack_premium')";
        }

        // Prepend ORDER BY with FIELD on pack values
        $ordering = "FIELD({$meta_alias}.meta_value,'platinum','gold','silver','standard') DESC";
        if (!empty($clauses['orderby'])) {
            $clauses['orderby'] = $ordering . ', ' . $clauses['orderby'];
        } else {
            $clauses['orderby'] = $ordering;
        }
        return $clauses;
    }
}

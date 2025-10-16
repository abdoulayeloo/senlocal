<?php

namespace App\View\Composers;


/**
 * Archive for Entreprise CPT with filters and counts.
 */
class ArchiveEntreprise extends AbstractComposer
{
    use Cpt;

    /** @var array<int,string> */
    protected static $views = [
        'archive-entreprise',
    ];

    public function with(): array
    {
        global $wp_query;

        // Active filters
        $active = [
            'categorie' => get_query_var('categorie') ?: null,
            'ville'     => get_query_var('ville') ?: null,
            'services'  => get_query_var('tags_service') ?: null,
            's'         => get_query_var('s') ?: null,
        ];

        return [
            'title'        => post_type_archive_title('', false) ?: __('Entreprises', 'sage'),
            'filters'      => [
                'categories' => $this->termsFor('categorie'),
                'villes'     => $this->termsFor('ville'),
                'services'   => $this->termsFor('tags_service'),
                'active'     => $active,
            ],
            'count'        => (int) ($wp_query->found_posts ?? 0),
            'pagination'   => $this->pagination($wp_query),
        ];
    }
}

<?php

namespace App\View\Composers;

use Roots\Acorn\View\Composer;

/**
 * Taxonomy archive composer.
 */
class Taxonomy extends AbstractComposer
{
    /** @var array<int,string> */
    protected static $views = [
        'taxonomy',
        'taxonomy-*',
    ];

    public function with(): array
    {
        $term = get_queried_object();
        return [
            'title'       => $term && isset($term->name) ? $term->name : get_the_archive_title(),
            'description' => term_description(),
        ];
    }
}

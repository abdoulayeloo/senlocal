<?php

namespace App\View\Composers;


/**
 * Search results composer.
 */
class Search extends AbstractComposer
{
    /** @var array<int,string> */
    protected static $views = [
        'search',
    ];

    public function with(): array
    {
        global $wp_query;
        return [
            'query'      => get_search_query(),
            'count'      => (int) ($wp_query->found_posts ?? 0),
            'pagination' => $this->pagination($wp_query),
        ];
    }
}

<?php

namespace App\View\Composers;


/**
 * Generic WordPress archive composer (posts, pages, etc.).
 */
class Archive extends AbstractComposer
{
    /** @var array<int,string> */
    protected static $views = [
        'archive',
        'archive-*',
    ];

    public function with(): array
    {
        global $wp_query;
        return [
            'title'      => get_the_archive_title(),
            'description'=> get_the_archive_description(),
            'pagination' => $this->pagination($wp_query),
        ];
    }
}

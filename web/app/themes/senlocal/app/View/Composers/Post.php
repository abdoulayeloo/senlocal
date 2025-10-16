<?php

namespace App\View\Composers;

/**
 * Single WP Post composer.
 */
class Post extends AbstractComposer
{
    /** @var array<int,string> */
    protected static $views = [
        'single',
    ];

    public function with(): array
    {
        return [
            'author' => get_the_author_meta('display_name', (int) get_post_field('post_author', get_the_ID())),
            'date'   => get_the_date('', get_the_ID()),
            'title'  => $this->title(),
            'pagination' => $this->pagination(),
        ];
    }

    public function title(): string
    {
        if ($this->view->name() !== 'partials.page-header') {
            return get_the_title();
        }

        if (is_home()) {
            if ($home = get_option('page_for_posts', true)) {
                return get_the_title($home);
            }

            return __('Latest Posts', 'sage');
        }

        if (is_archive()) {
            return get_the_archive_title();
        }

        if (is_search()) {
            return sprintf(
                /* translators: %s is replaced with the search query */
                __('Search Results for %s', 'sage'),
                get_search_query()
            );
        }

        if (is_404()) {
            return __('Not Found', 'sage');
        }

        return get_the_title();
    }
}

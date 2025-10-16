<?php

namespace App\View\Composers;

use Roots\Acorn\View\Composer;

/**
 * Generic page composer.
 */
class Page extends AbstractComposer
{
    /** @var array<int,string> */
    protected static $views = [
        'page',
        'page-*',
    ];

    public function with(): array
    {
        return [
            'title' => get_the_title(),
        ];
    }
}

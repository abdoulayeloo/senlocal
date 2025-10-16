<?php

namespace App\View\Composers;

use Roots\Acorn\View\Composer;

/**
 * Generic single template composer (fallback).
 */
class Single extends AbstractComposer
{
    /** @var array<int,string> */
    protected static $views = [
        'single',
    ];

    public function with(): array
    {
        return [
            'title' => get_the_title(),
        ];
    }
}

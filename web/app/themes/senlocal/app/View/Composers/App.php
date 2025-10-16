<?php

namespace App\View\Composers;

/**
 * Global data available to all views.
 */
class App extends AbstractComposer
{
    /**
     * Bind to all views.
     * @var array<int,string>
     */
    protected static $views = ['*'];

    /**
     * @return array<string,mixed>
     */
    public function with(): array
    {
        return [
            'siteName'   => get_bloginfo('name'),
            'primaryMenu'=> has_nav_menu('primary_navigation') ? wp_get_nav_menu_items(get_nav_menu_locations()['primary_navigation']) : [],
            'footerMenu' => has_nav_menu('footer_navigation') ? wp_get_nav_menu_items(get_nav_menu_locations()['footer_navigation']) : [],
            'context'    => $this->queryContext(),
        ];
    }
}

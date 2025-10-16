<?php

/**
 * Load pack helpers and register filters.
 */

namespace App\View\Composers;



require_once __DIR__ . 'Pack.php';
require_once __DIR__ . 'SetupPack.php';
require_once __DIR__ . 'EntrepriseQuery.php';

add_action('after_setup_theme', function () {
    SetupPack::register();
});

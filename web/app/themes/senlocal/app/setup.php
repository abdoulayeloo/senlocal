<?php

/**
 * Theme setup.
 */

namespace App;

use Env\Env;
use Illuminate\Support\Facades\Vite;

/**
 * Inject styles into the block editor.
 *
 * @return array
 */
add_filter('block_editor_settings_all', function ($settings) {
    $style = Vite::asset('resources/css/editor.css');

    $settings['styles'][] = [
        'css' => "@import url('{$style}')",
    ];

    return $settings;
});

/**
 * Inject scripts into the block editor.
 *
 * @return void
 */
add_filter('admin_head', function () {
    if (! get_current_screen()?->is_block_editor()) {
        return;
    }

    $dependencies = json_decode(Vite::content('editor.deps.json'));

    foreach ($dependencies as $dependency) {
        if (! wp_script_is($dependency)) {
            wp_enqueue_script($dependency);
        }
    }

    echo Vite::withEntryPoints([
        'resources/js/editor.js',
    ])->toHtml();
});

/**
 * Use the generated theme.json file.
 *
 * @return string
 */
add_filter('theme_file_path', function ($path, $file) {
    return $file === 'theme.json'
        ? public_path('build/assets/theme.json')
        : $path;
}, 10, 2);

/**
 * Register the initial theme setup.
 *
 * @return void
 */
add_action('after_setup_theme', function () {
    /**
     * Disable full-site editing support.
     *
     * @link https://wptavern.com/gutenberg-10-5-embeds-pdfs-adds-verse-block-color-options-and-introduces-new-patterns
     */
    remove_theme_support('block-templates');

    /**
     * Register the navigation menus.
     *
     * @link https://developer.wordpress.org/reference/functions/register_nav_menus/
     */
    register_nav_menus([
        'primary_navigation' => __('Primary Navigation', 'sage'),
    ]);

    /**
     * Disable the default block patterns.
     *
     * @link https://developer.wordpress.org/block-editor/developers/themes/theme-support/#disabling-the-default-block-patterns
     */
    remove_theme_support('core-block-patterns');

    /**
     * Enable plugins to manage the document title.
     *
     * @link https://developer.wordpress.org/reference/functions/add_theme_support/#title-tag
     */
    add_theme_support('title-tag');

    /**
     * Enable post thumbnail support.
     *
     * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
     */
    add_theme_support('post-thumbnails');

    /**
     * Enable responsive embed support.
     *
     * @link https://developer.wordpress.org/block-editor/how-to-guides/themes/theme-support/#responsive-embedded-content
     */
    add_theme_support('responsive-embeds');

    /**
     * Enable HTML5 markup support.
     *
     * @link https://developer.wordpress.org/reference/functions/add_theme_support/#html5
     */
    add_theme_support('html5', [
        'caption',
        'comment-form',
        'comment-list',
        'gallery',
        'search-form',
        'script',
        'style',
    ]);

    /**
     * Enable selective refresh for widgets in customizer.
     *
     * @link https://developer.wordpress.org/reference/functions/add_theme_support/#customize-selective-refresh-widgets
     */
    add_theme_support('customize-selective-refresh-widgets');
}, 20);

/**
 * Register the theme sidebars.
 *
 * @return void
 */
add_action('widgets_init', function () {
    $config = [
        'before_widget' => '<section class="widget %1$s %2$s">',
        'after_widget' => '</section>',
        'before_title' => '<h3>',
        'after_title' => '</h3>',
    ];

    register_sidebar([
        'name' => __('Primary', 'sage'),
        'id' => 'sidebar-primary',
    ] + $config);

    register_sidebar([
        'name' => __('Footer', 'sage'),
        'id' => 'sidebar-footer',
    ] + $config);
});

add_action('init', function () {
    register_post_type('entreprise', [
        'labels' => [
            'name' => 'Entreprises',
            'singular_name' => 'Entreprise',
            'add_new' => 'Ajouter une entreprise',
            'add_new_item' => 'Nouvelle entreprise',
            'edit_item' => 'Modifier l’entreprise',
            'view_item' => 'Voir l’entreprise',
            'search_items' => 'Rechercher une entreprise',
        ],
        'public' => true,
        'has_archive' => true,
        'menu_icon' => 'dashicons-store',
        'supports' => ['title', 'editor', 'thumbnail', 'excerpt'],
        'rewrite' => ['slug' => 'entreprises'],
        'show_in_rest' => true,
    ]);
});

// Catégorie d’activité
register_taxonomy('categorie', 'entreprise', [
    'label' => 'Catégories',
    'hierarchical' => true,
    'rewrite' => ['slug' => 'categorie'],
    'show_in_rest' => true,
]);

// Ville
register_taxonomy('ville', 'entreprise', [
    'label' => 'Villes',
    'hierarchical' => false,
    'rewrite' => ['slug' => 'ville'],
    'show_in_rest' => true,
]);

// Tags de service
register_taxonomy('tags_service', 'entreprise', [
    'label' => 'Services',
    'hierarchical' => false,
    'rewrite' => ['slug' => 'services'],
    'show_in_rest' => true,
]);

/**
 * Groupe de champs ACF pour le CPT "entreprise".
 */

add_action('acf/init', function () {
    if (!function_exists('acf_add_local_field_group')) return;

    acf_add_local_field_group([
        'key' => 'group_sen_entreprise',
        'title' => 'Fiche Entreprise',
        'fields' => [

            // Identité
            [
                'key' => 'field_sen_raison_sociale',
                'label' => 'Raison sociale',
                'name'  => 'raison_sociale',
                'type'  => 'text',
                'required' => 1,
                'wrapper' => ['width' => '100'],
            ],
            [
                'key' => 'field_sen_logo',
                'label' => 'Logo',
                'name'  => 'logo',
                'type'  => 'image',
                'return_format' => 'array',
                'preview_size'  => 'medium',
                'library'       => 'all',
                'wrapper' => ['width' => '100'],
            ],

            // Descriptions
            [
                'key' => 'field_sen_desc_courte',
                'label' => 'Description courte',
                'name'  => 'description_courte',
                'type'  => 'textarea',
                'maxlength' => 220,
                'rows' => 3,
            ],
            [
                'key' => 'field_sen_desc_longue',
                'label' => 'Description longue',
                'name'  => 'description_longue',
                'type'  => 'wysiwyg',
                'tabs'  => 'all',
                'toolbar' => 'full',
                'media_upload' => 1,
            ],

            // Contacts
            [
                'key' => 'field_sen_telephone',
                'label' => 'Téléphone',
                'name'  => 'telephone',
                'type'  => 'text',
                'placeholder' => '+221…',
                'wrapper' => ['width' => '33'],
            ],
            [
                'key' => 'field_sen_whatsapp',
                'label' => 'WhatsApp',
                'name'  => 'whatsapp',
                'type'  => 'text',
                'placeholder' => '+221…',
                'wrapper' => ['width' => '33'],
            ],
            [
                'key' => 'field_sen_email',
                'label' => 'E-mail',
                'name'  => 'email_contact',
                'type'  => 'email',
                'wrapper' => ['width' => '33'],
            ],
            [
                'key' => 'field_sen_siteweb',
                'label' => 'Site web',
                'name'  => 'site_web',
                'type'  => 'url',
                'wrapper' => ['width' => '100'],
            ],
            [
                'key'   => 'field_sen_social_group',
                'label' => 'Réseaux sociaux',
                'name'  => 'social_links',
                'type'  => 'group',
                'instructions' => 'Renseigne uniquement les réseaux utilisés.',
                'layout' => 'block',
                'wrapper' => ['width' => '100'],
                'sub_fields' => [

                    [
                        'key' => 'field_sen_social_facebook',
                        'label' => 'Facebook',
                        'name'  => 'facebook',
                        'type'  => 'url',
                        'placeholder' => 'https://facebook.com/...',
                        'wrapper' => ['width' => '33'],
                    ],
                    [
                        'key' => 'field_sen_social_instagram',
                        'label' => 'Instagram',
                        'name'  => 'instagram',
                        'type'  => 'url',
                        'placeholder' => 'https://instagram.com/...',
                        'wrapper' => ['width' => '33'],
                    ],
                    [
                        'key' => 'field_sen_social_linkedin',
                        'label' => 'LinkedIn',
                        'name'  => 'linkedin',
                        'type'  => 'url',
                        'placeholder' => 'https://www.linkedin.com/...',
                        'wrapper' => ['width' => '33'],
                    ],
                    [
                        'key' => 'field_sen_social_x',
                        'label' => 'X (Twitter)',
                        'name'  => 'x',
                        'type'  => 'url',
                        'placeholder' => 'https://x.com/...',
                        'wrapper' => ['width' => '33'],
                    ],
                    [
                        'key' => 'field_sen_social_tiktok',
                        'label' => 'TikTok',
                        'name'  => 'tiktok',
                        'type'  => 'url',
                        'placeholder' => 'https://www.tiktok.com/@...',
                        'wrapper' => ['width' => '33'],
                    ],
                    [
                        'key' => 'field_sen_social_youtube',
                        'label' => 'YouTube',
                        'name'  => 'youtube',
                        'type'  => 'url',
                        'placeholder' => 'https://youtube.com/@... ou /c/...',
                        'wrapper' => ['width' => '33'],
                    ],
                    [
                        'key' => 'field_sen_social_whatsapp',
                        'label' => 'WhatsApp (lien direct)',
                        'name'  => 'whatsapp',
                        'type'  => 'url',
                        'placeholder' => 'https://wa.me/221XXXXXXXXX',
                        'wrapper' => ['width' => '33'],
                    ],
                    [
                        'key' => 'field_sen_social_telegram',
                        'label' => 'Telegram',
                        'name'  => 'telegram',
                        'type'  => 'url',
                        'placeholder' => 'https://t.me/...',
                        'wrapper' => ['width' => '33'],
                    ],
                    [
                        'key' => 'field_sen_social_site',
                        'label' => 'Site web (secondaire)',
                        'name'  => 'site_secondaire',
                        'type'  => 'url',
                        'placeholder' => 'https://...',
                        'wrapper' => ['width' => '33'],
                    ],

                ],
            ],

            // Localisation
            [
                'key' => 'field_sen_adresse',
                'label' => 'Adresse complète',
                'name'  => 'adresse',
                'type'  => 'text',
                'rows'  => 3,
                'wrapper' => ['width' => '100'],
            ],

            // Exploitation
            [
                'key' => 'field_sen_horaires',
                'label' => 'Horaires d’ouverture',
                'name'  => 'horaires',
                'type'  => 'textarea',
                'rows'  => 3,
                'instructions' => 'Ex. Lun–Ven 09:00–18:00 ; Sam 10:00–14:00',
                'wrapper' => ['width' => '100'],
            ],
            [
                'key' => 'field_sen_paiement',
                'label' => 'Moyens de paiement',
                'name'  => 'paiement',
                'type'  => 'select',
                'choices' => [
                    'cash' => 'Espèces',
                    'wave' => 'Wave',
                    'om'   => 'Orange Money',
                    'free' => 'Free Money',
                    'card' => 'Carte bancaire (Visa/Mastercard)',
                    'virement' => 'Virement',
                ],
                'multiple' => 1,
                'ui' => 1,
                'wrapper' => ['width' => '100'],
            ],
            [
                'key' => 'field_sen_prix',
                'label' => 'Fourchette de prix',
                'name'  => 'prix',
                'type'  => 'text',
                'placeholder' => 'Ex. 5 000–10 000 FCFA',
                'wrapper' => ['width' => '50'],
            ],

            // Vérification & Premium
            [
                'key' => 'field_sen_verifie',
                'label' => 'Compte vérifié',
                'name'  => 'verifie',
                'type'  => 'true_false',
                'ui'    => 1,
                'wrapper' => ['width' => '25'],
            ],
            [
                'key' => 'field_sen_pack',
                'label' => 'Pack',
                'name'  => 'pack_premium',
                'type'  => 'select',
                'choices' => [
                    'standard' => 'Standard',
                    'silver'   => 'Premium Silver',
                    'gold'     => 'Premium Gold',
                    'platinum' => 'Premium Platinum',
                ],
                'default_value' => 'standard',
                'ui' => 1,
                'wrapper' => ['width' => '25'],
            ],
            [
                'key' => 'field_sen_date_debut',
                'label' => 'Début du pack',
                'name'  => 'date_debut_pack',
                'type'  => 'date_picker',
                'display_format' => 'd/m/Y',
                'return_format'  => 'Y-m-d',
                'wrapper' => ['width' => '25'],
                'conditional_logic' => [
                    [['field' => 'field_sen_pack', 'operator' => '!=', 'value' => 'standard']]
                ],
            ],
            [
                'key' => 'field_sen_date_fin',
                'label' => 'Fin du pack',
                'name'  => 'date_fin_pack',
                'type'  => 'date_picker',
                'display_format' => 'd/m/Y',
                'return_format'  => 'Y-m-d',
                'wrapper' => ['width' => '25'],
                'conditional_logic' => [
                    [['field' => 'field_sen_pack', 'operator' => '!=', 'value' => 'standard']]
                ],
            ],

            // Statistiques (basiques)
            [
                'key' => 'field_sen_stat_vues',
                'label' => 'Vues (compteur)',
                'name'  => 'stat_vues',
                'type'  => 'number',
                'default_value' => 0,
                'wrapper' => ['width' => '25'],
            ],
            [
                'key' => 'field_sen_stat_clics',
                'label' => 'Clics CTA (compteur)',
                'name'  => 'stat_clics',
                'type'  => 'number',
                'default_value' => 0,
                'wrapper' => ['width' => '25'],
            ],
            [
                'key' => 'field_sen_map',
                'label' => 'Géolocalisation',
                'name'  => 'localisation',
                'type'  => 'google_map',
                'center' => ['lat' => 14.6937, 'lng' => -17.4441], // Dakar par défaut
                'zoom'   => 14,
                'wrapper' => ['width' => '100'],
            ],
        ],

        'location' => [
            [
                ['param' => 'post_type', 'operator' => '==', 'value' => 'entreprise'],
            ],
        ],
        'menu_order' => 0,
        'position'   => 'normal',
        'style'      => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'active' => true,
        'show_in_rest' => 0,
    ]);
});

/**
 * (Optionnel) Clé API Google Maps pour le champ google_map ACF.
 * Décommente et remplace YOUR_GOOGLE_MAPS_API_KEY si tu utilises le champ.
 */
add_action('acf/init', function () {
    acf_update_setting('google_api_key', Env::get('GOOGLE_MAPS_API_KEY'));
});

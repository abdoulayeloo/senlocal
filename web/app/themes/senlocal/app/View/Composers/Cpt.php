<?php

namespace App\View\Composers;

/**
 * Small reusable utilities for the Entreprise CPT.
 */
trait Cpt
{
    /**
     * Returns the CPT slug used throughout the project.
     */
    protected function entrepriseType(): string
    {
        return 'entreprise';
    }

    protected function entrepriseMeta(int $post_id): array
    {
        // Guard if ACF is not present.
        if (!function_exists('get_field')) {
            return [];
        }
        $fields = [
            'raison_sociale' => get_field('raison_sociale', $post_id),
            'logo'           => get_field('logo', $post_id),
            'description_courte' => get_field('description_courte', $post_id),
            'description_longue' => get_field('description_longue', $post_id),
            'telephone'      => get_field('telephone', $post_id),
            'whatsapp'       => get_field('whatsapp', $post_id),
            'email_contact'  => get_field('email_contact', $post_id),
            'site_web'       => get_field('site_web', $post_id),
            'social_links'   => get_field('social_links', $post_id),
            'adresse'        => get_field('adresse', $post_id),
            'localisation'   => get_field('localisation', $post_id),
            'horaires'       => get_field('horaires', $post_id),
            'paiement'       => get_field('paiement', $post_id),
            'prix'           => get_field('prix', $post_id),
            'verifie'        => (bool) get_field('verifie', $post_id),
            'pack_premium'   => get_field('pack_premium', $post_id) ?: 'standard',
            'date_debut_pack'=> get_field('date_debut_pack', $post_id),
            'date_fin_pack'  => get_field('date_fin_pack', $post_id),
            'stat_vues'      => (int) (get_field('stat_vues', $post_id) ?: 0),
            'stat_clics'     => (int) (get_field('stat_clics', $post_id) ?: 0),
            'whatsapp_link'  => get_field('whatsapp_link', $post_id),
        ];

        // Taxonomies
        $terms = [
            'categorie'    => wp_get_post_terms($post_id, 'categorie'),
            'ville'        => wp_get_post_terms($post_id, 'ville'),
            'tags_service' => wp_get_post_terms($post_id, 'tags_service'),
        ];

        return [
            'id'     => $post_id,
            'title'  => get_the_title($post_id),
            'link'   => get_permalink($post_id),
            'terms'  => array_map(function($arr){
                if (is_wp_error($arr)) return [];
                return array_map(function($t){
                    return [
                        'id'   => (int) $t->term_id,
                        'name' => $t->name,
                        'slug' => $t->slug,
                        'link' => get_term_link($t),
                    ];
                }, $arr);
            }, $terms),
            'acf'    => $fields,
        ];
    }
}

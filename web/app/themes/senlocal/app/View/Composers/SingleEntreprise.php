<?php

namespace App\View\Composers;


/**
 * Single composer for Entreprise CPT.
 */
class SingleEntreprise extends AbstractComposer
{
    use Cpt;

    /** @var array<int,string> */
    protected static $views = [
        'single-entreprise',
        'template-etablissement',
    ];

    public function with(): array
    {
        $id = get_the_ID();
        $meta = $this->entrepriseMeta($id);

        $opening = function_exists('App\sl_get_open_status')
            ? sl_get_open_status($id)
            : ['open' => null, 'label' => null, 'next_change' => null];

        // Similar entreprises (pack-boosted)
        $similar_q = EntrepriseQuery::similar($id, 8);
        $similar = array_map(function($p){
            $pid = $p->ID;
            return [
                'id'    => $pid,
                'title' => get_the_title($pid),
                'link'  => get_permalink($pid),
                'thumb' => get_the_post_thumbnail_url($pid, 'thumbnail'),
                'pack'  => get_field('pack_premium', $pid) ?: 'standard',
                'badge' => Pack::badge(get_field('pack_premium', $pid)),
            ];
        }, $similar_q->posts);

        // Badge for current
        $meta['pack_badge'] = Pack::badge($meta['acf']['pack_premium'] ?? 'standard');
        $meta['pack_active'] = Pack::isActive($id);

        return [
            'entreprise' => $meta,
            'opening'    => $opening,
            'similar_entreprises' => $similar,
        ];
    }
}

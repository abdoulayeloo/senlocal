<?php

/**
 * Helpers "Opening Hours" – senlocal.com
 * Structure ACF attendue (group 'horaires'):
 *  - jours: lun/mar/mer/jeu/ven/sam/dim -> group { open(bool), start(H:i), end(H:i), pause_start(H:i), pause_end(H:i) }
 *  - exceptions -> group { date1 (Y-m-d), note1, date2, note2, date3, note3 } // ferme toute la journée
 */

/**
 * Récupère les horaires ACF normalisés
 */
function sl_get_hours(?int $post_id = null): array
{
    $post_id = $post_id ?: get_the_ID();
    $data = get_field('horaires', $post_id) ?: [];
    $days = ['lun', 'mar', 'mer', 'jeu', 'ven', 'sam', 'dim'];
    $out = ['days' => [], 'exceptions' => []];

    foreach ($days as $d) {
        $row = $data[$d] ?? [];
        $out['days'][$d] = [
            'open'        => !empty($row['open']),
            'start'       => isset($row['start']) ? trim($row['start']) : null,       // "09:00"
            'end'         => isset($row['end']) ? trim($row['end']) : null,           // "18:00"
            'pause_start' => isset($row['pause_start']) ? trim($row['pause_start']) : null,
            'pause_end'   => isset($row['pause_end']) ? trim($row['pause_end']) : null,
        ];
    }

    // exceptions fermées (journée entière)
    $exc = $data['exceptions'] ?? [];
    foreach ([1, 2, 3] as $i) {
        $key = "date{$i}";
        if (!empty($exc[$key])) {
            $out['exceptions'][] = $exc[$key]; // format Y-m-d
        }
    }

    return $out;
}

/**
 * Convertit "H:i" en DateTimeImmutable du jour donné
 */
function sl_at_time(string $ymd, ?string $time, DateTimeZone $tz): ?DateTimeImmutable
{
    if (!$time) return null;
    return new DateTimeImmutable("{$ymd} {$time}:00", $tz);
}

/**
 * Calcule le statut d’ouverture à un instant T (par défaut maintenant).
 * Gère une seule plage avec éventuelle pause. Pas d’horaires “nuit suivante”.
 */
function sl_get_open_status(?int $post_id = null, ?DateTimeImmutable $now = null): array
{
    $tz   = new DateTimeZone('Africa/Dakar');
    $now  = $now ?: new DateTimeImmutable('now', $tz);
    $Ymd  = $now->format('Y-m-d');
    $w    = (int) $now->format('N'); // 1=lundi … 7=dimanche
    $map  = [1 => 'lun', 2 => 'mar', 3 => 'mer', 4 => 'jeu', 5 => 'ven', 6 => 'sam', 7 => 'dim'];

    $hours = sl_get_hours($post_id);

    // Exception du jour => fermé toute la journée
    if (in_array($Ymd, $hours['exceptions'] ?? [], true)) {
        return [
            'open' => false,
            'label' => 'Fermé (exception)',
            'next_change' => sl_next_opening($post_id, $now),
            'intervals_today' => [],
        ];
    }

    $key = $map[$w];
    $row = $hours['days'][$key] ?? null;
    if (!$row || empty($row['open']) || !$row['start'] || !$row['end']) {
        return [
            'open' => false,
            'label' => 'Fermé',
            'next_change' => sl_next_opening($post_id, $now),
            'intervals_today' => [],
        ];
    }

    $start = sl_at_time($Ymd, $row['start'], $tz);
    $end   = sl_at_time($Ymd, $row['end'],   $tz);
    // Pause éventuelle
    $p1 = $row['pause_start'] ? sl_at_time($Ymd, $row['pause_start'], $tz) : null;
    $p2 = $row['pause_end']   ? sl_at_time($Ymd, $row['pause_end'],   $tz) : null;

    // Construire les créneaux actifs de la journée
    $intervals = [];
    if ($start && $end && $end > $start) {
        if ($p1 && $p2 && $p2 > $p1 && $p1 > $start && $p2 < $end) {
            // Matin + Après-midi
            $intervals[] = [$start, $p1];
            $intervals[] = [$p2, $end];
        } else {
            $intervals[] = [$start, $end];
        }
    }

    // Déterminer si "now" tombe dans un intervalle
    $open = false;
    $closes_at = null;
    foreach ($intervals as [$a, $b]) {
        if ($now >= $a && $now < $b) {
            $open = true;
            $closes_at = $b;
            break;
        }
    }

    if ($open) {
        $mins = (int) floor(($closes_at->getTimestamp() - $now->getTimestamp()) / 60);
        $label = $mins <= 15 ? 'Ferme bientôt' : 'Ouvert';
        return [
            'open' => true,
            'label' => $label,
            'closes_at' => $closes_at,
            'next_change' => $closes_at,
            'intervals_today' => $intervals,
        ];
    }

    // sinon prochain opening
    $next = sl_next_opening($post_id, $now);
    return [
        'open' => false,
        'label' => 'Fermé',
        'next_change' => $next,
        'intervals_today' => $intervals,
    ];
}

/**
 * Donne la prochaine date/heure d’ouverture >= now (sur 8 jours max).
 */
function sl_next_opening(?int $post_id = null, ?DateTimeImmutable $now = null): ?DateTimeImmutable
{
    $tz   = new DateTimeZone('Africa/Dakar');
    $now  = $now ?: new DateTimeImmutable('now', $tz);
    $hours = sl_get_hours($post_id);
    $map  = [1 => 'lun', 2 => 'mar', 3 => 'mer', 4 => 'jeu', 5 => 'ven', 6 => 'sam', 7 => 'dim'];

    for ($i = 0; $i < 8; $i++) {
        $day = $now->add(new DateInterval("P{$i}D"));
        $Ymd = $day->format('Y-m-d');
        if (in_array($Ymd, $hours['exceptions'] ?? [], true)) {
            continue; // fermé toute la journée
        }
        $w = (int) $day->format('N');
        $key = $map[$w];
        $row = $hours['days'][$key] ?? null;
        if (!$row || empty($row['open']) || !$row['start'] || !$row['end']) {
            continue;
        }

        $start = sl_at_time($Ymd, $row['start'], $tz);
        $end   = sl_at_time($Ymd, $row['end'],   $tz);

        // si aujourd'hui et start est passé mais end aussi, continue
        if ($i === 0) {
            if ($start && $start > $now) return $start;
            // gérer pause : si on est dans la pause et qu'il y a une reprise
            if ($row['pause_start'] && $row['pause_end']) {
                $p1 = sl_at_time($Ymd, $row['pause_start'], $tz);
                $p2 = sl_at_time($Ymd, $row['pause_end'],   $tz);
                if ($p1 && $p2 && $now >= $p1 && $now < $p2 && $p2 < $end) {
                    return $p2; // reprise après pause
                }
            }
            if ($end && $end > $now) {
                // start déjà passé et on n'est pas dans la pause → déjà ouvert, handled ailleurs
                continue;
            }
        } else {
            if ($start) return $start;
        }
    }

    return null;
}

/**
 * Formatage humain d’une heure
 */
function sl_time_h(DateTimeImmutable $dt): string
{
    return $dt->format('H:i');
}

/**
 * Raccourci pratique pour Blade
 */
function sl_opening_badge(?int $post_id = null): string
{
    $s = sl_get_open_status($post_id);
    if ($s['open']) {
        $until = isset($s['closes_at']) ? sl_time_h($s['closes_at']) : '';
        $txt = $s['label'] . ($until ? " • ferme à {$until}" : '');
        return $txt;
    }
    $next = $s['next_change'] ? ' • ouvre à ' . sl_time_h($s['next_change']) : '';
    return 'Fermé' . $next;
}

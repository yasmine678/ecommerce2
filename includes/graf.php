<?php
// utilitaires/graphiques.php

function genererCourbeLissee(array $points): string
{
    if (count($points) < 2) return '';

    $d = "M {$points[0][0]} {$points[0][1]} ";

    for ($i = 0; $i < count($points) - 1; $i++) {
        $p0 = $points[$i > 0 ? $i - 1 : $i];
        $p1 = $points[$i];
        $p2 = $points[$i + 1];
        $p3 = $points[$i + 2 < count($points) ? $i + 2 : $i + 1];

        $cp1x = $p1[0] + ($p2[0] - $p0[0]) / 6;
        $cp1y = $p1[1] + ($p2[1] - $p0[1]) / 6;
        $cp2x = $p2[0] - ($p3[0] - $p1[0]) / 6;
        $cp2y = $p2[1] - ($p3[1] - $p1[1]) / 6;

        $d .= "C $cp1x $cp1y, $cp2x $cp2y, {$p2[0]} {$p2[1]} ";
    }

    return $d;
}
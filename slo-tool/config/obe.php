<?php
function normalize_attainment(float $score): float {
    return max(0, min(3, round(($score / 100) * 3, 2)));
}

function risk_label(float $avg): string {
    if ($avg >= 70) return 'Green';
    if ($avg >= 50) return 'Yellow';
    return 'Red';
}

function predictive_alert(float $avg): string {
    if ($avg < 50) return 'High risk: immediate intervention required.';
    if ($avg < 65) return 'Moderate risk: assign mentor action plan.';
    return 'On track: continue current support.';
}
?>

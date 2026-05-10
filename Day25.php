<?php

function division($a, $b) {
    if ($b == 0) {
        throw new Exception("Division par zéro impossible");
    }
    return $a / $b;
}

try {
    $resultat = division(10, 0);
    echo $resultat;
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage();
}

?>

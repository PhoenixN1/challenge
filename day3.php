<?php

$age = 20;

if (!is_numeric($age) || $age < 0) {
    echo "Age invalide";
} elseif ($age < 13) {
    echo "Enfant";
} elseif ($age >= 13 && $age <= 17) {
    echo "Adolescent";
} elseif ($age >= 18 && $age <= 59) {
    
    if ($age >= 18 && $age < 21) {
        echo "Jeune adulte (acces limite)";
    } else {
        echo "Adulte";
    }

} elseif ($age >= 60 && $age <= 120) {
    echo "Senior";
} else {
    echo "Age suspect";
}

?>
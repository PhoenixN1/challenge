<?php

$password = "Mostafa1234";

$hash = password_hash($password, PASSWORD_BCRYPT);

echo "Mot de passe original : " . $password . "\n";
echo "Mot de passe hache    : " . $hash . "\n\n";

$passwordCorrect = "Mostafa1234";
$passwordIncorrect = "MauvaisMotDePasse";

if (password_verify($passwordCorrect, $hash)) {
    echo "Verification avec mot de passe correct : VALIDE\n";
} else {
    echo "Verification avec mot de passe correct : INVALIDE\n";
}

if (password_verify($passwordIncorrect, $hash)) {
    echo "Verification avec mot de passe incorrect : VALIDE\n";
} else {
    echo "Verification avec mot de passe incorrect : INVALIDE\n";
}

echo "\n";

$hashArgon = password_hash($password, PASSWORD_ARGON2ID);
echo "Hash avec Argon2id : " . $hashArgon . "\n\n";

$options = [
    'cost' => 12,
];
$hashCost = password_hash($password, PASSWORD_BCRYPT, $options);
echo "Hash avec cout personnalise (12) : " . $hashCost . "\n\n";

$info = password_get_info($hash);
echo "Algorithme utilise : " . $info['algoName'] . "\n";
echo "Options            : cost = " . $info['options']['cost'] . "\n\n";

if (password_needs_rehash($hash, PASSWORD_BCRYPT, ['cost' => 14])) {
    $nouveauHash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 14]);
    echo "Rehachage necessaire. Nouveau hash : " . $nouveauHash . "\n";
} else {
    echo "Rehachage non necessaire.\n";
}

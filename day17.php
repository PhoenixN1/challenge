<?php
$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'] ?? '';
    $email = $_POST['email'] ?? '';
    $age = $_POST['age'] ?? '';
    $message = $_POST['message'] ?? '';

    if (empty($nom)) {
        $errors['nom'] = 'Le nom est obligatoire';
    }

    if (!isset($_POST['email']) || empty($_POST['email'])) {
        $errors['email'] = 'L\'email est obligatoire';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'L\'email n\'est pas valide';
    }

    if (empty($age)) {
        $errors['age'] = 'L\'âge est obligatoire';
    } elseif (!is_numeric($age) || $age < 18 || $age > 99) {
        $errors['age'] = 'L\'âge doit être entre 18 et 99';
    }

    if (!isset($_POST['message']) || empty(trim($_POST['message']))) {
        $errors['message'] = 'Le message est obligatoire';
    } elseif (strlen($message) < 10) {
        $errors['message'] = 'Le message doit contenir au moins 10 caractères';
    }

    if (empty($errors)) {
        $success = 'Formulaire soumis avec succès';
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Form Validation</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 40px auto; padding: 0 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, textarea { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        input.error, textarea.error { border-color: red; }
        .error-msg { color: red; font-size: 13px; margin-top: 4px; }
        .success { color: green; font-weight: bold; margin-bottom: 15px; }
        button { background: #333; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #555; }
    </style>
</head>
<body>

<h2>Formulaire de validation</h2>

<?php if (!empty($success)): ?>
    <p class="success"><?= $success ?></p>
<?php endif; ?>

<form method="POST">
    <div class="form-group">
        <label>Nom</label>
        <input type="text" name="nom" value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>" class="<?= isset($errors['nom']) ? 'error' : '' ?>">
        <?php if (isset($errors['nom'])): ?>
            <div class="error-msg"><?= $errors['nom'] ?></div>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label>Email</label>
        <input type="text" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" class="<?= isset($errors['email']) ? 'error' : '' ?>">
        <?php if (isset($errors['email'])): ?>
            <div class="error-msg"><?= $errors['email'] ?></div>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label>Age</label>
        <input type="number" name="age" value="<?= htmlspecialchars($_POST['age'] ?? '') ?>" class="<?= isset($errors['age']) ? 'error' : '' ?>">
        <?php if (isset($errors['age'])): ?>
            <div class="error-msg"><?= $errors['age'] ?></div>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label>Message</label>
        <textarea name="message" rows="4" class="<?= isset($errors['message']) ? 'error' : '' ?>"><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
        <?php if (isset($errors['message'])): ?>
            <div class="error-msg"><?= $errors['message'] ?></div>
        <?php endif; ?>
    </div>

    <button type="submit">Envoyer</button>
</form>

</body>
</html>
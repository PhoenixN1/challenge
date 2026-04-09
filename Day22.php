<?php
$name = "";
$email = "";
$message = "";
$result = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = htmlspecialchars(trim($_POST["name"] ?? ""));
    $email = htmlspecialchars(trim($_POST["email"] ?? ""));
    $message = htmlspecialchars(trim($_POST["message"] ?? ""));

    if ($name && $email && $message) {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $result = "Message envoyé";
        } else {
            $result = "Email invalide";
        }
    } else {
        $result = "Tous les champs sont obligatoires";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Contact Form</title>
<style>
body {
    font-family: Arial;
    background: #f4f4f4;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}
form {
    background: white;
    padding: 20px;
    width: 300px;
    border-radius: 5px;
}
input, textarea {
    width: 100%;
    margin-bottom: 10px;
    padding: 10px;
    border: 1px solid #ccc;
}
button {
    width: 100%;
    padding: 10px;
    background: black;
    color: white;
    border: none;
}
p {
    text-align: center;
}
</style>
</head>
<body>

<form method="POST">
<input type="text" name="name" placeholder="Name" value="<?php echo $name; ?>">
<input type="email" name="email" placeholder="Email" value="<?php echo $email; ?>">
<textarea name="message" placeholder="Message"><?php echo $message; ?></textarea>
<button type="submit">Send</button>
<p><?php echo $result; ?></p>
</form>

</body>
</html>

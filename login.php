<?php
session_start();
include("includes/connessione.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Query per verificare l'utente
    $stmt = $pdo->prepare("SELECT * FROM utenti WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user) {
        echo "Credenziali non valide.";
    } else {
        // Verifica la password hashata
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            // Se l'email è admin@php.com, è un admin
            $_SESSION['is_admin'] = ($email === 'admin@php.com');
            header("Location: index.php");
            exit();
        } else {
            echo "Password errata.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Gestione Alternanza</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h1>Login</h1>
    <div class="form-container">
        <?php if (isset($email)): ?>
            <div class="alert alert-error">
                Credenziali non valide. Riprova.
            </div>
        <?php endif; ?>
        
        <form method="post">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Accedi</button>
        </form>
        
        <div class="auth-links">
            <p>Non hai un account?<a href="register.php">Registrati</a></p>
        </div>
    </div>
</body>
</html>

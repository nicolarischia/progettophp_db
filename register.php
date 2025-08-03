<?php
include("includes/connessione.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Controlla se le password coincidono
    if ($password !== $confirm_password) {
        $error = "Le password non coincidono.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Controlla se l'email è già registrata
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM utenti WHERE email = ?");
        $stmt->execute([$email]);
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            $error = "⚠️ L'email è già registrata.";
        } else {
            // Inserisci il nuovo utente
            $stmt = $pdo->prepare("INSERT INTO utenti (email, password) VALUES (?, ?)");
            if ($stmt->execute([$email, $hashed_password])) {
                $success = "✅ Registrazione completata.";
            } else {
                $error = "Errore durante la registrazione.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrazione - Gestione Alternanza</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h1>Registrazione</h1>
    <div class="form-container">
        <?php if (isset($error)): ?>
            <div class="alert alert-error">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        <?php if (isset($success)): ?>
            <div class="alert alert-success">
                <?php echo $success; ?> <a href='login.php'>Vai al login</a>
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
            <div class="form-group">
                <label for="confirm_password">Conferma Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit">Registrati</button>
        </form>
        
        <div class="auth-links">
            <p>Hai già un account? <a href="login.php">Accedi</a></p>
        </div>
    </div>
</body>
</html>

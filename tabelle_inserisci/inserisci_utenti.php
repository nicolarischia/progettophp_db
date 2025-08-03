<?php
session_start();
include("../includes/connessione.php");
include("../auth.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    
    // Hash della password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    try {
        $stmt = $pdo->prepare("INSERT INTO utenti (email, password) VALUES (?, ?)");
        $stmt->execute([$email, $hashed_password]);
        header("Location: ../tabelle_visualizza/gestione_utenti.php?success=1");
        exit();
    } catch (PDOException $e) {
        $error = "Errore durante l'inserimento: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inserisci Utente - PCTO</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h1>Inserisci Nuovo Utente</h1>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="" class="form-container">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="button-container">
                <button type="submit" class="btn btn-primary">Inserisci</button>
                <a href="../tabelle_visualizza/gestione_utenti.php" class="btn btn-secondary">Annulla</a>
            </div>
        </form>
    </div>
</body>
</html> 
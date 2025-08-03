<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include("../includes/connessione.php");
include("../auth.php");

// Verifica se l'utente è autenticato
if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit();
}

// Query per ottenere tutti gli utenti
try {
    $stmt = $pdo->query("SELECT * FROM utenti ORDER BY email");
    $utenti = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Errore nel recupero dei dati: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizza Utenti - PCTO</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h1>Utenti</h1>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Email</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($utenti) && !empty($utenti)): ?>
                        <?php foreach ($utenti as $utente): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($utente['id']); ?></td>
                                <td><?php echo htmlspecialchars($utente['email']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="2">Nessun utente trovato</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <div class="button-container">
            <a href="../index.php" class="btn btn-secondary">Torna alla Home</a>
        </div>
    </div>
</body>
</html> 
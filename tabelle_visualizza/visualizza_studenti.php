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

// Query per ottenere tutti gli studenti
try {
    $stmt = $pdo->query("SELECT * FROM studenti ORDER BY cognome, nome");
    $studenti = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Errore nel recupero dei dati: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizza Studenti - PCTO</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h1>Studenti</h1>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cognome</th>
                        <th>Nome</th>
                        <th>Data di Nascita</th>
                        <th>Email</th>
                        <th>Classe</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($studenti) && !empty($studenti)): ?>
                        <?php foreach ($studenti as $studente): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($studente['idStudente']); ?></td>
                                <td><?php echo htmlspecialchars($studente['cognome']); ?></td>
                                <td><?php echo htmlspecialchars($studente['nome']); ?></td>
                                <td><?php echo htmlspecialchars($studente['dataDiNascita']); ?></td>
                                <td><?php echo htmlspecialchars($studente['email']); ?></td>
                                <td><?php echo htmlspecialchars($studente['classe']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">Nessuno studente trovato</td>
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
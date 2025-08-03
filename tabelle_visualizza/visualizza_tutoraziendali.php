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

// Query per ottenere tutti i tutor aziendali con le relative aziende
try {
    $stmt = $pdo->query("SELECT t.*, a.regioneSociale as nomeAzienda 
                         FROM tutoraziendali t
                         JOIN aziende a ON t.idAzienda = a.idAzienda
                         ORDER BY t.cognome, t.nome");
    $tutor = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Errore nel recupero dei dati: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizza Tutor Aziendali - PCTO</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h1>Tutor Aziendali</h1>
        
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
                        <th>Email</th>
                        <th>Telefono</th>
                        <th>Azienda</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($tutor) && !empty($tutor)): ?>
                        <?php foreach ($tutor as $t): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($t['idTutor']); ?></td>
                                <td><?php echo htmlspecialchars($t['cognome']); ?></td>
                                <td><?php echo htmlspecialchars($t['nome']); ?></td>
                                <td><?php echo htmlspecialchars($t['email']); ?></td>
                                <td><?php echo htmlspecialchars($t['telefono']); ?></td>
                                <td><?php echo htmlspecialchars($t['nomeAzienda']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">Nessun tutor aziendale trovato</td>
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
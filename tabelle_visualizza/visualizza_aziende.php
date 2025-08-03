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

// Query per ottenere tutte le aziende
try {
    $stmt = $pdo->query("SELECT * FROM aziende ORDER BY regioneSociale");
    $aziende = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Errore nel recupero dei dati: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizza Aziende - PCTO</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h1>Aziende</h1>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Ragione Sociale</th>
                        <th>Partita IVA</th>
                        <th>Numero Civico</th>
                        <th>Telefono</th>
                        <th>Numero Partita IVA</th>
                        <th>Email</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($aziende) && !empty($aziende)): ?>
                        <?php foreach ($aziende as $azienda): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($azienda['idAzienda']); ?></td>
                                <td><?php echo htmlspecialchars($azienda['regioneSociale']); ?></td>
                                <td><?php echo htmlspecialchars($azienda['partitaIva']); ?></td>
                                <td><?php echo htmlspecialchars($azienda['numCivico']); ?></td>
                                <td><?php echo htmlspecialchars($azienda['telefono']); ?></td>
                                <td><?php echo htmlspecialchars($azienda['numPartitaIva']); ?></td>
                                <td><?php echo htmlspecialchars($azienda['email']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7">Nessuna azienda trovata</td>
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
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

// Query per ottenere tutti gli stage con informazioni su studenti e aziende
try {
    $stmt = $pdo->query("SELECT s.*, 
                         st.cognome as cognomeStudente, st.nome as nomeStudente,
                         a.regioneSociale as nomeAzienda
                         FROM stage s
                         JOIN studenti st ON s.idStudente = st.idStudente
                         JOIN aziende a ON s.idAzienda = a.idAzienda
                         ORDER BY s.dataInizio DESC");
    $stage = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Errore nel recupero dei dati: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizza Stage - PCTO</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h1>Stage</h1>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Studente</th>
                        <th>Azienda</th>
                        <th>Argomento</th>
                        <th>Modo Svolgimento</th>
                        <th>Data Inizio</th>
                        <th>Data Fine</th>
                        <th>Ore Settore</th>
                        <th>Valutazione</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($stage) && !empty($stage)): ?>
                        <?php foreach ($stage as $s): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($s['idStage']); ?></td>
                                <td><?php echo htmlspecialchars($s['cognomeStudente'] . ' ' . $s['nomeStudente']); ?></td>
                                <td><?php echo htmlspecialchars($s['nomeAzienda']); ?></td>
                                <td><?php echo htmlspecialchars($s['argomento']); ?></td>
                                <td><?php echo htmlspecialchars($s['modoSvolgimento']); ?></td>
                                <td><?php echo htmlspecialchars($s['dataInizio']); ?></td>
                                <td><?php echo htmlspecialchars($s['dataFine']); ?></td>
                                <td><?php echo htmlspecialchars($s['oreSettoreSvolte']); ?></td>
                                <td><?php echo htmlspecialchars($s['valutazione']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9">Nessuno stage trovato</td>
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
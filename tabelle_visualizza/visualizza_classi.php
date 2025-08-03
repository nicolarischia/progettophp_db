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

// Query per ottenere tutte le classi con i docenti referenti
try {
    $stmt = $pdo->query("SELECT c.*, d.cognome as cognomeDocente, d.nome as nomeDocente 
                         FROM classi c 
                         LEFT JOIN docentireferenti d ON c.idDocente = d.idDocente 
                         ORDER BY c.nomeClasse");
    $classi = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Errore nel recupero dei dati: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizza Classi - PCTO</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h1>Classi</h1>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome Classe</th>
                        <th>Anno</th>
                        <th>Specializzazione</th>
                        <th>Docente Referente</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($classi) && !empty($classi)): ?>
                        <?php foreach ($classi as $classe): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($classe['idClasse']); ?></td>
                                <td><?php echo htmlspecialchars($classe['nomeClasse']); ?></td>
                                <td><?php echo htmlspecialchars($classe['anno']); ?></td>
                                <td><?php echo htmlspecialchars($classe['specializzazione']); ?></td>
                                <td>
                                    <?php 
                                    if ($classe['cognomeDocente'] && $classe['nomeDocente']) {
                                        echo htmlspecialchars($classe['cognomeDocente'] . ' ' . $classe['nomeDocente']);
                                    } else {
                                        echo "Non assegnato";
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">Nessuna classe trovata</td>
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
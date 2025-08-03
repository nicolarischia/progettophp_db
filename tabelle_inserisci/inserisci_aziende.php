<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include("../includes/connessione.php");
include("../auth.php");

// Verifica se l'utente è autenticato e se è admin
if (!isset($_SESSION["user_id"]) || !isAdmin()) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $regioneSociale = trim($_POST['regioneSociale']);
    $partitaIva = trim($_POST['partitaIva']);
    $numCivico = trim($_POST['numCivico']);
    $telefono = trim($_POST['telefono']);
    $numPartiteIva = trim($_POST['numPartiteIva']);
    
    try {
        $stmt = $pdo->prepare("INSERT INTO aziende (regioneSociale, partitaIva, numCivico, telefono, numPartiteIva) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$regioneSociale, $partitaIva, $numCivico, $telefono, $numPartiteIva]);
        header("Location: ../tabelle_gestione/gestione_aziende.php?success=1");
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
    <title>Inserisci Azienda - PCTO</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h1>Inserisci Nuova Azienda</h1>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="" class="form-container">
            <div class="form-group">
                <label for="regioneSociale">Ragione Sociale:</label>
                <input type="text" id="regioneSociale" name="regioneSociale" required>
            </div>

            <div class="form-group">
                <label for="partitaIva">Partita IVA:</label>
                <input type="text" id="partitaIva" name="partitaIva" required>
            </div>

            <div class="form-group">
                <label for="numCivico">Numero Civico:</label>
                <input type="number" id="numCivico" name="numCivico" required>
            </div>

            <div class="form-group">
                <label for="telefono">Telefono:</label>
                <input type="tel" id="telefono" name="telefono" required>
            </div>

            <div class="form-group">
                <label for="numPartiteIva">Numero Partita IVA:</label>
                <input type="text" id="numPartiteIva" name="numPartiteIva" maxlength="11">
            </div>

            <div class="button-container">
                <button type="submit" class="btn btn-primary">Inserisci</button>
                <a href="../tabelle_gestione/gestione_aziende.php" class="btn btn-secondary">Annulla</a>
            </div>
        </form>
    </div>
</body>
</html> 
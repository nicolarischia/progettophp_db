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

// Recupero lista aziende
try {
    $stmt = $pdo->query("SELECT idAzienda, regioneSociale FROM aziende ORDER BY regioneSociale");
    $aziende = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Errore nel recupero delle aziende: " . $e->getMessage();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cognome = trim($_POST['cognome']);
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $telefono = trim($_POST['telefono']);
    $idAzienda = trim($_POST['idAzienda']);
    
    try {
        $stmt = $pdo->prepare("INSERT INTO tutoraziendali (cognome, nome, email, telefono, idAzienda) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$cognome, $nome, $email, $telefono, $idAzienda]);
        header("Location: ../tabelle_gestione/gestione_tutor.php?success=1");
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
    <title>Inserisci Tutor - PCTO</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h1>Inserisci Nuovo Tutor Aziendale</h1>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="" class="form-container">
            <div class="form-group">
                <label for="cognome">Cognome:</label>
                <input type="text" id="cognome" name="cognome" required>
            </div>

            <div class="form-group">
                <label for="nome">Nome:</label>
                <input type="text" id="nome" name="nome" required>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="telefono">Telefono:</label>
                <input type="tel" id="telefono" name="telefono" required>
            </div>

            <div class="form-group">
                <label for="idAzienda">Azienda:</label>
                <select name="idAzienda" id="idAzienda" required>
                    <option value="">Seleziona un'azienda</option>
                    <?php if (isset($aziende) && !empty($aziende)): ?>
                        <?php foreach ($aziende as $azienda): ?>
                            <option value="<?php echo $azienda['idAzienda']; ?>">
                                <?php echo htmlspecialchars($azienda['regioneSociale']); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="button-container">
                <button type="submit" class="btn btn-primary">Inserisci</button>
                <a href="../tabelle_gestione/gestione_tutor.php" class="btn btn-secondary">Annulla</a>
            </div>
        </form>
    </div>
</body>
</html> 
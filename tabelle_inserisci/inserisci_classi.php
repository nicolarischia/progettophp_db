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

// Recupero lista docenti per il select
try {
    $stmt = $pdo->query("SELECT idDocente, cognome, nome FROM docentireferenti ORDER BY cognome, nome");
    $docenti = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Errore nel recupero dei docenti: " . $e->getMessage();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nomeClasse = trim($_POST['nomeClasse']);
    $anno = trim($_POST['anno']);
    $specializzazione = trim($_POST['specializzazione']);
    $idDocente = trim($_POST['idDocente']);
    
    try {
        $stmt = $pdo->prepare("INSERT INTO classi (nomeClasse, anno, specializzazione, idDocente) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nomeClasse, $anno, $specializzazione, $idDocente]);
        header("Location: ../tabelle_gestione/gestione_classi.php?success=1");
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
    <title>Inserisci Classe - PCTO</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h1>Inserisci Nuova Classe</h1>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="" class="form-container">
            <div class="form-group">
                <label for="nomeClasse">Nome Classe:</label>
                <input type="text" id="nomeClasse" name="nomeClasse" maxlength="4" required>
            </div>

            <div class="form-group">
                <label for="anno">Anno:</label>
                <input type="number" id="anno" name="anno" min="1" max="5" required>
            </div>

            <div class="form-group">
                <label for="specializzazione">Specializzazione:</label>
                <input type="text" id="specializzazione" name="specializzazione" required>
            </div>

            <div class="form-group">
                <label for="idDocente">Docente Referente:</label>
                <select id="idDocente" name="idDocente" required>
                    <option value="">Seleziona un docente</option>
                    <?php foreach ($docenti as $docente): ?>
                        <option value="<?php echo $docente['idDocente']; ?>">
                            <?php echo htmlspecialchars($docente['cognome'] . ' ' . $docente['nome']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="button-container">
                <button type="submit" class="btn btn-primary">Inserisci</button>
                <a href="../tabelle_gestione/gestione_classi.php" class="btn btn-secondary">Annulla</a>
            </div>
        </form>
    </div>
</body>
</html> 
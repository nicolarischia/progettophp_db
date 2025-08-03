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

// Recupero lista studenti e aziende per i select
try {
    $stmt = $pdo->query("SELECT idStudente, cognome, nome FROM studenti ORDER BY cognome, nome");
    $studenti = $stmt->fetchAll();
    
    $stmt = $pdo->query("SELECT idAzienda, regioneSociale FROM aziende ORDER BY regioneSociale");
    $aziende = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Errore nel recupero dei dati: " . $e->getMessage();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $argomento = trim($_POST['argomento']);
    $modoSvolgimento = trim($_POST['modoSvolgimento']);
    $dataInizio = trim($_POST['dataInizio']);
    $dataFine = trim($_POST['dataFine']);
    $oreSettoreSvolte = trim($_POST['oreSettoreSvolte']);
    $valutazione = !empty($_POST['valutazione']) ? trim($_POST['valutazione']) : null;
    $idStudente = trim($_POST['idStudente']);
    $idAzienda = trim($_POST['idAzienda']);
    
    try {
        $stmt = $pdo->prepare("INSERT INTO stage (argomento, modoSvolgimento, dataInizio, dataFine, oreSettoreSvolte, valutazione, idStudente, idAzienda) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$argomento, $modoSvolgimento, $dataInizio, $dataFine, $oreSettoreSvolte, $valutazione, $idStudente, $idAzienda]);
        header("Location: ../tabelle_gestione/gestione_stage.php?success=1");
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
    <title>Inserisci Stage - PCTO</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h1>Inserisci Nuovo Stage</h1>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="" class="form-container">
            <div class="form-group">
                <label for="argomento">Argomento:</label>
                <input type="text" id="argomento" name="argomento" required>
            </div>

            <div class="form-group">
                <label for="modoSvolgimento">Modo Svolgimento:</label>
                <select id="modoSvolgimento" name="modoSvolgimento" required>
                    <option value="">Seleziona il modo di svolgimento</option>
                    <option value="Esercizio pratico">Esercizio pratico</option>
                    <option value="Autoapprendimento">Autoapprendimento</option>
                    <option value="Apprendimento assistito">Apprendimento assistito</option>
                </select>
            </div>

            <div class="form-group">
                <label for="dataInizio">Data Inizio:</label>
                <input type="date" id="dataInizio" name="dataInizio" required>
            </div>

            <div class="form-group">
                <label for="dataFine">Data Fine:</label>
                <input type="date" id="dataFine" name="dataFine">
            </div>

            <div class="form-group">
                <label for="oreSettoreSvolte">Ore Settore Svolte:</label>
                <input type="number" id="oreSettoreSvolte" name="oreSettoreSvolte" min="0" required>
            </div>

            <div class="form-group">
                <label for="valutazione">Valutazione:</label>
                <input type="number" id="valutazione" name="valutazione" min="0" max="10">
            </div>

            <div class="form-group">
                <label for="idStudente">Studente:</label>
                <select id="idStudente" name="idStudente" required>
                    <option value="">Seleziona uno studente</option>
                    <?php foreach ($studenti as $studente): ?>
                        <option value="<?php echo $studente['idStudente']; ?>">
                            <?php echo htmlspecialchars($studente['cognome'] . ' ' . $studente['nome']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="idAzienda">Azienda:</label>
                <select id="idAzienda" name="idAzienda" required>
                    <option value="">Seleziona un'azienda</option>
                    <?php foreach ($aziende as $azienda): ?>
                        <option value="<?php echo $azienda['idAzienda']; ?>">
                            <?php echo htmlspecialchars($azienda['regioneSociale']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="button-container">
                <button type="submit" class="btn btn-primary">Inserisci</button>
                <a href="../tabelle_gestione/gestione_stage.php" class="btn btn-secondary">Annulla</a>
            </div>
        </form>
    </div>

    <script>
        // Validazione date
        document.getElementById('dataFine').addEventListener('change', function() {
            var dataInizio = document.getElementById('dataInizio').value;
            var dataFine = this.value;
            
            if (dataInizio && dataFine && dataInizio > dataFine) {
                alert('La data di fine non può essere precedente alla data di inizio');
                this.value = '';
            }
        });
    </script>
</body>
</html> 
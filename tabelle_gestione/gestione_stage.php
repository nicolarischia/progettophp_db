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

// Gestione eliminazione
if (isset($_POST['action']) && $_POST['action'] === 'delete') {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM stage WHERE idStage = ?");
        $stmt->execute([$id]);
        echo json_encode(['success' => true]);
        exit();
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Errore durante l\'eliminazione']);
        exit();
    }
}

// Gestione modifica
if (isset($_POST['action']) && $_POST['action'] === 'edit') {
    $data = json_decode(file_get_contents('php://input'), true);
    try {
        $stmt = $pdo->prepare("UPDATE stage SET 
            argomento = ?, 
            modoSvolgimento = ?, 
            dataInizio = ?, 
            dataFine = ?, 
            oreSettoreSvolte = ?, 
            valutazione = ?, 
            idStudente = ?, 
            idAzienda = ? 
            WHERE idStage = ?");
        $stmt->execute([
            $data['argomento'],
            $data['modoSvolgimento'],
            $data['dataInizio'],
            $data['dataFine'] ?: null,
            $data['oreSettoreSvolte'],
            $data['valutazione'] ?: null,
            $data['idStudente'],
            $data['idAzienda'],
            $data['id']
        ]);
        echo json_encode(['success' => true]);
        exit();
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Errore durante la modifica']);
        exit();
    }
}

// Recupero dati
try {
    $stmt = $pdo->query("SELECT s.*, st.cognome as cognomeStudente, st.nome as nomeStudente, 
                                a.regioneSociale
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
    <title>Gestione Stage - PCTO</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
            border-radius: 5px;
            position: relative;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        .close:hover {
            color: black;
        }
        .btn-container {
            margin-top: 20px;
            text-align: right;
        }
        .btn-container button {
            margin-left: 10px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
        }
        .alert-success {
            color: #3c763d;
            background-color: #dff0d8;
            border-color: #d6e9c6;
        }
        .alert-error {
            color: #a94442;
            background-color: #f2dede;
            border-color: #ebccd1;
        }
        #messageContainer {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1001;
        }
    </style>
</head>
<body>
    <div id="messageContainer"></div>
    <div class="container">
        <h1>Gestione Stage</h1>
        
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">Operazione completata con successo!</div>
        <?php endif; ?>
        
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-error">Si è verificato un errore durante l'operazione.</div>
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
                        <th>Ore Svolte</th>
                        <th>Valutazione</th>
                        <th>Azioni</th>
                    </tr>
                </thead>
                <tbody id="stageTableBody">
                    <?php if (isset($stage) && !empty($stage)): ?>
                        <?php foreach ($stage as $s): ?>
                            <tr data-id="<?php echo htmlspecialchars($s['idStage']); ?>">
                                <td><?php echo htmlspecialchars($s['idStage']); ?></td>
                                <td><?php echo htmlspecialchars($s['cognomeStudente'] . ' ' . $s['nomeStudente']); ?></td>
                                <td><?php echo htmlspecialchars($s['regioneSociale']); ?></td>
                                <td><?php echo htmlspecialchars($s['argomento']); ?></td>
                                <td><?php echo htmlspecialchars($s['modoSvolgimento']); ?></td>
                                <td><?php echo htmlspecialchars($s['dataInizio']); ?></td>
                                <td><?php echo htmlspecialchars($s['dataFine']); ?></td>
                                <td><?php echo htmlspecialchars($s['oreSettoreSvolte']); ?></td>
                                <td><?php echo htmlspecialchars($s['valutazione']); ?></td>
                                <td>
                                    <button onclick="openEditModal(<?php echo htmlspecialchars(json_encode($s)); ?>)" class="btn btn-edit">Modifica</button>
                                    <button onclick="openDeleteModal(<?php echo $s['idStage']; ?>, '<?php echo htmlspecialchars($s['argomento']); ?>')" class="btn btn-delete">Elimina</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10">Nessuno stage trovato</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="button-container">
            <a href="../tabelle_inserisci/inserisci_stage.php" class="btn btn-primary">Inserisci Nuovo Stage</a>
            <a href="../index.php" class="btn btn-secondary">Torna alla Home</a>
        </div>
    </div>

    <!-- Modal Modifica -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeEditModal()">&times;</span>
            <h2>Modifica Stage</h2>
            <form id="editForm" onsubmit="return handleEditSubmit(event)">
                <input type="hidden" id="edit_id" name="id">
                <div class="form-group">
                    <label for="edit_argomento">Argomento:</label>
                    <input type="text" id="edit_argomento" name="argomento" required>
                </div>
                <div class="form-group">
                    <label for="edit_modoSvolgimento">Modo Svolgimento:</label>
                    <select id="edit_modoSvolgimento" name="modoSvolgimento" required>
                        <option value="Esercizio pratico">Esercizio pratico</option>
                        <option value="Autoapprendimento">Autoapprendimento</option>
                        <option value="Apprendimento assistito">Apprendimento assistito</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="edit_dataInizio">Data Inizio:</label>
                    <input type="date" id="edit_dataInizio" name="dataInizio" required>
                </div>
                <div class="form-group">
                    <label for="edit_dataFine">Data Fine:</label>
                    <input type="date" id="edit_dataFine" name="dataFine">
                </div>
                <div class="form-group">
                    <label for="edit_oreSettoreSvolte">Ore Settore Svolte:</label>
                    <input type="number" id="edit_oreSettoreSvolte" name="oreSettoreSvolte" min="0" required>
                </div>
                <div class="form-group">
                    <label for="edit_valutazione">Valutazione:</label>
                    <input type="number" id="edit_valutazione" name="valutazione" min="0" max="10">
                </div>
                <div class="form-group">
                    <label for="edit_idStudente">Studente:</label>
                    <select id="edit_idStudente" name="idStudente" required>
                        <?php
                        $stmt = $pdo->query("SELECT idStudente, cognome, nome FROM studenti ORDER BY cognome, nome");
                        while ($studente = $stmt->fetch()) {
                            echo "<option value='" . $studente['idStudente'] . "'>" . 
                                 htmlspecialchars($studente['cognome'] . ' ' . $studente['nome']) . 
                                 "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="edit_idAzienda">Azienda:</label>
                    <select id="edit_idAzienda" name="idAzienda" required>
                        <?php
                        $stmt = $pdo->query("SELECT idAzienda, regioneSociale FROM aziende ORDER BY regioneSociale");
                        while ($azienda = $stmt->fetch()) {
                            echo "<option value='" . $azienda['idAzienda'] . "'>" . 
                                 htmlspecialchars($azienda['regioneSociale']) . 
                                 "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="btn-container">
                    <button type="submit" class="btn btn-primary">Salva Modifiche</button>
                    <button type="button" class="btn btn-secondary" onclick="closeEditModal()">Annulla</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Elimina -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeDeleteModal()">&times;</span>
            <h2>Conferma Eliminazione</h2>
            <p>Sei sicuro di voler eliminare lo stage "<span id="delete_name"></span>"?</p>
            <input type="hidden" id="delete_id">
            <div class="btn-container">
                <button onclick="handleDelete()" class="btn btn-delete">Elimina</button>
                <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()">Annulla</button>
            </div>
        </div>
    </div>

    <script>
        function showMessage(message, type) {
            const messageContainer = document.getElementById('messageContainer');
            const alert = document.createElement('div');
            alert.className = `alert alert-${type}`;
            alert.textContent = message;
            messageContainer.appendChild(alert);
            setTimeout(() => alert.remove(), 3000);
        }

        function openEditModal(stage) {
            document.getElementById('edit_id').value = stage.idStage;
            document.getElementById('edit_argomento').value = stage.argomento;
            document.getElementById('edit_modoSvolgimento').value = stage.modoSvolgimento;
            document.getElementById('edit_dataInizio').value = stage.dataInizio;
            document.getElementById('edit_dataFine').value = stage.dataFine || '';
            document.getElementById('edit_oreSettoreSvolte').value = stage.oreSettoreSvolte;
            document.getElementById('edit_valutazione').value = stage.valutazione || '';
            document.getElementById('edit_idStudente').value = stage.idStudente;
            document.getElementById('edit_idAzienda').value = stage.idAzienda;
            document.getElementById('editModal').style.display = 'block';
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        function openDeleteModal(id, argomento) {
            document.getElementById('delete_id').value = id;
            document.getElementById('delete_name').textContent = argomento;
            document.getElementById('deleteModal').style.display = 'block';
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').style.display = 'none';
        }

        async function handleEditSubmit(event) {
            event.preventDefault();
            const formData = new FormData(event.target);
            const data = Object.fromEntries(formData.entries());
            data.action = 'edit';

            try {
                const response = await fetch(window.location.href, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();
                
                if (result.success) {
                    showMessage('Stage modificato con successo!', 'success');
                    closeEditModal();
                    window.location.reload();
                } else {
                    showMessage(result.message || 'Errore durante la modifica', 'error');
                }
            } catch (error) {
                console.error('Errore:', error);
                showMessage('Errore durante la comunicazione con il server', 'error');
            }

            return false;
        }

        async function handleDelete() {
            const id = document.getElementById('delete_id').value;
            
            try {
                const response = await fetch(window.location.href, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ action: 'delete', id: id })
                });

                const result = await response.json();
                
                if (result.success) {
                    showMessage('Stage eliminato con successo!', 'success');
                    closeDeleteModal();
                    const row = document.querySelector(`tr[data-id="${id}"]`);
                    if (row) {
                        row.remove();
                    }
                } else {
                    showMessage(result.message || 'Errore durante l\'eliminazione', 'error');
                }
            } catch (error) {
                showMessage('Errore durante la comunicazione con il server', 'error');
            }
        }

        // Chiudi i modal quando si clicca fuori
        window.onclick = function(event) {
            if (event.target == document.getElementById('editModal')) {
                closeEditModal();
            }
            if (event.target == document.getElementById('deleteModal')) {
                closeDeleteModal();
            }
        }

        // Validazione date
        document.getElementById('edit_dataFine').addEventListener('change', function() {
            var dataInizio = document.getElementById('edit_dataInizio').value;
            var dataFine = this.value;
            
            if (dataInizio && dataFine && dataInizio > dataFine) {
                alert('La data di fine non può essere precedente alla data di inizio');
                this.value = '';
            }
        });
    </script>
</body>
</html> 
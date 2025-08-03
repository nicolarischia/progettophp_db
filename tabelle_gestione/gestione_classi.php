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
        // Prima verifichiamo se ci sono studenti associati a questa classe
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM studenti WHERE classe = ?");
        $stmt->execute([$id]);
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            echo json_encode(['success' => false, 'message' => 'Impossibile eliminare la classe: ci sono studenti associati']);
            exit();
        }

        $stmt = $pdo->prepare("DELETE FROM classi WHERE nomeClasse = ?");
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
        $stmt = $pdo->prepare("UPDATE classi SET 
            anno = ?, 
            specializzazione = ?, 
            idDocente = ? 
            WHERE nomeClasse = ?");
        $stmt->execute([
            $data['anno'],
            $data['specializzazione'],
            $data['idDocente'],
            $data['nomeClasse']
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
    <title>Gestione Classi - PCTO</title>
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
        <h1>Gestione Classi</h1>
        
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
                        <th>Nome Classe</th>
                        <th>Anno</th>
                        <th>Specializzazione</th>
                        <th>Docente Referente</th>
                        <th>Azioni</th>
                    </tr>
                </thead>
                <tbody id="classiTableBody">
                    <?php if (isset($classi) && !empty($classi)): ?>
                        <?php foreach ($classi as $classe): ?>
                            <tr data-id="<?php echo htmlspecialchars($classe['nomeClasse']); ?>">
                                <td><?php echo htmlspecialchars($classe['nomeClasse']); ?></td>
                                <td><?php echo htmlspecialchars($classe['anno']); ?></td>
                                <td><?php echo htmlspecialchars($classe['specializzazione']); ?></td>
                                <td><?php echo htmlspecialchars($classe['cognomeDocente'] . ' ' . $classe['nomeDocente']); ?></td>
                                <td>
                                    <button onclick="openEditModal(<?php echo htmlspecialchars(json_encode($classe)); ?>)" class="btn btn-edit">Modifica</button>
                                    <button onclick="openDeleteModal('<?php echo htmlspecialchars($classe['nomeClasse']); ?>', '<?php echo htmlspecialchars($classe['nomeClasse']); ?>')" class="btn btn-delete">Elimina</button>
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
            <a href="../tabelle_inserisci/inserisci_classi.php" class="btn btn-primary">Inserisci Nuova Classe</a>
            <a href="../index.php" class="btn btn-secondary">Torna alla Home</a>
        </div>
    </div>

    <!-- Modal Modifica -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeEditModal()">&times;</span>
            <h2>Modifica Classe</h2>
            <form id="editForm" onsubmit="return handleEditSubmit(event)">
                <input type="hidden" id="edit_nomeClasse" name="nomeClasse">
                <div class="form-group">
                    <label for="edit_anno">Anno:</label>
                    <input type="number" id="edit_anno" name="anno" min="1" max="5" required>
                </div>
                <div class="form-group">
                    <label for="edit_specializzazione">Specializzazione:</label>
                    <input type="text" id="edit_specializzazione" name="specializzazione" required>
                </div>
                <div class="form-group">
                    <label for="edit_idDocente">Docente Referente:</label>
                    <select id="edit_idDocente" name="idDocente" required>
                        <?php
                        try {
                            $stmt = $pdo->query("SELECT idDocente, cognome, nome FROM docentireferenti ORDER BY cognome, nome");
                            $docenti = $stmt->fetchAll();
                            foreach ($docenti as $docente) {
                                echo "<option value='" . $docente['idDocente'] . "'>" . 
                                     htmlspecialchars($docente['cognome'] . ' ' . $docente['nome']) . 
                                     "</option>";
                            }
                        } catch (PDOException $e) {
                            echo "<option value=''>Errore nel caricamento dei docenti</option>";
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
            <p>Sei sicuro di voler eliminare la classe <span id="delete_name"></span>?</p>
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

        function openEditModal(classe) {
            document.getElementById('edit_nomeClasse').value = classe.nomeClasse;
            document.getElementById('edit_anno').value = classe.anno;
            document.getElementById('edit_specializzazione').value = classe.specializzazione;
            document.getElementById('edit_idDocente').value = classe.idDocente;
            document.getElementById('editModal').style.display = 'block';
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        function openDeleteModal(id, nome) {
            document.getElementById('delete_id').value = id;
            document.getElementById('delete_name').textContent = nome;
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

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();
                
                if (result.success) {
                    showMessage('Classe modificata con successo!', 'success');
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

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();
                
                if (result.success) {
                    showMessage('Classe eliminata con successo!', 'success');
                    closeDeleteModal();
                    const row = document.querySelector(`tr[data-id="${id}"]`);
                    if (row) {
                        row.remove();
                    }
                } else {
                    showMessage(result.message || 'Errore durante l\'eliminazione', 'error');
                }
            } catch (error) {
                console.error('Errore:', error);
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
    </script>
</body>
</html> 
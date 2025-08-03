<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include("../includes/connessione.php");
include("../auth.php");

// Gestione eliminazione
if (isset($_POST['action']) && $_POST['action'] === 'delete') {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM docentireferenti WHERE idDocente = ?");
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
        $stmt = $pdo->prepare("UPDATE docentireferenti SET 
            cognome = ?, 
            nome = ?, 
            email = ? 
            WHERE idDocente = ?");
        $stmt->execute([
            $data['cognome'],
            $data['nome'],
            $data['email'],
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
    $stmt = $pdo->query("SELECT * FROM docentireferenti ORDER BY cognome, nome");
    $docenti = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Errore nel recupero dei dati: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestione Docenti - PCTO</title>
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
        .form-group input {
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
        <h1>Gestione Docenti</h1>
        
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
                        <th>Cognome</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Azioni</th>
                    </tr>
                </thead>
                <tbody id="docentiTableBody">
                    <?php if (isset($docenti) && !empty($docenti)): ?>
                        <?php foreach ($docenti as $docente): ?>
                            <tr data-id="<?php echo htmlspecialchars($docente['idDocente']); ?>">
                                <td><?php echo htmlspecialchars($docente['idDocente']); ?></td>
                                <td><?php echo htmlspecialchars($docente['cognome']); ?></td>
                                <td><?php echo htmlspecialchars($docente['nome']); ?></td>
                                <td><?php echo htmlspecialchars($docente['email']); ?></td>
                                <td>
                                    <button onclick="openEditModal(<?php echo htmlspecialchars(json_encode($docente)); ?>)" class="btn btn-edit">Modifica</button>
                                    <button onclick="openDeleteModal(<?php echo $docente['idDocente']; ?>, '<?php echo htmlspecialchars($docente['cognome'] . ' ' . $docente['nome']); ?>')" class="btn btn-delete">Elimina</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">Nessun docente trovato</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="button-container">
            <a href="../tabelle_inserisci/inserisci_docenti.php" class="btn btn-primary">Inserisci Nuovo Docente</a>
            <a href="../index.php" class="btn btn-secondary">Torna alla Home</a>
        </div>
    </div>

    <!-- Modal Modifica -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeEditModal()">&times;</span>
            <h2>Modifica Docente</h2>
            <form id="editForm" onsubmit="return handleEditSubmit(event)">
                <input type="hidden" id="edit_id" name="id">
                <div class="form-group">
                    <label for="edit_cognome">Cognome:</label>
                    <input type="text" id="edit_cognome" name="cognome" required>
                </div>
                <div class="form-group">
                    <label for="edit_nome">Nome:</label>
                    <input type="text" id="edit_nome" name="nome" required>
                </div>
                <div class="form-group">
                    <label for="edit_email">Email:</label>
                    <input type="email" id="edit_email" name="email" required>
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
            <p>Sei sicuro di voler eliminare il docente <span id="delete_name"></span>?</p>
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

        function openEditModal(docente) {
            document.getElementById('edit_id').value = docente.idDocente;
            document.getElementById('edit_cognome').value = docente.cognome;
            document.getElementById('edit_nome').value = docente.nome;
            document.getElementById('edit_email').value = docente.email;
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
                    showMessage('Docente modificato con successo!', 'success');
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
                    showMessage('Docente eliminato con successo!', 'success');
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
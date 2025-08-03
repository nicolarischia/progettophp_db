<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Gestione Alternanza Scuola-Lavoro</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .admin-only {
            display: <?php echo isset($_SESSION['is_admin']) && $_SESSION['is_admin'] ? 'block' : 'none'; ?>;
        }
        .menu-section {
            margin: 20px 0;
            padding: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .menu-section h3 {
            margin-top: 0;
            color: #333;
        }
        .menu-section ul {
            list-style-type: none;
            padding-left: 20px;
        }
        .menu-section li {
            margin: 10px 0;
        }
        .menu-section a {
            text-decoration: none;
            color: #0066cc;
        }
        .menu-section a:hover {
            text-decoration: underline;
        }
        .admin-actions {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px dashed #ccc;
        }
        .admin-actions a {
            color: #cc0000;
        }
    </style>
</head>
<body>
<h1>Benvenuto nella Gestione Alternanza Scuola-Lavoro</h1>

<div>
    <h2>Benvenuto <?php echo htmlspecialchars($_SESSION['email']); ?></h2>
    <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
        <p class="admin-only">Stai accedendo come amministratore</p>
    <?php endif; ?>
</div>

<div class="menu-section">
    <h3>Gestione Studenti</h3>
    <ul>
        <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
            <li><a href="tabelle_inserisci/inserisci_studenti.php">Inserisci Studente</a></li>
            <li><a href="tabelle_gestione/gestione_studenti.php">Gestione Studenti</a></li>
        <?php else: ?>
            <li><a href="tabelle_visualizza/visualizza_studenti.php">Visualizza Studenti</a></li>
        <?php endif; ?>
    </ul>
</div>

<div class="menu-section">
    <h3>Gestione Aziende</h3>
    <ul>
        <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
            <li><a href="tabelle_inserisci/inserisci_aziende.php">Inserisci Azienda</a></li>
            <li><a href="tabelle_gestione/gestione_aziende.php">Gestione Aziende</a></li>
        <?php else: ?>
            <li><a href="tabelle_visualizza/visualizza_aziende.php">Visualizza Aziende</a></li>
        <?php endif; ?>
    </ul>
</div>

<div class="menu-section">
    <h3>Gestione Classi</h3>
    <ul>
        <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
            <li><a href="tabelle_inserisci/inserisci_classi.php">Inserisci Classe</a></li>
            <li><a href="tabelle_gestione/gestione_classi.php">Gestione Classi</a></li>
        <?php else: ?>
            <li><a href="tabelle_visualizza/visualizza_classi.php">Visualizza Classi</a></li>
        <?php endif; ?>
    </ul>
</div>

<div class="menu-section">
    <h3>Gestione Docenti</h3>
    <ul>
        <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
            <li><a href="tabelle_inserisci/inserisci_docenti.php">Inserisci Docente</a></li>
            <li><a href="tabelle_gestione/gestione_docenti.php">Gestione Docenti</a></li>
        <?php else: ?>
            <li><a href="tabelle_visualizza/visualizza_docentireferenti.php">Visualizza Docenti</a></li>
        <?php endif; ?>
    </ul>
</div>

<div class="menu-section">
    <h3>Gestione Tutor</h3>
    <ul>
        <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
            <li><a href="tabelle_inserisci/inserisci_tutor.php">Inserisci Tutor</a></li>
            <li><a href="tabelle_gestione/gestione_tutor.php">Gestione Tutor</a></li>
        <?php else: ?>
            <li><a href="tabelle_visualizza/visualizza_tutoraziendali.php">Visualizza Tutor</a></li>
        <?php endif; ?>
    </ul>
</div>

<div class="menu-section">
    <h3>Gestione Stage</h3>
    <ul>
        <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
            <li><a href="tabelle_inserisci/inserisci_stage.php">Inserisci Stage</a></li>
            <li><a href="tabelle_gestione/gestione_stage.php">Gestione Stage</a></li>
        <?php else: ?>
            <li><a href="tabelle_visualizza/visualizza_stage.php">Visualizza Stage</a></li>
        <?php endif; ?>
    </ul>
</div>

<div class="menu-section">
    <h3>Account</h3>
    <ul>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</div>

<?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
    <div class="admin-only">
        <p>Sei un amministratore e puoi:</p>
        <ul>
            <li>Visualizzare tutti i dati</li>
            <li>Inserire nuovi dati</li>
            <li>Gestire i dati esistenti (modifica ed eliminazione)</li>
        </ul>
    </div>
<?php else: ?>
    <div class="user-only">
        <p>Sei un utente normale e puoi:</p>
        <ul>
            <li>Solo visualizzare i dati</li>
        </ul>
    </div>
<?php endif; ?>
</body>
</html>

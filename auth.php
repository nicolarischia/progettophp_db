<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include("../includes/connessione.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    $stmt = $pdo->prepare("SELECT * FROM utenti WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user["password"])) {
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["is_admin"] = isset($user["is_admin"]) ? $user["is_admin"] : false;
        header("Location: ../index.php");
    } else {
        echo "Credenziali non valide.";
    }
}

// Funzione per verificare se l'utente è admin
function isAdmin() {
    return isset($_SESSION["is_admin"]) && $_SESSION["is_admin"] === true;
}
?>

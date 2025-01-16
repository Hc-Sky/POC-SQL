<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

try {
    $pdo = new PDO("mysql:host=localhost;dbname=POC", "root", "root");

    if ($_GET['action'] === 'get_formations') {
        $stmt = $pdo->query("SELECT * FROM formations");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    } elseif ($_GET['action'] === 'get_documents' && isset($_GET['formation_id'])) {
        $stmt = $pdo->prepare("SELECT * FROM documents WHERE formation_id = ?");
        $stmt->execute([$_GET['formation_id']]);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    } elseif ($_GET['action'] === 'save_document' && isset($_POST['utilisateur_id'], $_POST['formation_id'], $_POST['titre'], $_FILES['file'])) {
        $chemin = '/Applications/MAMP/htdocs/POC/documents/' . basename($_FILES['file']['name']);
        move_uploaded_file($_FILES['file']['tmp_name'], $chemin);
        
        $stmt = $pdo->prepare("INSERT INTO documents (titre, chemin, formation_id) VALUES (?, ?, ?)");
        $stmt->execute([$_POST['titre'], $chemin, $_POST['formation_id']]);

        // Debugging information
        error_log("Document saved: " . json_encode(['titre' => $_POST['titre'], 'chemin' => $chemin, 'formation_id' => $_POST['formation_id']]));

        echo json_encode(['success' => true, 'message' => 'Document enregistré.']);
    } else {
        echo json_encode(['error' => 'Action inconnue.']);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
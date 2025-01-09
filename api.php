<?php
header("Content-Type: application/json");

try {
    $pdo = new PDO("mysql:host=localhost;dbname=POC", "root", "root");

    if ($_GET['action'] === 'get_formations') {
        $stmt = $pdo->query("SELECT * FROM formations");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    } elseif ($_GET['action'] === 'get_documents' && isset($_GET['formation_id'])) {
        $stmt = $pdo->prepare("SELECT * FROM documents WHERE formation_id = ?");
        $stmt->execute([$_GET['formation_id']]);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    } elseif ($_GET['action'] === 'save_document' && isset($_POST['utilisateur_id'], $_POST['document_id'], $_FILES['file'])) {
        $chemin = 'docs_modifies/' . basename($_FILES['file']['name']);
        move_uploaded_file($_FILES['file']['tmp_name'], $chemin);

        $stmt = $pdo->prepare("INSERT INTO documents_modifies (utilisateur_id, document_id, chemin_modifie) VALUES (?, ?, ?)");
        $stmt->execute([$_POST['utilisateur_id'], $_POST['document_id'], $chemin]);

        echo json_encode(['success' => true, 'message' => 'Document enregistré.']);
    } else {
        echo json_encode(['error' => 'Action inconnue.']);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>

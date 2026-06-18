<?php
session_start();

include '../config/db.php';


if (isset($_POST['update_hotline'])) {


    $id = $_POST['id'];
    $title = $_POST['title'];
    $contact_number = $_POST['contact_number'];
    $description = $_POST['description'];


    try {


        $stmt = $pdo->prepare("
            UPDATE hotlines
            SET
                title = ?,
                contact_number = ?,
                description = ?
            WHERE id = ?
        ");


        $stmt->execute([
            $title,
            $contact_number,
            $description,
            $id
        ]);


        $_SESSION['success'] = "Hotline updated successfully!";
    } catch (PDOException $e) {

        $_SESSION['error'] = $e->getMessage();
    }


    header("Location: emergency-contact.php");
    exit();
}

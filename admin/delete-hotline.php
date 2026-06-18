<?php

session_start();

include '../config/db.php';


if (isset($_GET['id'])) {


    $id = $_GET['id'];


    try {


        $stmt = $pdo->prepare("
            DELETE FROM hotlines
            WHERE id = ?
        ");


        $stmt->execute([$id]);


        $_SESSION['success'] = "Hotline deleted successfully!";
    } catch (PDOException $e) {

        $_SESSION['error'] = $e->getMessage();
    }
}


header("Location: emergency-contact.php");
exit();

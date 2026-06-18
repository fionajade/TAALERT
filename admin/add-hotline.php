<?php
session_start();

include '../config/db.php';


// ==========================
// ADD HOTLINE
// ==========================
if (isset($_POST['add_hotline'])) {

    try {

        $agency = $_POST['agency'];
        $title = $_POST['title'];
        $contact = $_POST['contact_number'];
        $description = $_POST['description'];


        $stmt = $pdo->prepare("
            INSERT INTO hotlines
            (
                area_name,
                title,
                contact_number,
                description,
                is_emergency
            )

            VALUES
            (
                ?,
                ?,
                ?,
                ?,
                true
            )
        ");


        $result = $stmt->execute([
            $agency,
            $title,
            $contact,
            $description
        ]);


        if ($result) {

            echo "<script>
                alert('Hotline Saved Successfully');
                window.location='emergency-contact.php';
            </script>";

            exit;

        }


    } catch(PDOException $e) {

        echo "DATABASE ERROR: " . $e->getMessage();

    }

}
// ==========================
// UPDATE HOTLINE
// ==========================
if (isset($_POST['update_contact'])) {

    try {

        $stmt = $pdo->prepare("
            UPDATE hotlines
            SET
                area_name = ?,
                title = ?,
                contact_number = ?,
                description = ?,
                updated_at = NOW()
            WHERE id = ?
        ");

        $stmt->execute([
            trim($_POST['area_name']),
            trim($_POST['title']),
            trim($_POST['contact_number']),
            trim($_POST['description']),
            $_POST['id']
        ]);

        header("Location: emergency-contact.php?success=updated");
        exit;
    } catch (PDOException $e) {

        die("Error updating hotline: " . $e->getMessage());
    }
}


// ==========================
// FETCH HOTLINES
// ==========================
$stmt = $pdo->query("
    SELECT
        id,
        area_name,
        title,
        contact_number,
        description
    FROM hotlines
    WHERE is_emergency = TRUE
    ORDER BY id ASC
");

$contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);

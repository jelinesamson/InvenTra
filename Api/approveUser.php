<?php
include 'config.php';
requireLogin();
if ($_POST['action'] == 'approve') {
    if (empty($_POST['role'])) {
        header("Location: ../Html/accounts.php?error=role");
        exit;
    }
}
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../Html/dashboard.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = intval($_POST['user_id']);
    $action = $_POST['action'];

    if ($action === 'approve') {
        $role = $_POST['role'];
        if (empty($role)) {
            header("Location: ../Html/accounts.php?error=no_role");
            exit;
        }

        if (!in_array($role, ['admin', 'cashier'])) {
            header("Location: ../Html/accounts.php?error=invalid_role");
            exit;
        }

        $stmt = $conn->prepare("UPDATE accounts SET status = 'approved', role = ? WHERE account_id = ?");
        $stmt->bind_param("si", $role, $user_id);
        $stmt->execute();

        header("Location: ../Html/accounts.php?success=approved");
        exit;

        } elseif ($action === 'delete') {

            $stmt = $conn->prepare("DELETE FROM accounts WHERE account_id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();

            header("Location: ../Html/accounts.php?success=deleted");
            exit;
        }
    if ($action === 'delete') {
    if ($user_id == $_SESSION['account_id']) {
        header("Location: ../Html/accounts.php?error=cannot_delete_self");
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM accounts WHERE account_id = ?");
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        header("Location: ../Html/accounts.php?success=deleted");
    } else {
        header("Location: ../Html/accounts.php?error=failed");
    }
    exit;
}
}


<?php
include "config.php";
if (isset($_POST['action'])) {
    if ($_POST['action'] == "store") {
		$payload = json_decode($_POST['payload']);

		if (!$payload) {
			echo json_encode([
				"status" => "failed",
				"message" => "Invalid data"
			]);
			exit;
    	}
		
		$hashedPassword = password_hash($payload->regpassword, PASSWORD_DEFAULT);

		 //  check duplicate email
		$check = $conn->prepare("SELECT account_id FROM accounts WHERE email = ?");
		$check->bind_param("s", $payload->regemail);
		$check->execute();
		$result = $check->get_result();

		if ($result->num_rows > 0) {
			echo json_encode([
				"status" => "failed",
				"message" => "Email already exists"
			]);
			exit;
		}
		
		$statement = $conn->prepare("INSERT INTO accounts (firstName, lastName, email, password, status) VALUES (?,?,?,?, 'pending')");
		$statement->bind_param("ssss", 
			$payload->firstName, 
			$payload->lastName, 
			$payload->regemail, 
			$hashedPassword
		);
		
		if ($statement->execute()) {
			echo json_encode([
				"status" => "success",
				"message" => "Registered successfully. Waiting for admin approval"
			]);
		} else {
			echo json_encode([
				"status" => "failed",
				"message" => "Failed to register"
			]);
		}
	}
	
	if ($_POST['action'] == "update") {
		
	}
	
	if ($_POST['action'] == "drop") {
		
	}
}

?>
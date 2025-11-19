<?php

// --- 1. Database Connection Configuration (REQUIRED) ---
// !!! CHANGE THESE VALUES TO MATCH YOUR LOCAL SERVER !!!
$servername = "localhost"; 
$username = "root";        
$password = "";            
$dbname = "adoptme_db";    // <-- MUST MATCH THE NAME YOU CREATED IN PHPMYADMIN!

// Check if the form was submitted using the POST method
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // --- 2. Sanitize and Collect Form Data ---
    // Safely collect and sanitize data
    $name       = htmlspecialchars(trim($_POST['name'] ?? ''));
    $email      = htmlspecialchars(trim($_POST['email'] ?? ''));
    $phone      = htmlspecialchars(trim($_POST['phone'] ?? ''));
    $address    = htmlspecialchars(trim($_POST['address'] ?? ''));
    $pet        = htmlspecialchars(trim($_POST['pet'] ?? ''));
    // If date is empty, set it to NULL for the database
    $visitDate  = trim($_POST['visitDate'] ?? NULL) ?: NULL; 
    $reason     = htmlspecialchars(trim($_POST['reason'] ?? ''));
    $agree      = isset($_POST['agree']) ? 'Yes' : 'No';

    // Basic Validation Check for required fields
    if (empty($name) || empty($email) || empty($pet)) {
        http_response_code(400);
   // --- 3. Establish Database Connection ---
    $conn = new mysqli($servername, $username, $password, $dbname);
    $db_error = '';
    $success = false; // Initialize success status

    // Check connection
    if ($conn->connect_error) {
        $db_error = "Connection Failed: " . $conn->connect_error;
    } else {
        
        // --- 4. Prepare and Execute SQL INSERT Statement ---
        $sql = "INSERT INTO applications (full_name, email, phone, address, pet_name, visit_date, reason, agree_to_terms) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                
        $stmt = $conn->prepare($sql);

        // Check if statement preparation failed (e.g., column names are wrong)
        if (!$stmt) {
             $db_error = "SQL Prepare Failed: " . $conn->error;
        } else {
            // Bind parameters and execute
            $stmt->bind_param("ssssssss", $name, $email, $phone, $address, $pet, $visitDate, $reason, $agree);
            
            $success = $stmt->execute();
            
            if (!$success) {
                $db_error = "SQL Insert Failed: " . $stmt->error;
            }
            
            // Close statement
            $stmt->close();
        }
        
        // Close connection
        $conn->close();
    }
        $success = $stmt->execute();
        
        if (!$success) {
            $db_error = "SQL Insert Failed: " . $stmt->error;
        }
        
        // Close statement and connection
        $stmt->close();
        $conn->close();
    }

    // --- 5. Confirmation Page Output ---
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Received</title>
    <link rel="stylesheet" href="pet.css" /> 
    <style>
        .confirmation-container {
            max-width: 700px;
            margin: 120px auto 48px;
            padding: 20px;
            background: <?php echo $db_error ? '#ffe6e6' : '#e6ffe6'; ?>; /* Red for error, Green for success */
            border: 2px solid <?php echo $db_error ? '#c0392b' : '#4CAF50'; ?>;
            border-radius: 12px;
            text-align: center;
        }
        .confirmation-container h1 {
            color: <?php echo $db_error ? '#c0392b' : '#4CAF50'; ?>;
        }
        .details {
            text-align: left;
            margin-top: 30px;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }
        .details p {
            margin: 8px 0;
        }
        .details strong {
            display: inline-block;
            width: 150px;
            color: #333;
        }
    </style>
</head>
<body>
    <main>
        <div class="confirmation-container">
            <?php if (empty($db_error)): ?>
                <h1>🎉 Application Submitted Successfully!</h1>
                <p>Your application has been **saved to the database**.</p>
                <p>Thank you, **<?php echo htmlspecialchars($name); ?>**, for your interest in adoption. We will review it shortly.</p>
            <?php else: ?>
                <h1>❌ Submission Failed</h1>
                <p>There was a **critical server error** while saving your application. Please check your database settings.</p>
                <p>Technical details: <strong><?php echo htmlspecialchars($db_error); ?></strong></p>
                <p>Please try again or contact support.</p>
            <?php endif; ?>
            
            <div class="details">
                <h3>Summary of Your Application</h3>
                <p><strong>Pet Requested:</strong> <?php echo htmlspecialchars($pet); ?></p>
                <p><strong>Preferred Date:</strong> <?php echo htmlspecialchars($visitDate) ?: 'Not specified'; ?></p>
                <p><strong>Your Email:</strong> <?php echo htmlspecialchars($email); ?></p>
                </div>
            <p style="margin-top: 25px;">You can now return to the <a href="Pet.html">Pets page</a>.</p>
        </div>
    </main>
</body>
</html>
<?php

} else {
    // Redirect if accessed directly
    header('Location: Pet.html');
    exit;
}

?>
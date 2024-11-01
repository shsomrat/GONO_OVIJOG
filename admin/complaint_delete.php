<?php
// Include the database configuration
require_once 'config.php';

// Check if a complaint_id is passed in the URL
if (isset($_GET['complaint_id'])) {
    $complaint_id = (int)$_GET['complaint_id'];

    // Prepare the SQL statement to delete the complaint
    $sql = "DELETE FROM complaints WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $complaint_id);

    // Execute the deletion
    if ($stmt->execute()) {
        // Respond with success message
        $response = array(
            "success" => true,
            "message" => "Complaint ID $complaint_id deleted successfully!"
        );
    } else {
        // Respond with error message
        $response = array(
            "success" => false,
            "message" => "Error deleting complaint: " . $conn->error
        );
    }

    // Close the statement
    $stmt->close();
} else {
    // Respond with error if no complaint_id is provided
    $response = array(
        "success" => false,
        "message" => "No complaint ID provided."
    );
}

// Close the database connection
$conn->close();

// Return the response as JSON
header('Content-Type: application/json');
echo json_encode($response);
?>

<?php
    $inData = getRequestInfo();

    //grab user input
    $firstName = isset($inData["firstName"]) ? trim($inData["firstName"]) : "";
    $lastName = isset($inData["lastName"]) ? trim($inData["lastName"]) : "";
    $email = isset($inData["email"]) ? trim($inData["email"]) : "";
    $contactID = isset($inData["contactID"]) ? $inData["contactID"] : null;
    $ownerId = isset($inData["ownerId"]) ? $inData["ownerId"] : null;

    //check if required fields are present
    if (empty($firstName) || empty($lastName) || empty($email) || empty($contactID) || empty($ownerId)) {
        returnWithError("Missing required fields.");
        exit;
    }

    //ensure contactID is a valid integer
    if (!is_numeric($contactID) || $contactID <= 0) {
        returnWithError("Invalid contact ID.");
        exit;
    }

    //attempt to establish database connection
    $conn = new mysqli("localhost", "cmapi", "b4ckend!", "ContactManager");

    //failed connection
        if( $conn->connect_error )
        {
                returnWithError("Database Connection Failed: " . $conn->connect_error);
        exit;
        }

    //check if contact exists before updating AND ensure it belongs to the correct user
    $checkStmt = $conn->prepare("SELECT * FROM Contacts WHERE ID = ? AND ContactOwnerID = ?");
    $checkStmt->bind_param("ii", $contactID, $ownerId);
    $checkStmt->execute();
    $result = $checkStmt->get_result();

    if ($result->num_rows === 0) {
        returnWithError("Contact not found or does not belong to this user.");
        $checkStmt->close();
        $conn->close();
        exit;
    }

    $checkStmt->close();

    // successful connection
    // else {

        $stmt = $conn->prepare("UPDATE Contacts SET FirstName=?, LastName=?, Email=? WHERE ID=?");
        $stmt->bind_param("sssi", $firstName, $lastName, $email, $contactID);
        $stmt->execute();
        $stmt->close();
        $conn->close();

        returnWithInfo($firstName, $lastName, $contactID);
    // }

    function getRequestInfo()
        {
                return json_decode(file_get_contents('php://input'), true);
        }
    function returnWithError( $err )
        {
                $retValue = ["id" => 0, "firstName" => "", "lastName" => "", "error" => $err];
                sendResultInfoAsJson($retValue);
        }

    function returnWithInfo( $firstName, $lastName, $contactID )
        {
                $retValue = ["id" => $contactID, "firstName" => $firstName, "lastName" => $lastName, "error" => ""];
                sendResultInfoAsJson($retValue);
        }

    function sendResultInfoAsJson($obj)
    {
        header('Content-type: application/json');
        echo json_encode($obj);
    }
?>

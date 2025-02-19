<?php
	$inData = getRequestInfo();

    	//important data for Contacts Table
	//info of the contact to be created
    $first = isset($inData["firstName"]) ? trim($inData["firstName"]) : "";
    $last = isset($inData["lastName"]) ? trim($inData["lastName"]) : "";
    $email = isset($inData["email"]) ? trim($inData["email"]) : "";
    $ownerId = isset($inData["ownerId"]) ? $inData["ownerId"] : null;

    //check if required fields are present
    if (empty($first) || empty($last) || empty($email) || empty($ownerId)) {
        returnWithError("Missing required fields.");
        exit;
    }

	$conn = new mysqli("localhost", "cmapi", "b4ckend!", "ContactManager");
	if ($conn->connect_error)
	{
		returnWithError( $conn->connect_error );
	}
	else
	{
    	//making new entry into contacts using these fields
		$stmt = $conn->prepare("INSERT into Contacts (ContactOwnerID, Email, FirstName, LastName, DateCreated, DateUpdated) VALUES(?,?,?,?, NOW(), NOW())");

		$stmt->bind_param("ssss", $ownerId, $email, $first, $last);
		$stmt->execute();
     
		$id = $stmt->insert_id;

		$stmt->close();
		$conn->close();

		returnWithInfo($id, $first, $last, $email, $ownerId);
	}

	function getRequestInfo()
	{
		return json_decode(file_get_contents('php://input'), true);
	}

	function returnWithInfo( $id, $first, $last, $email, $ownerId )
	{
		$retValue = ["id" => $id, "ownerId" => $ownerId, "firstName" => $first, "lastName" => $last, "email" => $email, "error" => ""];
		sendResultInfoAsJson($retValue);
	}

	function returnWithError( $err )
	{
		$retValue = ["error" => $err];
		sendResultInfoAsJson( $retValue );
	}
 
  	function sendResultInfoAsJson($obj)
    {
        header('Content-type: application/json');
        echo json_encode($obj);
    }
?>

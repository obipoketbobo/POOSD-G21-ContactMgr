<?php
	$inData = getRequestInfo();
	
	//user input login
	$id = 0;
	$email = $inData["email"];
	$password = $inData["password"];

	$conn = new mysqli("localhost", "cmapi", "b4ckend!", "ContactManager"); 	
	if( $conn->connect_error )
	{
		returnWithError( $conn->connect_error );
	}
	else
	{
		$stmt = $conn->prepare("SELECT ID, Email, Password, FirstName, LastName FROM Users WHERE Email=? AND Password =?");
		$stmt->bind_param("ss", $inData["email"], $inData["password"]);
		$stmt->execute();
		$result = $stmt->get_result();

		if( $row = $result->fetch_assoc()  )
		{
			returnWithInfo( $row['FirstName'], $row['LastName'], $row['ID'] );
		}
		else
		{
			returnWithError("Invalid credentials.");
		}

		$stmt->close();
		$conn->close();
	}
	
	function getRequestInfo()
	{
		return json_decode(file_get_contents('php://input'), true);
	}

	function returnWithInfo( $firstName, $lastName, $id )
	{
		$retValue = ["id" => $id, "firstName" => $firstName, "lastName" => $lastName, "error" => ""];
		sendResultInfoAsJson( $retValue );
	}

	function returnWithError( $err )
	{
		$retValue = ["id" => 0, "error" => $err];
		sendResultInfoAsJson( $retValue );
	}

    function sendResultInfoAsJson($obj)
    {
        header('Content-type: application/json');
        echo json_encode($obj);
    }
?>

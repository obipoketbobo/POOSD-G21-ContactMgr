
<?php	
	$inData = getRequestInfo();

	$id = $inData["contactID"];

	$conn = new mysqli("localhost", "cmapi", "b4ckend!", "ContactManager"); 	
	if( $conn->connect_error )
	{
		returnWithError( $conn->connect_error );
	}
	else
	{
		$stmt = $conn->prepare("DELETE FROM Contacts WHERE ID=?");
		$stmt->bind_param("i", $id);

		$stmt->execute();

		$stmt->close();
		$conn->close();

		returnWithError("");
	}
		
	function getRequestInfo()
	{
		return json_decode(file_get_contents('php://input'), true);
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

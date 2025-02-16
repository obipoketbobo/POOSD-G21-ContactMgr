<?php
    $inData = getRequestInfo();

    //grab user input
    $firstName = $inData["firstName"];
    $lastName = $inData["lastName"];
    $email = $inData["email"];
    $contactID = $inData["contactID"];

    //attempt to establish database connection
    $conn = new mysqli("localhost", "cmapi", "b4ckend!", "ContactManager");
    //failed connection
	if( $conn->connect_error )
	{
		returnWithError( $conn->connect_error );
	}
    //successful connection
	else
	{
        //update contact
        $stmt = $conn->prepare("UPDATE Contacts SET FirstName=?, LastName=?, Email=? WHERE ID=?");
        $stmt->bind_param("sssi", $firstName, $lastName, $email, $contactID);

        //successfully executed update
        if($stmt->execute())
        {
            returnWithInfo($firstName, $lastName, $contactID);
        }
        //failed to execute update
        else
        {
            returnWithError("Unable to update contact.");
        }

        $stmt->close();
        $conn->close();
    }

    function getRequestInfo()
	{
		return json_decode(file_get_contents('php://input'), true);
	}

    function returnWithError( $err )
	{
		$retValue = '{"id":0,"firstName":"","lastName":"","error":"' . $err . '"}';
		sendResultInfoAsJson( $retValue );
	}

    function returnWithInfo( $firstName, $lastName, $contactID )
	{
		$retValue = '{"id":' . $contactID . ',"firstName":"' . $firstName . '","lastName":"' . $lastName . '","error":""}';
		sendResultInfoAsJson( $retValue );
	}

    function sendResultInfoAsJson($obj)
    {
        header('Content-type: application/json');
        echo $obj;
    }
?>
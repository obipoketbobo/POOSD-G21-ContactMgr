<?php
    $inData = getRequestInfo();

    //grab user input
    $firstName = $inData["firstName"];
    $lastName = $inData["lastName"];
    $email = $inData["email"];
    $password = $inData["password"];

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
        //determine if email already in use
        $stmt = $conn->prepare("SELECT ID FROM Users WHERE Email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        //user already exists
        if($row = $result->fetch_assoc())
        {
            //close connections
            $stmt->close();
            $conn->close();

            returnWithError("User already exists.");
        }
        //create new user
        else
        {
            //add account information to database
            $stmt = $conn->prepare("INSERT into Users (FirstName, LastName, Email, Password, DateCreated)
                                        Values(?, ?, ?, ?, NOW())");
            $stmt->bind_param("ssss", $firstName, $lastName, $email, $password);
            $stmt->execute();

            $user_id = $stmt->insert_id; //save user's id for returning

            //close connections
            $stmt->close();
		    $conn->close();

            returnWithInfo($firstName, $lastName, $user_id);
        }
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

    function returnWithInfo( $firstName, $lastName, $id )
	{
		$retValue = '{"id":' . $id . ',"firstName":"' . $firstName . '","lastName":"' . $lastName . '","error":""}';
		sendResultInfoAsJson( $retValue );
	}

    function sendResultInfoAsJson($obj)
    {
        header('Content-type: application/json');
        echo $obj;
    }
?>
<?php

	$inData = getRequestInfo();
	
	$searchResults = "";
	$searchCount = 0;

	$conn = new mysqli("localhost", "cmapi", "b4ckend!", "ContactManager");
	if ($conn->connect_error) 
	{
		returnWithError( $conn->connect_error );
	} 
	else
	{
		// Select FirstName, LastName, and Email columns from Contacts table
		// Searches for specific strings in the FirstName and LastName columns
		// Limited to entries with a specific ContactOwnerID
		$stmt = $conn->prepare("SELECT FirstName, LastName, Email, ID FROM Contacts WHERE FirstName LIKE ? AND LastName LIKE ? AND Email LIKE ? AND ContactOwnerID=?");
		
		//stores first and last names from the input
		$firstName = "%" . $inData["first"] . "%";
		$lastName = "%" . $inData["last"] . "%";
		$email = "%" . $inData["email"] . "%";
		
		//fill in prepared statement with the input
		$stmt->bind_param("sssi", $firstName, $lastName, $email, $inData["ownerId"]);
		$stmt->execute();
		
		$result = $stmt->get_result();
		
		// Initialize an array to store search results
		$searchResultsArray = [];
		
		while($row = $result->fetch_assoc())
		{
			// Append each contact's details to the array
			$searchResultsArray[] = [
				"firstName" => $row["FirstName"],
				"lastName" => $row["LastName"],
				"email" => $row["Email"],
        "contactID" => $row["ID"]
			];
			$searchCount++;
		}
		
		// If no records are found, return an error message
		if( $searchCount == 0 )
		{
			returnWithError( "No Records Found" );
		}
		else
		{
			returnWithInfo( $searchResultsArray );
		}
		
		$stmt->close();
		$conn->close();
	}

	// Function to decode the JSON request body
	function getRequestInfo()
	{
		return json_decode(file_get_contents('php://input'), true);
	}

	// Function to send a JSON response
	function sendResultInfoAsJson( $obj )
	{
		header('Content-type: application/json');
		echo json_encode($obj);
	}
	
	// Function to return an error message in JSON format
	function returnWithError( $err )
	{
		$retValue = ["results" => [], "error" => $err];
		sendResultInfoAsJson( $retValue );
	}
	
	// Function to return search results in JSON format
	function returnWithInfo( $searchResults )
	{
		$retValue = ["results" => $searchResults, "error" => ""];
		sendResultInfoAsJson( $retValue );
	}
	
?>
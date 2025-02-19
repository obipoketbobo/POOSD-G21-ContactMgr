<?php
        $inData = getRequestInfo();

        //important data for Contacts Table
        //user creating contact
        $ownerId = $inData["ownerId"];
        //new contact's email
        $email = $inData["email"];
        //new contact's first name
        $first = $inData["firstName"];
        //new contact's last name
        $last = $inData["lastName"]


        $conn = new mysqli("localhost", "cmapi", "b4ckend!", "ContactManager");
        if ($conn->connect_error)
        {
                returnWithError( $conn->connect_error );
        }
        else
        {
                //making new entry into contacts using these fields
                $stmt = $conn->prepare("INSERT into Contacts (ContactOwnerID, Email, FirstName, LastName) VALUES(?,?,?,?)");

                //maybe ssss ?
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

        function sendResultInfoAsJson( $obj )
        {
                header('Content-type: application/json');
                echo $obj;
        }

        function returnWithInfo( $id, $firstName, $lastName, $email, $ownerId )
        {
                $retValue = '{"ID":"' . $id . '", "ownerID":"' . $ownerId . '","firstName":"' . $firstName . '","lastName":"' . $lastName . '","email":"' . $email . '","error":""}';
                sendResultInfoAsJson( $retValue );
        }

        function returnWithError( $err )
        {
                $retValue = '{"error":"' . $err . '"}';
                sendResultInfoAsJson( $retValue );
        }

?>

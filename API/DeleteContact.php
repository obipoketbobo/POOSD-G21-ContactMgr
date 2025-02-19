
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
        }

        function returnWithError( $err )
        {
                $retValue = '{"error":"' . $err . '"}';
                sendResultInfoAsJson( $retValue );
        }
?>

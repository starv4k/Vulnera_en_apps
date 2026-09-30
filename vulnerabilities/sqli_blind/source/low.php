<?php

if( isset( $_GET[ 'Submit' ] ) ) {
        // Get input
        $id = $_GET[ 'id' ];
        $exists = false;

        switch ($_DVWA['SQLI_DB']) {
                case MYSQL:
                        // Check database con Prepared Statements (MySQLi)
                        try {
                                $stmt = mysqli_prepare($GLOBALS["___mysqli_ston"], "SELECT first_name, last_name FROM users WHERE user_id = ?;");
                                if ($stmt) {
                                        // Vincula la variable $id como parámetro de tipo string ("s")
                                        mysqli_stmt_bind_param($stmt, "s", $id);
                                        mysqli_stmt_execute($stmt);
                                        $result = mysqli_stmt_get_result($stmt);
                                        
                                        $exists = ($result && mysqli_num_rows($result) > 0);
                                        mysqli_stmt_close($stmt);
                                }
                        } catch (Exception $e) {
                                print "There was an error.";
                                exit;
                        }

                        ((is_null($___mysqli_res = mysqli_close($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);
                        break;

                case SQLITE:
                        global $sqlite_db_connection;

                        // Check database con Prepared Statements (SQLite3)
                        try {
                                $stmt = $sqlite_db_connection->prepare("SELECT first_name, last_name FROM users WHERE user_id = :id;");
                                if ($stmt) {
                                        $stmt->bindValue(':id', $id, SQLITE3_TEXT);
                                        $results = $stmt->execute();
                                        $row = $results->fetchArray();
                                        $exists = ($row !== false);
                                        $stmt->close();
                                }
                        } catch(Exception $e) {
                                $exists = false;
                        }

                        break;
        }

        if ($exists) {
                // Feedback for end user
                $html .= '<pre>User ID exists in the database.</pre>';
        } else {
                // User wasn't found, so the page wasn't!
                header( $_SERVER[ 'SERVER_PROTOCOL' ] . ' 404 Not Found' );

                // Feedback for end user
                $html .= '<pre>User ID is MISSING from the database.</pre>';
        }

}

?>

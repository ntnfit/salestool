<?php

namespace App\Http\Controllers\sap;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SAPB1Controller extends Controller
{
    function connect(){
        $host='115.84.182.179:30015';
        $driver = '/home/nguyen/sap/hdbclient/libodbcHDB.so';
        $db_name = "HDB";
        // Username
        $username = 'SYSTEM';
        // Password
        $password = " S@p@HanaSapb1";
        $conn = odbc_connect("Driver=$driver;ServerNode=$host;Database=$db_name;", $username, $password, SQL_CUR_USE_ODBC);
        if (!$conn)
        {
            // Try to get a meaningful error if the connection fails
            echo "Connection failed.\n";
            echo "ODBC error code: " . odbc_error() . ". Message: " . odbc_errormsg();
        
        }
        else
        {
            // Do a basic select from DUMMY with is basically a synonym for SYS.DUMMY
            $sql = "SELECT '111' FROM DUMMY";
            $result = odbc_exec($conn, $sql);
            if (!$result)
            {
                echo "Error while sending SQL statement to the database server.\n";
                echo "ODBC error code: " . odbc_error() . ". Message: " . odbc_errormsg();
            }
            else
            {
                while ($row = odbc_fetch_object($result))
                {
                    // Should output one row containing the string 'X'
                    var_dump($row);
                }
            }
            odbc_close($conn);
        }
    }
}

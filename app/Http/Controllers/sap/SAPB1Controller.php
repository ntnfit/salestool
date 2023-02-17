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
    function connectSetup (Request $request)
    {
        try {
            $data=$request->all();
            //$request->port
            $username = '{"CompanyDB":"'.$request->CompanyDB.'","UserName":"'.$request->username.'"}';
            $password = "manager";
            $authString = base64_encode("$username:$password");
            $headers = $authString;

           $path = base_path('.env');
           
           $key="BSHeader";
           $value=$headers;
           if (file_exists($path)) {
            //headers
            file_put_contents($path, str_replace(
                "$key=" . env($key), "$key=" . $value, file_get_contents($path)));
            //namserserver
            file_put_contents($path, str_replace(
                "SAP_SERVER=" . env("SAP_SERVER"), "SAP_SERVER=". $request->servername, file_get_contents($path)));
            //port
            file_put_contents($path, str_replace(
                "SAP_PORT=" . env("SAP_PORT"), "SAP_PORT=". $request->port, file_get_contents($path)));
            //CompanyDB
            file_put_contents($path, str_replace(
                "SAP_DB=" . env("SAP_DB"), "SAP_DB=". $request->CompanyDB, file_get_contents($path)));
            //Username
            file_put_contents($path, str_replace(
                "user_name=" . env("user_name"), "user_name=". $request->username, file_get_contents($path)));
            //Password
            file_put_contents($path, str_replace(
                "password=" . env("password"), "password=". $request->password, file_get_contents($path)));

            
         }
         return redirect()->route('setup-connect')->with('success','User created successfully');
        }
        catch (Throwable $e)
        {
            return redirect()->route('setup-connect')->with('error',$e);
        }
       

      
    }
    function connect_saphan_db()
    {
        $username = “<HANA User ID”;
            $password = “<HANA password>”;
            $dsn =”odbc:<name of Data Source>”;$queryString = ‘SELECT …’;
            try {
            // Connect to the data source
            $dbh = new PDO($dsn, $username, $password);
            $stmt = $dbh->prepare($queryString);
            $stmt -> execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);.
            Then I wrote some manual logic to parse a JSON structure from the result set, and then closed out the PHP file:
            }
            catch (Exception $e) {
                echo $e->getMessage();
            }
    }
}

<?php 

# server name
// $sName = "localhost";
# user name
// $uName = "u679317752_tatvam";
# password
// $pass = "Tatvam@1313";

# database name
// 


$sName = "localhost";
$uName = "root";
$pass = "";
$db_name = "tatvam";


/**
creating database connection 
useing The PHP Data Objects (PDO)
**/
try {
    $conn = new PDO("mysql:host=$sName;dbname=$db_name", 
                    $uName, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}catch(PDOException $e){
  echo "Connection failed : ". $e->getMessage();
}
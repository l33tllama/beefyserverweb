<!DOCTYPE HTML>
<?php
$username = "";
$password = "";
if(isset($_GET['user'])){
    if(isset($_GET['pass'])){
        $username = $_GET['user'];
        $password = $_GET['pass'];
        // If I had a database of users, I'd check if user exists, then get the user's MD5(or better) PW hash and compare.
        if($username == "leo"){
            if($password == "3bb7b584635d792fd74778558371bf37"){
                echo "SUCCESS";
            } else{
                echo "PW_FAIL";
            }
        }
        else {
            echo "UN_FAIL";
        }
    }
    else {
        echo "PW_FAIL";
    }
} else {
    echo "UN_FAIL";
}
?>
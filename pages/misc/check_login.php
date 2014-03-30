<?php
$username = "";
$password = "";
if(isset($_GET['user'])){
    if(isset($_GET['pass'])){
        $username = $_GET['user'];
        $password = $_GET['pass'];
        
        // MongoDB connection
        $mongo_conn = new Mongo('localhost');
        
        // Games Lists DB
        $games_lists = $mongo_conn->games_lists;
        
        // Users collection
        $users_coll = $games_lists->users;
        
        // User from MongoDB
		$user = $users_coll->findOne(array('username' => $username));
		if ($user === null){
			echo "UN_FAIL";
		} else {
			// Get user's hashed passsword and check with entered
			$user_pw_hash = $user['password_md5'];
			if($user_pw_hash == $password){
				// Create new session ID/token
				$rand_session = "";
				for($i = 0; $i < 16; $i++)
					$rand_session = $rand_session . rand(0, 9);
				// Update user's session ID for when returning
				$users_coll->update(array("username"=>$username), array('$set'=>array("session_id"=>$rand_session)));
				echo "SUCCESS-";
				echo $rand_session;
			}
		}
        $mongo_conn->close();
    }
    else {
        echo "PW_FAIL";
    }
} else {
    echo "UN_FAIL";
}
?>
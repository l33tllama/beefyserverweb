<?php
$username = "";
$password = "";
if(isset($_GET['user'])){
    if(isset($_GET['session_id'])){
        $username = $_GET['user'];
        $session_id = $_GET['session_id'];
        
        // MongoDB connection
        $mongo_conn = new Mongo('localhost');
        
        // Games Lists DB
        $games_lists = $mongo_conn->games_lists;
        
        // Users collection
        $users_coll = $games_lists->users;
        
        // User from MongoDB
		$user = $users_coll->findOne(array('username' => $username));
		if ($user === null){
			echo "UN_MISMATCH";
		} else {
		
			// Get previous session id stored for user
			$user_session_id = $user['session_id']; 
			if($user_session_id == $session_id){
				
				// Create new session ID/token
				$rand_session = "";
				for($i = 0; $i < 16; $i++)
					$rand_session = $rand_session . rand(0, 9);
				
				// Update session ID to a new one
				$users_coll->update(array("username"=>$username), array('$set'=>array("session_id"=>$rand_session)));
				echo "SUCCESS-";
				echo $rand_session;
			} else {
				echo "SESS_MISMATCH";
			} 
			
		}
        $mongo_conn->close();
    }
    else {
        echo "SESS_FAIL";
    }
} else {
    echo "UN_FAIL";
}
?>
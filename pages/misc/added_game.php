<?php
try{
	/*
     * filtering an array
     */
    function filter_by_value ($array, $index, $value){
        if(is_array($array) && count($array)>0) 
        {
            foreach(array_keys($array) as $key){
                $temp[$key] = $array[$key][$index];
                if ($temp[$key] == $value){
                    $newarray[$key] = $array[$key];
                }
            }
          }
      return $newarray;
    } 
	if(isset($_GET['gamename']) && isset($_GET['platform']) &&  isset($_GET['username'])){
		$gamename = urldecode($_GET['gamename']);
		$username = $_GET['username'];
		$session_id = $_COOKIE['session_id'];
		$platform = $_GET['platform'];		
		
		// create new clean name:
		// remove non-alphanumeric characters
		// lower case
		// replace space with "-"
		$gamename_clean = $new_game_name_clean = str_replace(" ", "-", strtolower(preg_replace('/[^a-z\d ]/i', '', $new_game_name)));
		
		// MongoDB connection
		$mongo_conn = new Mongo('localhost');
		
		// Connect to games DB
		$games_lists = $mongo_conn->games_lists;
		
		// Get user's DB
		$user_acct = $games_lists->users->findOne(array("username" => $username));
		
		
		// Check if session ID is correct (no need to set a new one..)
		if($user_acct != null && ($user_acct['session_id'] == $session_id)){
			//TODO: Add game here
			// create new clean name:
			// remove non-alphanumeric characters
			// lower case
			// replace space with "-"
			if($platform == "linux" || $platform == "win"){
				$new_game_name_clean = str_replace(" ", "-", strtolower(preg_replace('/[^a-z\d ]/i', '', $new_game_name)));
				$new_game = array("GameName" => $gamename, 
								"GameNameClean" => $new_game_name_clean, 
								"Platform" => $platform, 
								"Category" => "new", 
								"DateAdded" => "", 
								"PlayedState" => "unplayed");
				// Get games list
				$user_game_list = $user_acct['steam_puap_games'];
				array_push($user_game_list, $new_game);
				$games_lists->users->update(array("username" => $username), array('$set' => array("steam_puap_games" => $user_game_list)));
				echo "SUCCESS: $gamename has been ported.";
				
			} else {
				echo "ERR: Invalid platform: $platform.";
			}
		} else {
			echo "ERR: Sesion ID incorrect or wrong username.";
		}
		
		
	} else {
		echo "ERR: Not enough URL vars set.";
	}
}catch (MongoConnectionException $e){
	echo $e->getMessage();
}?>
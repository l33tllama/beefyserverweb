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
	if(isset($_GET['gamename']) && isset($_GET['username'])){
		$gamename = $_GET['gamename'];
		$username = $_GET['username'];
		$session_id = $_COOKIE['session_id'];
		
		// MongoDB connection
		$mongo_conn = new Mongo('localhost');
		
		// Connect to games DB
		$games_lists = $mongo_conn->games_lists;
		
		// Get user's DB
		$user_acct = $games_lists->users->findOne(array("username" => $username));
		
		// Check if session ID is correct (no need to set a new one..)
		if($user_acct != null && ($user_acct['session_id'] == $session_id)){
		
		// Get games list
 			$user_game_list = $user_acct['steam_puap_games'];
 			
 			// Filter unplayed games
 			$unplayed_games = filter_by_value($user_game_list, 'PlayedState', 'unplayed');
 			
 			// Filter game to modify (if found)
 			$game_to_edit = filter_by_value($user_game_list, 'GameNameClean', $gamename);

			// New list of games to send back to user's DB
 			$new_games_list = array();
 			
 			$already_ported = False;
 			
 			// If game found
 			if($game_to_edit != null){
				
				// Loop through all games
				foreach($user_game_list as $user_game){
				
					// If game is an unplayed
					if($user_game['PlayedState'] == 'unplayed'){
					
						// If found game
						if($user_game['GameNameClean'] == $gamename){
							if($user_game['Platform'] == "linux"){
								
								$already_ported = True;
							} else {
								$user_game['Platform'] = "linux";
							}
						}
					}
					array_push($new_games_list, $user_game);
				}
				if(!$already_ported){
					$games_lists->users->update(array("username" => $username), array('$set' => array("steam_puap_games" => $new_games_list)));
					echo "SUCCESS: $gamename has been ported.";
				} else {
					echo "ERR: $gamename already on Linux!";
				}
				
			} else {
				echo "ERR: Game $gamename not found!<br/>";
			}
		} else {
			echo "ERR: Username invalid or sessionID incorrect.<br/>";
		}
	} else {
		echo "ERR: Not enough URL variables set. <br/>";
	}
}catch (MongoConnectionException $e){
	echo $e->getMessage();
}?>

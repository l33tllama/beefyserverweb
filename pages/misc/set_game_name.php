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
    // ###################################### INPUTS ##############################################
	if(isset($_GET['gamename']) && isset($_GET['new_gamename']) && isset($_GET['username'])){
		$game_name = $_GET['gamename'];
		$new_game_name = $_GET['new_gamename'];
		$username = $_GET['username'];
		$session_id = $_COOKIE['session_id'];
	// ############################################################################################
		
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
 			$game_to_edit = filter_by_value($user_game_list, 'GameNameClean', $game_name);
 			
 			$new_games_list = array();
 			$new_game_name_clean = "";
 			
 			// If game found
 			if($game_to_edit != null){
				
				// Loop through all games
				foreach($user_game_list as $user_game){
				
					// If game is an unplayed
					if($user_game['PlayedState'] == 'unplayed'){
					
						// If found game
						if($user_game['GameNameClean'] == $game_name){
							
							// create new clean name:
							// remove non-alphanumeric characters
							// lower case
							// replace space with "-"
							$new_game_name_clean = str_replace(" ", "-", strtolower(preg_replace('/[^a-z\d ]/i', '', $new_game_name)));
							
							$user_game['GameName'] = $new_game_name;
							$user_game['GameNameClean'] = $new_game_name_clean;
							//echo "Updating $game_name to $new_game_name_clean/$new_game_name<br/>";
						}
					}
					array_push($new_games_list, $user_game);
					
				}
				$games_lists->users->update(array("username" => $username), array('$set' => array("steam_puap_games" => $new_games_list)));
		// ################################################# OUTPUTS ########################################
					echo "SUCCESS: Game name modified. Was: $game_name, now: $new_game_name_clean";
			} else {
				echo "ERR: Game: $game_name not found.<br/>";
			}
		} else {
			echo "ERR: Bad username/sessionID.<br/>";
		}
	} else {
		echo "ERR: Not all URL variables set.<br/>";
		// ###################################################################################################
	}
}catch (MongoConnectionException $e){
	echo $e->getMessage();
}?>		
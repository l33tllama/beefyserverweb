<?php
	function filter_by_value ($array, $index, $value){
        if(is_array($array) && count($array)>0) 
        {
            foreach(array_keys($array) as $key){
                $temp[$key] = $array[$key][$index];
//                 echo "$index value: " . $temp[$key] .", $value<br/>";
                
                if ($temp[$key] == $value){
                    $newarray[$key] = $array[$key];
                }
            }
          }
      return $newarray;
    }
	// Connect to MongoDB
	$mongo_conn = new Mongo('localhost');
	
	// Connect to games DB
	$games_lists = $mongo_conn->games_lists;

	$users_accts = $games_lists->users;
	
	$users_acct = $users_accts->findOne(array("username" => "leo"));
	
	$steam_puap_games = $users_acct['steam_puap_games'];
	
	foreach($steam_puap_games as $game){
		echo "Category: ";
		echo $game['Category'];
		echo "<br/>Platform: ";
		echo $game['Platform'];
		echo "<br/>Game Name: ";
		echo $game['GameName'];
		echo "<br/>";
		echo "<br/>";
	}
	
	$mongo_conn->close();
?>
<?php
$to_root = "../";
$o_low_games_lines = file($to_root."txt/sp_games_low.txt");
$o_med_games_lines = file($to_root."txt/sp_games_med.txt");
$o_high_games_lines = file($to_root."txt/sp_games_high.txt");

$low_games_lines = array();
$med_games_lines = array();
$high_games_lines = array();

$played_games_lines = file($to_root."txt/sp_played_games.txt");

$all_games = array($o_low_games_lines, $o_med_games_lines, $o_high_games_lines);

foreach($all_games as $id => $game_tier){
	foreach($game_tier as $game_id => $game){
		$game_to_skip = "";
		foreach($played_games_lines as $played_game){
			if($game == $played_game){
				$game_to_skip = $played_game;
			}
		}
		switch ($id) {
		case 0:
			if($game_to_skip != $game)
				array_push($low_games_lines, $game);
			break;
		case 1:
			if($game_to_skip != $game)
				array_push($med_games_lines, $game);
			break;
		case 2:
			if($game_to_skip != $game)
				array_push($high_games_lines, $game);
			break;
		default:
			break; 
		}
	}
}

$num_low = count($low_games_lines);
$num_med = count($med_games_lines);
$num_high = count($high_games_lines);
$scaled_low = 0;
$scaled_med = 0;
$scaled_high = 0;
$high_scaler = 6;
$med_scaler = 4;
$low_scaler = 2;
$largest = "";

$lsc = array($num_low, $num_med, $num_high);

//Find largest count of games
if ($lsc[0] >= $lsc[1] && $lsc[0] >= $lsc[2]){
	$largest = "low";
} else if ($lsc[1] >= $lsc[0] && $lsc[1] >= $lsc[2]){
	$largest = "med";
} else if ($lsc[2] >= $lsc[0] && $lsc[2] > $lsc[1]){
	$largest = "high";
}

//scale number of games to preference
switch($largest){
	case "low":
		$scaled_low = $num_low * $low_scaler;
		$scaled_med = $num_med * $med_scaler * ($num_low / $num_med);
		$scaled_high = $num_high * $high_scaler * ($num_low / $num_high);
		break;
	case "med":
		$scaled_low = $num_low * $low_scaler * ($num_med / $num_low);
		$scaled_med = $num_med * $med_scaler;
		$scaled_high = $num_high * $high_scaler * ($num_med / $num_high);
		break;
	case "high":
		$scaled_low = $num_low * $low_scaler * ($num_high / $num_low);
		$scaled_med = $num_med * $med_scaler * ($num_high / $num_med);
		$scaled_high = $num_high * $high_scaler;
		break;
}

//New way
$total_scaled = $scaled_high + $scaled_med + $scaled_low;
$total_actual = $num_high + $num_med + $num_low;

//add up random numbers of each probability of getting each list of games
$prob_low = $scaled_low / $total_scaled;
$prob_med = $scaled_med / $total_scaled;
$prob_high = $scaled_high / $total_scaled;

$low_sector = $prob_low * rand(0, 1000);
$med_sector = $prob_med * rand(0, 1000);
if($num_high =! 0){
	$high_sector = $prob_high * rand(0, 1000);
} else {
	$high_sector = 0;
}

$chosen_number = ($low_sector + $med_sector + $high_sector) / 1000;
$chosen_game = "";

if($chosen_number > 0 && $chosen_number <= $prob_low){
	while($chosen_game == null){
		$chosen_game = $low_games_lines[rand(0, $num_low)];
	}
} else if ($chosen_number > $prob_low && $chosen_number <= $prob_med ){
	while($chosen_game == null){
		$chosen_game = $med_games_lines[rand(0, $num_med)];
	}
} else if ($chosen_number > $prob_med && $chosen_number <= 1){
	while($chosen_game == null){
		$chosen_game = $high_games_lines[rand(0, $num_high)];
	}
}
//echo "chosen number: $chosen_number out of $total_actual\n";
echo $chosen_game;

file_put_contents($to_root."txt/sp_played_games.txt", $chosen_game, FILE_APPEND | LOCK_EX);
file_put_contents($to_root."txt/this_weeks_sp_game.txt", $chosen_game);

?>

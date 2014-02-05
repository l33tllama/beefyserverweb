<?php
$con = mysqli_connect("192.168.1.100", "mc_logger", "mc_logger1337", "minecraft_player_data");
// Check connection
if (mysqli_connect_errno($con)) {
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
} else {
	$log_cookie ='mc_log_date';
	// Check for no cookie or broken code..
	if($_COOKIE[$log_cookie] == null || $_COOKIE[$log_cookie] == "NaN-NaN-NaN"){
		$expire=time()+60*60*24*30;
		setcookie($log_cookie, $today, $expire);
	}
	
	// MySQL query 
	$query_date = "";	// $_COOKIE["mc_log_date"];
	if($_GET["date"]){
		$query_date = $_GET["date"];
	} else {
		$query_date = date("d-m-Y");
	}
		
	$select_command = mysqli_query($con, "SELECT * FROM all_days WHERE Date='$query_date'");
	
	// Create array to be converted into JSON format for Google Charts
	$google_graph_json_data = array();
	
	// Important data header columns
	$time_label_array = array("id" => "", "label" => "Time", "pattern" => "", "type" => "string");
	$player_count_label_array = array("id" => "", "label" => "Player Count", "pattern" => "", "type" => "number");
	$players_label_array = array("id" => "", "label" => "Players", "pattern" => "", "type" => "string", "p" => array("role" => "tooltip", "html" => TRUE));
	
	$google_graph_json_data['cols'] = array($time_label_array, $player_count_label_array, $players_label_array);
	$google_graph_json_data['rows'] = array();

	// Populate rows with MySQl query results
	while ($r = mysqli_fetch_assoc($select_command)) {
		$time = substr($r['Time'], 0, 5);
		$player_count = $r['PlayerCount'];
		$players = "$time : $player_count : " . $r['Players'];
		// $players = "<div class='chart_tooltip'><b>$time<&#47;b><br&#47;>$player_count:" . $r['Players'] . "<&#47;div>";
		$row_entry = array("c" => array(array("v" => $time, "f" => NULL), array("v" => (int)$player_count, "f" => null), array("v"=> $players, "f"=>NULL)));
		array_push($google_graph_json_data['rows'], $row_entry);
	}

	// Close MySQL connection
	mysqli_close($con);
	echo json_encode($google_graph_json_data);
}
?>
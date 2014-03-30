<?php

//If not visited page before, set cookie
if($_COOKIE[$log_cookie] == null || $_COOKIE[$log_cookie] == "NaN-NaN-NaN"){
	$expire=time()+60*60*24*30;
	setcookie($log_cookie, $today, $expire);
	$right_enabled = false;
} 

//Include Minecraft query php class
require_once ('./misc-php/minecraft/minecraftQuery.class.php');
//require_once ('./misc-php/minecraft/test.php');

$beefyServer = "localhost";//$_SERVER['SERVER_ADDR'];
$mc_query = new minecraftQuery();
$server_up = TRUE;
$query_error = null;
try {
	$mc_query -> connect($beefyServer, 25565, 3);
} catch (minecraftQueryException $e) {
	$query_error = $e -> getMessage();
	$server_up = FALSE;
}

?>
<div class="post">
	<p class="headingText">Minecraft Server Status</p>
	<div class="contentText">
		<div class="section-a">
			This page is kinda ugly and a WIP, but it works!<br/>
			<a href="<?php echo "http://".$_SERVER['SERVER_NAME'] . ":8123";?>"/><img src="./images/link_icons/dynmap.png"/>DynMap</a><br/><br/>
		</div>
<?php if($server_up == TRUE){
		/* - -Start section B - */
		echo "\t\t<div id=\"section-b\">\n";	
		echo "\t\t\t<!-- ######## MC SERVER STATUS TABLE ######## -->\n";
		echo "\t\t\t<table id=\"mc_players\">\n";
		echo "\t\t\t\t<tr>\n";
		echo "\t\t\t\t\t<td colspan=\"4\"><b>Players</b> (with rendered skins)</td>\n";
		$current_query = $mc_query -> getInfo();
		$player_count = $current_query['Players'];
		$max_players = $current_query['MaxPlayers'];
	
		// ###### MC Players Table #######
		echo "\t\t\t\t\t<td colspan=\"4\" align=\"center\">$player_count/$max_players</td>\n";
		echo "\t\t\t\t</tr>\n";
		echo "\t\t\t\t<tr>\n";					
		$players = $mc_query -> getPlayerList();
		$count = 0;
		
		for ($i = 0; $i < 16; $i++) {
			$slot_name = "[ empty ]";
			$empty_slot = false;
			// New row
			if ($i != 0 && $i % 8 == 0) {
				echo "\t\t\t\t</tr>\n";
				echo "\t\t\t\t<tr>\n";
			}
			// Check player name
			if($players[$i] == ""){
				$empty_slot = true;
			} else {
				$empty_slot = false;
				$slot_name = $players[$i];
			}

			echo "\t\t\t\t\t<td class = \"mc_player\" id = \"n$i\"><h5>$slot_name</h5>";
			
			$player_image = "";
			if ($empty_slot == true) {
				$player_image = $to_root . "./images/minecraft/empty_slot_w.png";
			} else {
				$player_image = $to_root . './misc-php/minecraft/3d_skin.php?a=-30&w=35&wt=-45&abg=0&abd=-30&ajg=-25&ajd=30&ratio=4&format=png&login=' . $slot_name;
			}
			echo "<img src = \"$player_image\" alt=\"Empty Slot\"/>";
			echo "</td>\n";
		}
		echo "\t\t\t\t</tr>\n";
		echo "\t\t\t</table>\n";
		echo "\t\t</div>\n";
		echo "\t\t<div id=\"section-a\">\n";
		echo "\t\t\t<table id=\"mcStatusTable\">\n";
		echo "\t\t\t\t<tr>\n";
		echo "\t\t\t\t\t<td>Server Mod</td>\n";
		echo "\t\t\t\t\t<td>" . $current_query['Software'] . "</td>\n";
		echo "\t\t\t\t</tr>\n";
		echo "\t\t\t\t<tr>\n";
		echo "\t\t\t\t\t<td>Server Version</td>\n";
		echo "\t\t\t\t\t<td>" . $current_query['Version'] . "</td>\n";
		echo "\t\t\t\t</tr>\n";
		echo "\t\t\t\t<tr>\n";
		echo "\t\t\t\t\t<td>Port</td>\n";
		echo "\t\t\t\t\t<td>" . $current_query['HostPort'] . "</td>\n";
		echo "\t\t\t\t</tr>\n";
		echo "\t\t\t\t<tr>\n";
		echo "\t\t\t\t\t<td>Plugins</td>\n";
		//echo "\t\t\t\t\t\t<table id=\"mc_plugins\">\n";
		echo "\t\t\t\t\t\t\t<tr>\n";
		echo "\t\t\t\t\t\t\t\t<td>\n";
		$plugins = $current_query['Plugins'];
		natsort($plugins);
		$counter = 0;
		foreach ($plugins as $plugin) {
			echo "\t\t\t\t\t\t\t\t\t$plugin<br/>\n";
			if ($counter > 8) {
				echo "\t\t\t\t\t\t\t\t</td>\n";
				echo "\t\t\t\t\t\t\t\t<td>\n";
				$counter = 0;
			}
			$counter++;
		}
		echo "\t\t\t\t\t\t\t\t</td>\n";
		echo "\t\t\t\t\t\t\t</tr>\n";
		// echo "\t\t\t\t\t\t</table>\n";
		echo "\t\t\t\t\t</td>\n";
		echo "\t\t\t\t</td>\n";
		echo "\t\t\t</tr>\n";
		echo "\t\t</table>\n";
		echo "\t</div>\n";
			
				
		} else {
			echo "Server is down. Let Beefy Ole know if you want it back up promptly. <br/>Details: $query_error";
		}
?>
<!-- ######### Player log chart ######## -->
	<ul id="inline_chart">
		<li id = "prev_day"><button id = "prev_day_empty" ></button></li>
		<li id = "chart_div">Tracking stats down atm. Will be back up eventually.</li>
		<li id = "next_day"><button id = "next_day_empty" ></button></li>
	</ul>
	
	<!-- ######## Recent Players ########## -->
	<h2>Players that Have visited this server:</h2>
	<div id="player_renders">
		<table>
			<tr>
				<?php
				$players = array("l33tllama", "Dojang", "Draglon", "Commander_Boom", "ch33z3", "sheegs", "Spanishroom", "Yoyo_inator");
				$count = 0;
				$comma = ", ";
				foreach ($players as $player) {
					$count++;
					if($count %10 ==0) {
						echo "\t\t\t\t\t\t</tr>\n";
						echo "\t\t\t\t\t\t<tr>\n";
					}
					if($count == count($players)){
						$comma = "";
					}
					echo "\t\t\t\t\t\t\t<td><p>$player$comma</p><img src=\"";
					echo "./misc-php/minecraft/3d_skin.php?a=-30&w=35&wt=-45&abg=0&abd=-30&ajg=-25&ajd=30&ratio=3&format=png&login=";
					echo "$player\"/></td>\n";
				}?>
			</tr>
		</table>
	</div>
    </div>
</div>
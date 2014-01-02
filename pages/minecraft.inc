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

$beefyServer = "192.168.1.100";//$_SERVER['SERVER_ADDR'];
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
	WIP!<br/>
	<a href="<?php echo "http://".$_SERVER['SERVER_NAME'] . ":8123";?>"/><img src="./images/link_icons/dynmap.png"/>DynMap</a><br/><br/>
	<!-- ######## MC SERVER STATUS TABLE ######## -->
	<table id="mc_players">
		<tr>
			<td colspan="4"><b>Players</b> (with rendered skins)</td>
<?php if($server_up == TRUE){
		$current_query = $mc_query -> getInfo();
		$player_count = $current_query['Players'];
		$max_players = $current_query['MaxPlayers'];
	
		// ###### MC Players Table #######
		echo "\t\t\t<td colspan=\"4\" align=\"center\">$player_count/$max_players</td>\n";
		echo "\t\t</tr>\n";
		echo "\t\t<tr>\n";					
		$players = $mc_query -> getPlayerList();
		$count = 0;
		
		for ($i = 0; $i < 16; $i++) {
			$slot_name = "[ empty ]";
			$empty_slot = false;
			// New row
			if ($i != 0 && $i % 8 == 0) {
				echo "\t\t</tr>\n";
				echo "\t\t<tr>\n";
			}
			// Check player name
			if($players[$i] == ""){
				$empty_slot = true;
			} else {
				$empty_slot = false;
				$slot_name = $players[$i];
			}

			echo "\t\t\t<td class = \"mc_player\" id = \"n$i\"><h5>$slot_name</h5>";
			
			$player_image = "";
			if ($empty_slot == true) {
				$player_image = $to_root . "./images/minecraft/empty_slot_w.png";
			} else {
				$player_image = $to_root . './misc-php/minecraft/3d_skin.php?a=-30&w=35&wt=-45&abg=0&abd=-30&ajg=-25&ajd=30&ratio=4&format=png&login=' . $slot_name;
			}
			echo "<img src = \"$player_image\" alt=\"Empty Slot\"/>";
			echo "</td>\n";
		}
			echo "\t\t</tr>\n";
			echo "\t</table>\n";
			echo "\t<table class=\"mcStatusTable\">\n";
			echo "\t\t<tr>\n";
			echo "\t\t\t<td>Server Mod</td>\n";
			echo "\t\t\t<td>" . $current_query['Software'] . "</td>\n";
			echo "\t\t</tr>\n";
			echo "\t\t<tr>\n";
			echo "\t\t\t<td>Server Version</td>\n";
			echo "\t\t\t<td>" . $current_query['Version'] . "</td>\n";
			echo "\t\t</tr>\n";
			
				
		} else {
			echo "Server is down. Let Beefy Ole know if you want it back up promptly. <br/>Details: $query_error";}?>				
		<tr>
			<td>Port</td>
			<td><?php echo $current_query['HostPort']; ?></td>
		</tr>					
		<tr>
			<td>Plugins</td>
			<td>
				<table id="mc_plugins">
					<tr>
						<td><?php
							echo "\n";
							$plugins = $current_query['Plugins'];
							natsort($plugins);
							$counter = 0;

							foreach ($plugins as $plugin) {
								echo "\t\t\t\t\t\t\t$plugin<br/>\n";
								if ($counter > 8) {
									echo "\t\t\t\t\t\t</td>\n";
									echo "\t\t\t\t\t\t<td>\n";
									$counter = 0;
								}
								$counter++;
							}
							?>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	
	<!-- ######### Player log chart ######## -->
	<ul id="inline_chart">
		<li id = "prev_day"><button id = "prev_day_empty" ></button></li>
		<li id = "chart_div" width="699px" style="width:699; height:320">Tracking stats down atm. Will be back up eventually.</li>
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
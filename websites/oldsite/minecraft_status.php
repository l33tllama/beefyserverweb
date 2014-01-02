<?php
$page_title = "Minecraft Status";
$to_root = "./";
$log_cookie = "mc_log_date";
$today = date("Y-m-d");
$right_enabled = true;
$left_enabled = true;

//If not visited page before, set cookie
if($_COOKIE[$log_cookie] == null || $_COOKIE[$log_cookie] == "NaN-NaN-NaN"){
	$expire=time()+60*60*24*30;
	setcookie($log_cookie, $today, $expire);
	$right_enabled = false;
} 
// Minecraft Query
require_once ($to_root . 'php/Minecraft/minecraftQuery.class.php');
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

// Headers
include_once $to_root . "header.inc";
include_once $to_root."/php/minecraft_graph_js_header.inc";?>
</head>
	<body>
		<?php
		include_once $to_root . "heading.inc";?>		
		<div id="wrapper"><?php
			include_once $to_root . "navigation.inc";?>
			<div id="main">
				<h3><b>Server Status</b></h3>
				<table id="mcStatusTable">	
					<?php if($server_up == TRUE){
						echo "\t\t\t<tr>\n";
						echo "\t\t\t<td>Current Players</td>\n";
						echo "\t\t\t<td>";
						$current_query = $mc_query -> getInfo();
						$player_count = $current_query['Players'];
						$max_players = $current_query['MaxPlayers'];
						echo "$player_count/$max_players";
						echo "\t\t\t\t</td>\n";	
						echo "\t\t\t\t</tr>\n";
						echo "\t\t\t\t\t<td>Player List: </td>\n";
						echo "\t\t\t\t\t<td>\n";
						echo "\t\t\t\t".'<table id="mc_players">'."\n"."\t\t\t\t".'<tr>';
									
						$players = $mc_query -> getPlayerList();

						$count = 0;
						
						for ($i = 0; $i < 16; $i++) {
							$slot_name = "";
							if ($i != 0 && $i % 8 == 0) {
								echo "\t\t\t\t\t\t\t\t</tr>\n";
								echo "\t\t\t\t\t\t\t\t<tr>\n";
							}
							if ($i < $player_count) {
								$slot_name = $players[$i];
							} else if ($i < $max_players){
								$slot_name = "Empty Slot";
							} else {
								
							}
							echo "\t\t\t\t\t\t\t\t\t" . '<td class = "mc_player" id = "n' .$i. '">'."<h5>$slot_name</h5>";
							$player_image = "";
							if ($slot_name == "Empty Slot") {
								$player_image = $to_root . "images/Minecraft_Players/empty_slot.png";
							} else if ($slot_name != null){
								$player_image = $to_root . 'php/Minecraft/3d_skin.php?a=-30&w=35&wt=-45&abg=0&abd=-30&ajg=-25&ajd=30&ratio=4&format=png&login=' . $slot_name;
							}
							echo '<img src = "' . $player_image . '"/>';
							echo "</td>\n";
						}
						echo "\t\t\t\t\t</tr>";
						echo "\t\t\t\t</table>";
						echo "\t\t\t\t</td>";
						echo "\n\t\t\t\t</tr>";
						echo "\n\t\t\t\t<tr>";
						echo "\n\t\t\t\t\t<td>Server Mod</td>";
						echo "\n\t\t\t\t\t<td>".$current_query['Software']."</td";
						echo "\n\t\t\t\t</tr>";
						
					} else {
						echo $query_error;
					}?>
	
					<tr>
						<td>Server Version</td>
						<td><?php echo $current_query['Version']; ?></td>
					</tr>					
					<tr>
						<td>Port</td>
						<td><?php echo $current_query['HostPort']; ?></td>
					</tr>					
					<tr>
						<td>Plugins</td>
						<td>
							<table id="mc_plugins">
								<tr>
									<td>
<?php $plugins = $current_query['Plugins'];
										natsort($plugins);
										$counter = 0;

										foreach ($plugins as $plugin) {
											if ($counter > 8) {
												echo "\t\t\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t\t\t<td>\n";
												$counter = 0;
											}
											echo "\t\t\t\t\t\t\t\t\t\t$plugin<br/>\n";
											$counter++;
										}
									?>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
				<ul id="inline_chart">
					<li id = "prev_day"><button id = "prev_day_empty" ></button></li>
					<li id = "chart_div" width="699px" style="width:699; height:320">Tracking stats down atm. Will be back up eventually.</li>
					<li id = "next_day"><button id = "next_day_empty" ></button></li>
				</ul>			
				<h2>Players that Have visited this server:</h2>
				<div id="player_renders">
					<table>
						<tr>
<?php
									
					$players = array("l33tllama", "Dojang", "Draglon", "Commander_Boom", "ch33z3", "sheegs", "Spanishroom", "Yoyo_inator");
					$count = 0;
					foreach ($players as $player) {
						$count++;
						if($count %8 ==0) {
							echo "\t\t\t\t\t\t</tr>\n";
							echo "\t\t\t\t\t\t<tr>\n";
						}
						echo "\t\t\t\t\t\t\t" . '<td><p>'.$player.'</p><img src="' . $to_root . 'php/Minecraft/3d_skin.php?a=-30&w=35&wt=-45&abg=0&abd=-30&ajg=-25&ajd=30&ratio=3&format=png&login=' . $player . '"/></td>' . "\n";
					}?>
						</tr>
					</table>
				</div>
			</div>
			<div id = "footer">
				<?php
				include_once $to_root . 'footer.inc';
			?>
			</div>
		</div>
	</body>
</html>

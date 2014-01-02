<?php
$to_root = "./";
$home_line = '<img src="'.$to_root.'images/home-2.png" class="nav_img" />Home</a>'; 
/* Game icon from http://findicons.com/icon/175997/game_pad?id=357180 */
$games_line = '<img src="'.$to_root.'images/game_pad.png" class="nav_img" />My Games</a>';
$mc_status_line = '<img src="'.$to_root.'images/minecraft-icon.png" class="nav_img" />Minecraft Status</a>';

$main_pages = array("index.php" => array("Home", "Home", $to_root."index.php", $to_root."images/home-2.png"),
					"games_list.php" => array("MyGames", "My Games", $to_root."games_list.php", $to_root."images/game_pad.png"),
					"minecraft_status.php" => array("MCStatus", "Minecraft Status", $to_root."minecraft_status.php", $to_root."images/minecraft-icon.png"),
					"misc_hacks.php" => array("MiscHacks", "Misc Stuff", $to_root."misc_hacks.php", ""));
					
$external_pages = array("RPi" => array("RPi", "./raspberrypi/", $to_root."images/rpi_logo_smaller.png"),
						"DynMap" => array("DynMap", "http://".$_SERVER['SERVER_NAME'].":8123"));
?>
<div id="navigation">
				<div class="localLinks">
					<?php 
					$count = 0;
					foreach($main_pages as $main_page => $page_info){
						if ($count > 0){
							echo "\t\t\t\t\t";
						}
						if($main_page == substr($_SERVER['PHP_SELF'], 1)){
							echo '<a class = "current_navlink" id="'.$page_info[0].'"><img src = "'.$page_info[3].'" class = "nav_img"/>'.$page_info[1]."</a>\n";
						} else {
							echo '<a class = "navlink" id="'.$page_info[0].'" href="'.$page_info[2].'"><img src = "'.$page_info[3].'" class = "nav_img"/>'.$page_info[1]."</a>\n";
						}
						$count++;
					}
					?>
				</div> 
				<div class="otherLinks">
					<a class = "navlink" id = "dynmap" href="http://<?php echo $_SERVER['SERVER_NAME'];?>:8123"><img src="<?php echo $to_root;?>images/dynmap.png" class="nav_img" />Dynmap</a>
					<a class = "navlink" id = "rpi" href="./raspberrypi/"><img src="<?php echo $to_root;?>images/rpi_logo_smaller.png" class="nav_img"/>Raspberry Pi</a>
				</div>
			</div>
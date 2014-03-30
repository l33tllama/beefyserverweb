<div class="post">
    <p class="headingText">Steam Games Chooser</p>
    <div class="contentText">
    
    <!-- ############################ User login form ################################# -->
		<form id="login_form">
			<fieldset hidden id="login_field">
				<legend>Super secret login (Do not hack)</legend>
				<label for="username">Username: </label><input type="text" id="username" name="username"/>
				<label for="password">Password:</label><input type="password" id="password" name="password"/><input id="submit" type="button" value="Login"/>	
				<br/>
				<p hidden id="login_error"></p>
			</fieldset>
			<fieldset hidden id="logged_in_field">
				<legend>You are logged in as <span id="login_name"></span></legend>
				
				<!-- ########################### Logout Button ################################# -->
				<br/>
				<br/><span hidden id="starring_error">Error Message</span>
				<br/>
				<label id="login_info" for="logout"></label><input type="button" id="logout" value="Log Out"/>
			</fieldset> 
		</form>
		<script src="https://www.google.com/jsapi"></script>
		<script src="http://crypto-js.googlecode.com/svn/tags/3.1.2/build/rollups/md5.js"></script>
		
		<!-- Local cached scripts for development -->
		<script src="./js/jsapi-localcached.js"></script>
		<script src="./js/cryptojs-md5-localcached.js"></script>
		<script type="text/javascript">
	    google.load('search', '1');
	    function searchComplete(searcher, div_id) {
			// Check that we got results
			if (searcher.results && searcher.results.length > 0) {
				
				// Grab our content div, clear it.
				var contentDiv = document.getElementById(div_id);
				contentDiv.innerHTML = '';
				
				// Loop through our results, printing them to the page.
				var results = searcher.results;
				
				// For each result write it's title and image to the screen
				var result = results[Math.floor((Math.random()*3)+1)]; //Math.floor((Math.random()*2)+1)
				var imgContainer = document.createElement('div');				
				var newImg = document.createElement('img');
				
				// There is also a result.url property which has the escaped version
				newImg.src = result.tbUrl;
				
				//imgContainer.appendChild(title);
				imgContainer.appendChild(newImg);
				
				// Put our title + image in the content
				contentDiv.appendChild(imgContainer);
				$('#' + div_id).addClass('animated flipInY');
				$('#' + div_id + "_td").fadeIn('slow');
			}
	    }
	</script>
<?php
	//TODO: EVENTUALLY - replace jQuery with DOM and stuff.. because EFFICIENCY
	
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
	
$NEW_GAME_EXPIRY = 7;
$FAV_GAME_EXPIRY = 12;
$OTH_GAME_EXPIRY = 20;
$connection_success = False;

$unplayed_not_empty = False;
$played_not_empty = False;
$current_not_empty = False;
	
try{
	// MongoDB connection
	$mongo_conn = new Mongo('localhost');
	$connection_success = True;
	
	// Connect to games DB
	$games_lists = $mongo_conn->games_lists;
	
	// Get user's collection entry that contains games
	$usernameCookie = "leo";
	if(isset($_COOKIE['username'])){
		$usernameCookie = $_COOKIE['username'];
	}
	$userCollection = $games_lists->users->findOne(array("username" => $usernameCookie));
	
	// Aaaaaaaaand, we're done with MongoDB!
	$mongo_conn->close();
	
	$userGames = $userCollection['steam_puap_games'];
	$userUnplayedGames = array();
	$userCurrentGames = array();
	$userPlayedGames = array();
	$userBrokenCatGames = array();
	
	// Fill relevant game list arrays
	forEach($userGames as $gameEntry){
		switch($gameEntry['PlayedState']){
			case "unplayed":
				$unplayed_not_empty = True;
				array_push($userUnplayedGames, $gameEntry);
				break;
			case "played":
				array_push($userPlayedGames, $gameEntry);
				break;
			case "current":
// 				 echo "Current game: " . $gameEntry['GameName'] . "<br/>";
				array_push($userCurrentGames, $gameEntry);
				$current_not_empty = True;
				break;
			default:
				array_push($userBrokenCatGames, $gameEntry);
				break;
		}
	}	
	
	// Pick new games if expired
	$newUserCurrentGames = array();
	
	$new_game_added = False;
	$finished_games = array();
	
	// Check each current game if expired
	foreach($userCurrentGames as $currUsrGame){
	
		// By default set the new game to the current game
		$new_game = $currUsrGame;
	
		// Create a DateTime object with the DateAdded date of the current game
		$dateAdded = DateTime::createFromFormat("d-m-Y G:i", $currUsrGame['DateAdded']); 		
		$dateToday = new DateTime();
 		
 		// Used for development
 		$timeDiff = $dateToday->diff($dateAdded);
 		$daysDiff = $timeDiff->days;
//  	echo $currUsrGame['GameName'] ." added on " . $currUsrGame['DateAdded'] . " and $daysDiff days since date added<br/>";
 		
 		// Determine game days duration interval based on game category
 		$gameInterval = 0;
		switch($currUsrGame['Category']){
			case "new":
				$gameInterval = $NEW_GAME_EXPIRY;
				break;
			case "fav":
				$gameInterval = $FAV_GAME_EXPIRY;
				break;
			case "other":
				$gameInterval = $OTH_GAME_EXPIRY;
				break;
			default:
				echo "Invlaid platform: " . $currUsrGame['GameName'] . " (" . $currUsrGame['Platform'] . ") so expiry date not caclulable.<br/>";
				break;
		}
		
		// Calculate expiry date to check with today
		$expiryDate = $dateAdded->add(new DateInterval('P' . $gameInterval . 'D'));

		// If game has expired (before today) and game is not starred
		if($expiryDate < $dateToday && $currUsrGame['Starred'] == false){
			
			echo $currUsrGame['GameName'] . " has expired! Time to pick new game..<br/>";
			
			// Pick next platform
			$finished_platform = $currUsrGame['Platform'];
			$next_platform = "";
			switch($finished_platform){
				case "linux":
					$next_platform = "win";
					break;
				case "win":
					$next_platform = "linux";
					break;
				default:
					$next_platform = "NA";
					break;
			}
			
			// Iterate through all unplayed games to find relevant next games (new/fav/other win/linux) 
			$next_games = array();
			$next_games_count = 0;
// 			echo "Looking for " . $currUsrGame['Category'] . "-" . $next_platform . ".<br/>";
			forEach($userUnplayedGames as $userUnplayedGame){
				if($userUnplayedGame['Platform'] == $next_platform && $userUnplayedGame['Category'] == $currUsrGame['Category']){
					array_push($next_games, $userUnplayedGame);
					$next_games_count = $next_games_count + 1;
				}
			}
// 			echo "Filled array with relevant games to pick one randomly from.<br>";
			
			// ############################ Pick a game at random! ##############################
			$new_game = $next_games[rand(0, $next_games_count-1)];
			$dateAddedStr = $dateToday->format("d-m-Y G:i");
			$new_game['DateAdded'] = $dateAddedStr;
			// Change played states
			$new_game['PlayedState'] = "current";
			$currUsrGame['PlayedState'] = "played";
			echo "Picked a new game!: " . $new_game['GameName'] . " on $dateAddedStr<br/>";
			
			// Add old current game to played list
			array_push($userPlayedGames, $currUsrGame);
			
// 			echo "Creating new list of unplayed games wihtout newly chosen game.<br/>";
			
			// Add game to finished game list to be removed later
			array_push($finished_games, $currUsrGame);
			
			$new_game_added = True;
// 			echo "New list/array created.<br/>";
			
		// Not expired or starred
		} else if ($expiryDate > $dateToday || $currUsrGame['Starred'] == true){		
			if($currUsrGame['Starred'] == true){
// 				echo "Game is starred, skipping.<br/>";
			} else {
				// Calculate time left to show on page
				$expiryDiff = $expiryDate->diff($dateToday);
				$timeLeftStr = $expiryDiff->format("%dd:%hh:%im"); 
				$new_game['TimeLeft'] = $timeLeftStr;
 				//echo $new_game['GameName'] . " has " . $new_game['TimeLeft'] ."<br/>";
			} 
		}
		
		// Add current game to current games list - could be a new game?
		array_push($newUserCurrentGames, $new_game);
		//echo "<br/>";
	}
	// Update user's current games to new list which may include a new game
		$userCurrentGames = $newUserCurrentGames;
	// Update user's games list if a new game was added
	if($new_game_added){
		
		// Add all but the just-finished games back to unplayed games
		$newUserUnplayedGames = array();
		foreach($finished_games as $finished_game){
			forEach($userUnplayedGames as $userUnplayedGame){
				if($userUnplayedGame['GameNameClean'] != $finished_game['GameNameClean']) {	
					//echo "Adding " . $userUnplayedGame['GameName'] . "<br/>";
					array_push($newUserUnplayedGames, $userUnplayedGame);
				}
			}
		}	
	
		
		
		// Update user's unplayed games to new list which might have last current game which just expired
		$userUnplayedGames = $newUserUnplayedGames;
		
		// Create new list of games
		$new_games_list = array();
// 		echo "NEW CURRENT<br/>";
		foreach($userCurrentGames as $ucg){
// 			echo "PlayedState: " . $ucg['PlayedState'] . "<br/>";
// 			echo "Category: " . $ucg['Category'] . "<br/>";
// 			echo "Platform: " . $ucg['Platform'] . "<br/>";
// 			echo "GameName: " . $ucg['GameName'] . "<br/>";
			array_push($new_games_list, $ucg);
		}
		foreach($userUnplayedGames as $uup){
			array_push($new_games_list, $uup);
		}
		foreach($userPlayedGames as $upg){
			array_push($new_games_list, $upg);
		}
// 		echo "All games<br/>";
		foreach($new_games_list as $updated_game){
// 			echo "PlayedState: " . $updated_game['PlayedState'] . "<br/>";
// 			echo "Category: " . $updated_game['Category'] . "<br/>";
// 			echo "Platform: " . $updated_game['Platform'] . "<br/>";
// 			echo "GameName: " . $updated_game['GameName'] . "<br/>";			
//  			echo "<br/>";
		}
		// Update user's games list with new steam_puap_games containing the new game in current
		$games_lists->users->update(array("username" => $usernameCookie), array('$set' => array("steam_puap_games" => $new_games_list)));
	}
	
} catch (MongoConnectionException $e){
	echo $e->getMessage();
}
?>
		<table id="current_games">
<?php 
		$platforms = array(array("Linux", "linux"), array("Win", "win"));
		$categories = array(array("New","new"), array("Favourite", "fav"), array("Other", "other"));
		
		// ################################ Current Games Table ###############################################
		
		// Outer row platforms (Linux, Win)
		for($i = 0; $i < 2; $i++) {
			echo "\t\t\t<tr>\n";
			// inner column categories (new, fav, other)
			for($j = 0; $j < 3; $j++){
			
				if ($connection_success && $current_not_empty) {
					$curr_ct_pl_games = array();
					foreach($userCurrentGames as $userCurrentGame){
						if($userCurrentGame['Platform'] == $platforms[$i][1] && $userCurrentGame['Category'] == $categories[$j][1]){
							array_push($curr_ct_pl_games, $userCurrentGame);
						}
					}
					if (count($curr_ct_pl_games) > 0){
						
						// For each game in each platform&category (usually one unless still unplayed from last time)
						foreach($curr_ct_pl_games as $game_data) {
							echo "\t\t\t\t<td id=\"" . $game_data['GameNameClean'] . "_td\">\n";
							echo "\t\t\t\t\t<div class=\"game_container\">\n";
							echo "\t\t\t\t\t\t<div class=\"game_details_box\">\n";
							echo "\t\t\t\t\t\t\t<p class=\"game_category\">" . $categories[$j][0] . " " . $platforms[$i][0] . "</p>\n";
							echo "\t\t\t\t\t\t\t<p class=\"game_name\">" . $game_data['GameName'] . "</p>\n";
							echo "\t\t\t\t\t\t</div>\n";
							echo "\t\t\t\t\t\t<div class=\"game_timeleft_box\">\n";
							echo "\t\t\t\t\t\t\t<p class=\"inset_shadow\">" . $game_data['TimeLeft'] . "</p>\n";
							echo "\t\t\t\t\t\t</div>\n";									
							echo "\t\t\t\t\t\t<div hidden class=\"starred_game_box\">\n";
							echo "\t\t\t\t\t\t\t<label class=\"starred_label\" for=\"" . $game_data['GameNameClean'] ."_checkbox\" id=\"" . $game_data['GameNameClean'] . "_label\">Star Me</label>\n";
							echo "\t\t\t\t\t\t\t<input hidden class=\"starred_checkbox\" type=\"checkbox\" id=\"" . $game_data['GameNameClean'] . "_checkbox\" />\n";
							
							echo "\t\t\t\t\t\t</div>\n";
							echo "\t\t\t\t\t</div>\n";
							echo "\t\t\t\t\t<div class=\"game_image\" id=\"" . $game_data['GameNameClean'] . "\"></div>\n";
							echo "\t\t\t\t</td>\n";
						}
					}
				}
			}
			echo "\t\t\t</tr>\n";
		}
?>
		</table>
	</div>
</div>
<div class="post">
	<p class="headingText">Unplayed Games</p>
	<!-- ############################################################## ADD NEW GAME FORM ################################################################################### -->
	<form id="add_game_form">
	    <fieldset id="game_details_field">
			<legend>Add <b>new</b> game to unplayed games database</legend>
			<label for="new_game_name">Game:</label><input disabled ="text" id="new_game_name_field" name="game_name"/><br/>
			<label for="platform_ddbox">Platform</label>
			<select disabled id="platform_select" name="platform_ddbox">
				<option value="select">-- Select --</option>
				<option value="linux">Linux and Windows</option>
				<option value="win">Windows-only</option>
			</select>
			<br/>
			<input disabled id="submit_game" type="button" value="Add Game">
			<p hidden id="add_game_error"></p>
	    </fieldset>
	    
	</form>
	<div class="contentText">
		<?php
		
		######################################################### UNPLAYED GAME LIST (move me to new page?)############################################################
		if($connection_success && $unplayed_not_empty) {
			
			// Windows-only, Linux
			for($i = 1; $i >= 0 ; $i--){
				
				// New, fav, other
				for($j = 0; $j < 3; $j++) {
					
					echo "<span class=\"upl_plat_catg_label_span\">". $platforms[$i][0] . " - ". $categories[$j][0] . "</span>\n";
					echo "<span class=\"edit_label_span\"><img src=\"./images/misc/edit-icon-24px.png\"/>Edit Name</span>\n";
					if($i == 1) {
							echo "<span class=\"ported_label_span\"><img src=\"./images/misc/linux-icon-24px.png\"/>Ported</span>\n";
					}
					
					// New list of games for each category/platform (eg new-linux)
					echo "<ul class=\"unplayed_list\" id=\"" . $categories[$j][1] . "-" . $platforms[$i][1] . "_list\">\n";
					
					$unplayed_ct_pl_games = array();
					
					// One loop - good!
  					foreach($userUnplayedGames as $userUnplayedGame){
  						if($userUnplayedGame['Platform'] == $platforms[$i][1] && $userUnplayedGame['Category'] == $categories[$j][1]){
  							array_push($unplayed_ct_pl_games, $userUnplayedGame);
  						}
  					}
					
					// Iterate cursor
					forEach($unplayed_ct_pl_games as $unplayed_game) {
						
						// Show game name and edit/ported buttons
						echo "\t<li>\n";
						echo "\t\t<span class=\"upl_game_name_span\" id=\"" . $unplayed_game['GameNameClean'] . "_text\">" . $unplayed_game['GameName'] . "</span>\n";
						echo "\t\t<span class=\"edit_name_span\"><input disabled type=\"button\" class=\"edit_name_btn\" id=\"". $unplayed_game['GameNameClean']. "_edit\" value=\"Edit\"/></span>\n";
						
						// Show ported to linux button if linux
						if($unplayed_game['Platform'] == "win"){
 							echo "\t\t<span class=\"ported_span\"><input disabled type=\"button\" class=\"ported_btn\" id=\"". $unplayed_game['GameNameClean'] . "_ported\" value=\"Yay!\"/></span>";
						}
						echo "\n";
						echo "\t</li>\n";
					}
					echo "</ul>\n";
				}
			}
		}		
		?>
		<div id="test_div"></div>
		<script>
		
function enable_buttons(){
	$('.ported_btn').attr("disabled", false);
	$('.edit_name_btn').attr("disabled", false);
	$('#new_game_name_btn').attr("disabled", false);
	$('#platform_select').attr("disabled", false);
	$('#submit_game').attr("disabled", false);
}	
function disable_buttons(){
	$('.ported_btn').attr("disabled", true);
	$('.edit_name_btn').attr("disabled", true);
	$('#new_game_name_btn').attr("disabled", true);
	$('#platform_select').attr("disabled", true);
	$('#submit_game').attr("disabled", true);
}

// ########################## LOGIN METHOD ####################################
function logIn(){

	//Get password and convert to MD5hash for security
	var userPWHash = CryptoJS.MD5(document.getElementById("password").value);
	
	//Get username in input field
	var userName = document.getElementById("username").value;
	
	// Check login details using ajax
	// ?user=" + userName + "&pass=" + userPWHash
	$.ajax({
		url: "./pages/misc/check_login.php?user=" + userName + "&pass=" + userPWHash, 
		success:function(response){
		
			switch(response){
				// Username correct, pw wrong
				case "PW_FAIL":
					$('#login_error').show();
					$('#login_error').text("Password is not correct.");
					break;
					
				// Username is incorrect.
				case "UN_FAIL":
					$('#login_error').show();
					$('#login_error').text("Bad username and/or password");
					break;
					
				// Some other wierd message
				default:
					
					// Username and Login correct!!
					if(response.indexOf("SUCCESS") > -1){	
						var responseArr = response.split("-");
						document.tmp_session_id = 999;
						// Hide login error if it was shown
						$('#login_error').hide();
						
						enable_buttons();
						setCookie('logged_in', "true", 21);
						setCookie('username', userName, 21);
						setCookie('session_id', responseArr[1], 21);
						
						// Pretty fade in animation for login status
						$('#login_field').fadeOut('slow', function(){		
							// Hide login form
							$('#login_field').hide();
							// Show login info and add game form
							$('#logged_in_field').fadeIn('slow');
							$('.starred_game_box').fadeIn('slow');
							$('#login_name').text(userName);
						});
					} else {
						// response error
						$('#login_error').text("Critical login error. Computer explode.");
					}
					break;
			}
		}
	});	
}

// ######################## Login triggers (onclick and on enter press) ##########
$('#submit').click(function(){logIn();});
$('#password').keypress(function( event ) {
	if ( event.which == 13 ) {
		event.preventDefault();
		logIn();
	}
});
$('#username').keypress(function( event ) {
	if ( event.which == 13 ) {
		event.preventDefault();
		logIn();
	}
});

// ############################# Logout trigger ##################################
$('#logout').click(function(){
	setCookie('logged_in', 'false', 21);
	setCookie('username', null, 21);
	$('#login_error').hide();
	// Fade out login info
	$('#logged_in_field').fadeOut('slow', function(){
	
		// Fade in login form
		$('#logged_in_field').hide();
		$('#login_field').fadeIn('slow');
		
		// Hide starred buttons
		$('.starred_game_box').fadeOut('slow');
		
		disable_buttons();
	});
});

// Google onLoad
function OnLoad() {
	document.tmp_session_id = 1337;
	
	// list generated by PHP
	var games_list = <?php echo json_encode($userCurrentGames); echo "\n"; ?>
	
	// Loop through games
	games_list.forEach(function(entry){
		
		// ImageSearch instance
		var tmpImgSearch = new google.search.ImageSearch();
		tmpImgSearch.setSearchCompleteCallback(this, searchComplete, [tmpImgSearch, entry.GameNameClean]);
		
		// Find a beautiful game screenshot
		tmpImgSearch.execute(entry.GameName + " site:store.steampowered.com");
		
		//Register checkbox clicked
		var check_element_str = "#" + entry.GameNameClean + "_checkbox";
		var label_element_str = "#" + entry.GameNameClean + "_label";
		
		// URL variables for setting starred and unstarred
		var url_vars_str_true = "gamename=" + entry.GameNameClean + "&starred=true&username=" + getCookie('username') + "&session_id=" + document.tmp_session_id;
		var url_vars_str_false = "gamename=" + entry.GameNameClean + "&starred=false&username=" + getCookie('username') + "&session_id=" + document.tmp_session_id ;
		
		// On checkbox click
		$(check_element_str).click(function(){
			// ########### If checking starred ############
			if ($(this).is(":checked")){		
				// Starred game - keep on list of current games for some reason
				$.ajax({
					url: "./pages/misc/set_starred_game_mongo.php?" + url_vars_str_true,
					success:function(response){
						if(response.indexOf("OK_") > -1){
							$('#starring_error').fadeOut();
							$(label_element_str).removeClass('hover').addClass('checked');
							$(label_element_str).fadeOut('fast', function(){
								$(label_element_str).text("Starred");
								$(label_element_str).fadeIn();
							});
							//addChecked();
						} else {
// 									if($('#starring_error').is('hidden'))
							$('#starring_error').fadeIn();
							$('#starring_error').html("Error starring: " + response.substring(4));
						}								
					}
				});
			} else {
				// ####### If unchecking starred ############
				
				// Unstarred game - remove from current list so it can be replaced by another
				$.ajax({
					url: "./pages/misc/set_starred_game_mongo.php?" + url_vars_str_false,
					success:function(response){
						if(response.indexOf("OK_") > -1){
							$('#starring_error').fadeOut();
							$(label_element_str).removeClass('checked');
							$(label_element_str).fadeOut('fast', function(){
								$(label_element_str).text("Star Me");
								$(label_element_str).fadeIn();
							}); 
						} else {
							$('#starring_error').fadeIn();
							$('#starring_error').html("Error un-starring: " + response.substring(4));
						}
					}
				});
			}
		});
	});
	
	var unplayed_games_list = <?php echo json_encode($userUnplayedGames); echo "\n"; ?>
	
	// Loop through games
	unplayed_games_list.forEach(function(entry){
		
		var unplayed_edit_btn_str = "#" + entry.GameNameClean + "_edit";
		var unplayed_ported_btn_str = "#" + entry.GameNameClean + "_ported";
		var game_change_btn = entry.GameNameClean + "_change_btn";
		var game_change_textfield = entry.GameNameClean + "_textfield";
		var game_name_text = $("#" + entry.GameNameClean + "_text");
		var game_change_cancel_btn = entry.GameNameClean + "_cancel_btn";
		
		// Save gamename for later
		var saved_gamename = game_name_text.text();
		var saved_gamename_clean = saved_gamename.replace(/[^a-z\d ]/i, '').replace(/ /g,"-").toLowerCase();
		
		var game_name_textfield_html = "<input type=\"text\" value=\"" + entry.GameName + "\" id=\"" + game_change_textfield + "\"/>";
		var game_change_btn_html = "<input type=\"button\" value=\"Set\" id=\"" + game_change_btn + "\"\>";
		var cancel_btn_html = "<input type=\"button\" value=\"Cancel\" id=\"" + game_change_cancel_btn + "\">";
		
		// On edit click
		$(unplayed_edit_btn_str).click(function(){
			
			// Set game text to textfield containing name, plus set button and cancel button
			game_name_text.html(game_name_textfield_html + game_change_btn_html + cancel_btn_html);
			
			// On game name set click
			$("#" + game_change_btn).click(function(){
				var new_game_name = $("#" + game_change_textfield).val();
				var encoded_new_game_name = encodeURI(new_game_name);
				//alert("Updating game name to: " + $("#" + game_change_textfield).val() + " encoded: " + encoded_new_game_name);
				
				var url_gamename_vars = "gamename=" + saved_gamename_clean + "&new_gamename=" + encoded_new_game_name + "&username=" + getCookie('username');
				$.ajax({
					url: "./pages/misc/set_game_name.php?" + url_gamename_vars,
					success:function(response){
						//alert(response);
						// Updated MongoDB
						if(response.indexOf("SUCCESS") > -1){
							// Fade out textbox
							game_name_text.fadeOut(function(){
								// Set text to say game name has changed
								game_name_text.html("<span id=\"changed_status\">Game Name Changed!</span>");
								// Fade in to display status, on complete show new game name
								game_name_text.fadeIn('slow', function() {
									game_name_text.fadeOut('slow', function(){
									game_name_text.text(new_game_name);
									game_name_text.fadeIn();
									});
								});
							});	
						}
					}
				});
			});
			
			// On cancel button clicked
			$("#" + game_change_cancel_btn).click(function(){
				game_name_text.html(entry.GameName);
			});
			
		});
		// On ported click (Thx, devs!!
		$(unplayed_ported_btn_str).click(function(){
			var game_category = entry.Category;
			var game_cat_list = $("#" + game_category + "-linux_list");
			var url_vars = "gamename=" + entry.GameNameClean +"&username=" + getCookie('username');					
			$.ajax({
				url: "./pages/misc/game_ported.php?" + url_vars,
				success: function(response){
					alert(response)
					if(response.indexOf("SUCCESS") > -1){
						game_name_text.fadeOut('slow', function(){
							
							// Set staus to ported
							game_name_text.html("<span id=\"changed_status\">" + saved_gamename + " ported! Magic!</span>");
							game_name_text.fadeIn('slow', function(){
								game_name_text.fadeOut('slow', function(){
								
									//Reset text
									game_name_text.html("<span id=\"changed_status\">" + saved_gamename + "</span>");
									
									// Save list element and remove
									var saved_element = game_name_text;
									saved_element.parent().remove();
									saved_element.remove();
									
									
									// Add to new list (?-linux)
									game_cat_list.append("<li>" + saved_element.html() + " (refresh for edit button)</li>");
									
									// Get element without jQ and scroll to it
									var ported_el = document.getElementById(entry.GameNameClean+"_text");
									ported_el.scrollIntoView();
								});
								
							});
						});
					}
				}
			});
		});
	});
	
	var current_games_list = <?php echo json_encode($userCurrentGames); echo "\n"; ?>
	
	// Loop through curent games, set checked if in DB
	current_games_list.forEach(function(entry){
		var checkbox = "#" + entry.GameNameClean + "_checkbox";
		if(entry.Starred){
			$(checkbox).prev("label").addClass('checked');
			$(checkbox).prev("label").html("Starred");
			//addChecked();
			//alert("Got starred game: " +entry[0] + " checked count: " + getChecked());
		}
	});
}

// jQuery document.ready
$(document).ready(function() {
		
	//Show if not logged in
	var loginStatus = getCookie('logged_in');
	var userName = getCookie('username');
	$("#current_games").addClass("animated tada");
	//If not logged in
	if (loginStatus != "true") {
		//Unhide login form
		$('#login_field').show();
		
	// ################################ LOGIN: Check if user was already logged in. ############################
	} else if (loginStatus == "true"){
		var savedSessionID = getCookie('session_id');
		var sessionPass = false;
		$.ajax({
			url: "./pages/misc/check_session_id.php?user=" + userName + "&session_id=" + savedSessionID, 
			success:function(response){
				$('#login_error').show();
				$('#login_error').text("Response: " + response);
				switch(response){
					// Session/user fail cases
					case "UN_FAIL":
						$('#login_error').show();
						$('#login_error').text("Username not set?");
						break;
					case "UN_MISMATCH":
						$('#login_error').show();
						$('#login_error').text("Incorrect username??");
						break;
					case "SESS_MISMATCH":
						$('#login_error').show();
						$('#login_error').text("Warning! Session ID is not correct!");
						break;
					case "SESS_FAIL":
						$('#login_error').show();
						$('#login_error').text("Session ID not set!?");
						break;
					default:
					
						// Login success!?
						if(response.indexOf("SUCCESS") > -1){
							
							// Get new session ID to store in cookies
							var responseArr = response.split("-");
							document.tmp_session_id = 1336;
							setCookie("session_id", responseArr[1], 21);
							sessionPass = true;
							$('#login_field').hide();
							//Set login name and unhide login info
							$('#login_name').text(userName);
							$('#logged_in_field').show();
							$('#add_game_form').show();
							
							// Fade-in starred button
							$('.starred_game_box').fadeIn('slow');
								
							enable_buttons();
							
							$('#login_error').show();
							$('#login_error').text("Login success!");
						} else {
							$('#login_error').show();
							$('#login_error').text("Some other response? " + response);
						}
						break;
				}
			},
			error: function (request, status, error) {
				$('#login_error').show();
				$('#login_error').text(request.responseText);
			}
		});	
		// ########################### On add game button click #########################
		$("#submit_game").click(function(){
			var gameName = $("#new_game_name_field").val();
			var platform = $("#platform_select").val();
			//alert("New game: " + gameName + " for " + platform);
			var games_list = $("#new-" + platform + "_list");
			var add_game_url_vars = "gamename=" + encodeURI(gameName) + "&platform=" + platform + "&username=" + getCookie("username");
			if(platform == "select"){
				$("#add_game_error").html("<b>Select a platform!</b>");
				$("#add_game_error").fadeIn();
				$("#add_game_form").addClass("animated shake");
				$('#add_game_form').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function(){
					// I am horribly lazy! Add animation duration in css..
					$('#add_game_form').removeClass("animated shake");
				});
			} else {
				$.ajax({
					url: "./pages/misc/added_game.php?" + add_game_url_vars, 
					success:function(response){
						if(response.indexOf("SUCCESS") > -1){
							$("#add_game_error").fadeOut();
							games_list.append("<li><span class=\"upl_game_name_span\">" + gameName + " (Refresh for buttons!)</span></li>");
						} else {
							
							$("#add_game_error").html(response);
							$("#add_game_error").show();
						}
					}
				});		
			}
		});
	}
});
google.setOnLoadCallback(OnLoad);
	</script>
    </div>
</div>
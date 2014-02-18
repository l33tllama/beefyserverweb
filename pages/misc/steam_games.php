<div class="post">
    <p class="headingText">Steam Games Chooser</p>
    <div class="contentText">
	<!-- dummy form that does nothing -->
	<form hidden id="login_form">
	    <fieldset id="login_field">
		<legend>Super secret login (Do not hack)</legend>
		<label for="username">Username: </label><input type="text" id="username" name="username"/><br/>
		<label for="password">Password:</label><input type="password" id="password" name="password"/><br/>
		<input id="submit" type="button" value="Login">
		<p hidden id="login_error"></p>
	    </fieldset>
	</form>
	<!-- Logged in status - only shown when logged in -->
	<div hidden id="login_info">You are logged in as <p id="login_name"></p><input type="button" id="logout" value="Log Out"/></div>
	
	<!-- Begin the Javascripting -->
	<!-- Latest scripts for when net is more stable.. INTERNODE FIX<script src="https://www.google.com/jsapi"></script>
	<script src="http://crypto-js.googlecode.com/svn/tags/3.1.2/build/rollups/md5.js"></script> -->
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
		    
		    //var title = document.createElement('h2');
		    // We use titleNoFormatting so that no HTML tags are left in the title
		    //title.innerHTML = '#'+div_id;//result.titleNoFormatting;
		    
		    var newImg = document.createElement('img');
		    // There is also a result.url property which has the escaped version
		    newImg.src = result.tbUrl;
		    
		    //imgContainer.appendChild(title);
		    imgContainer.appendChild(newImg);
		    
		    // Put our title + image in the content
		    contentDiv.appendChild(imgContainer);
		    $('#' + div_id).addClass('animated flipInY');
		    $('#' + div_id).fadeIn('slow');
		}
	    }
	</script>
<?php
		$games_names = array();
		$connection_success = False;
		try{
			// MongoDB connection
			$mongo_conn = new Mongo('localhost');
			
			// Connect to games DB
			$games_lists = $mongo_conn->games_lists;
			
			/*
			$played_games = $games_lists->played_puap_games;
			$unplayed_games = $games_lists->unplayed_puap_games;*/
			$current_games = $games_lists->current_puap_games;
			
			$cur_cursor = $current_games->find();
			
			
			
			if ($cur_cursor->count() > 0){
				foreach($cur_cursor as $obj){
					array_push($games_names, array($obj['GameName'], $obj['GameNameClean'], $obj['Category'], $obj['Platform']));
					// echo "GameName: " . $obj['GameName'] . ", Category: " . $obj['Category'] . ",  Platform: " . $obj['Platform'] . "<br/>";
					// if ($obj['Category'] == "Linux") {
					
				}
			}
			$connection_success = True;
			
			
		} catch (MongoConnectionException $e){
			echo $e->getMessage();
		}
		?>
		<table id="current_games">
<?php 
				$platforms = array(array("Linux", "linux"), array("Win", "win"));
				$categories = array(array("New","new"), array("Favourite", "fav"), array("Other", "other"));
				// Outer row platforms (Linux, Win)
				for($i = 0; $i < 2; $i++) {
					echo "\t\t\t<tr>\n";
					// inner column categories (new, fav, other)
					for($j = 0; $j < 3; $j++){
					
						if ($connection_success) {
							$cur_cursor = $current_games->find(array("Platform"=>$platforms[$i][1],"Category"=>$categories[$j][1]));
							if ($cur_cursor->count() > 0){
								// For each game in each platform&category (usually one unless still unplayed from last time)
								foreach($cur_cursor as $game_data) {
									echo "\t\t\t\t<td>\n";
									echo "\t\t\t\t\t<div class=\"game_container\">\n";
									echo "\t\t\t\t\t\t<div class=\"game_details_box\">\n";
									echo "\t\t\t\t\t\t\t<p class=\"game_category\">" . $categories[$j][0] . " " . $platforms[$i][0] . "</p>\n";
									echo "\t\t\t\t\t\t\t<p class=\"game_name\">" . $game_data['GameName'] . "</p>\n";
									echo "\t\t\t\t\t\t</div>\n";
									echo "\t\t\t\t\t\t<div hidden class=\"starred_game\">\n";
									echo "\t\t\t\t\t\t\t<label class=\"starred_label\" for=\"starred_" . $game_data['GameNameClean'] ."\">Star</label>\n";
									echo "\t\t\t\t\t\t\t<input class=\"starred_button\" type=\"checkbox\" id=\"" . $game_data['GameNameClean'] . "_checkbox\"/>\n";
									echo "\t\t\t\t\t\t</div>\n";
									echo "\t\t\t\t\t</div>\n";
									echo "\t\t\t\t\t<div class=\"game_image\" id=\"" . $game_data['GameNameClean'] . "\"/></div>\n";
									echo "\t\t\t\t</td>\n";
								}
							}
						}
					}
					echo "\t\t\t</tr>\n";
				}
				/*				
				foreach($platforms as $platform){
					echo "\t\t\t<tr>\n";
					
					foreach($cur_cursor as $game_data){
						echo "\t\t\t\t<td>\n";
						 $cur_cursor = $current_games->find(array("Platform"=>$platform[1],"Category"=>$category[1]));
						// if($cur_cursor->count() > 0){
							//$cur_cursor->next();
							// $game_data = $cur_cursor->current();
							echo "\t\t\t\t\t<div class=\"game_container\">\n";
							echo "\t\t\t\t\t\t<div class=\"game_details_box\">\n";
							echo "\t\t\t\t\t\t\t<p class=\"game_category\">" . $category[0] . " " . $platform[0] . "</p>\n";
							echo "\t\t\t\t\t\t\t<p class=\"game_name\">" . $game_data['GameName'] . "</p>\n";
							echo "\t\t\t\t\t\t</div>\n";
							echo "\t\t\t\t\t\t<div hidden class=\"played_game\">\n";
							echo "\t\t\t\t\t\t\t<label class=\"played_label\" for=\"played_" . $game_data['GameNameClean'] ."\">Played </label>\n";
							echo "\t\t\t\t\t\t\t<input class=\"played_button\" type=\"checkbox\" id=\"" . $game_data['GameNameClean'] . "_checkbox\"/>\n";
							echo "\t\t\t\t\t\t</div>\n";
							echo "\t\t\t\t\t</div>\n";
							echo "\t\t\t\t\t<div class=\"game_image\" id=\"" . $game_data['GameNameClean'] . "\"/></div>\n";
						/*} else {
							echo "\t\t\t\tNot found\n";
						}
						echo "\t\t\t\t</td>\n";
					}
					
					
				}*/
				$mongo_conn->close();
?>
		</table>
	    
	<script>
	    // Mega-(not affiliated with Kim Dotcom)-huge login function
	    function logIn(){
		
			//Get password and convert to MD5hash for security
			var userPWHash = CryptoJS.MD5(document.getElementById("password").value);
			
			//Get username in input field
			var userName = document.getElementById("username").value;
			
			// Check login details using ajax
			// ?user=" + userName + "&pass=" + userPWHash
			$.ajax({
				url: "./misc-php/check_login.php?user=" + userName + "&pass=" + userPWHash, 
				success:function(response){
					var top="<!DOCTYPE HTML>\n";
					switch(response){
					
					// Username correct, pw wrong
					case top + "PW_FAIL":
						$('#login_error').show();
						$('#login_error').text("Password is not correct.");
						break;
						
					// Username is incorrect.
					case top + "UN_FAIL":
						$('#login_error').show();
						$('#login_error').text("Bad username and/or password");
						break;
						
					// Username and Login correct!!
					case top + "SUCCESS":
					
						// Hide login error if it was shown
						$('#login_error').hide();
						
						// Pretty fade in animation for login status
						$('#login_form').fadeOut('slow', function(){
							setCookie('logged_in', "true", 21);
							setCookie('username', userName, 21);
							$('#login_name').text(userName);
							$('#login_form').hide();
							$('#login_info').fadeIn('slow');
						});
						$('.starred_game').fadeIn('slow');
						break;
					default:
					
						// response error
						$('#login_error').text("Critical login error. Computer explode.");
						break;
					}
				}
			});	
	    }
	    
	    // Login triggers (onclick and on enter press)
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
	    
	    // Logout trigger
	    $('#logout').click(function(){
			setCookie('logged_in', 'false', 21);
			setCookie('username', null, 21);
			
			// Fade out login info
			$('#login_info').fadeOut('slow', function(){
			
				// Fade in login form
				$('#login_info').hide();
				$('#login_form').fadeIn('slow');
				
				// Hide starred buttons
				$('.starred_game').fadeOut('slow');
			});
	    });
	    
	    // Google onLoad
	    function OnLoad() {
	    
		// list generated by PHP
		var games_list = <?php echo json_encode($games_names); echo "\n"; ?>
		
		// Loop through games
		games_list.forEach(function(entry){
		
		    // ImageSearch instance
		    var tmpImgSearch = new google.search.ImageSearch();
		    tmpImgSearch.setSearchCompleteCallback(this, searchComplete, [tmpImgSearch, entry[1]]);
		    
		    // Find a beautiful game screenshot
		    tmpImgSearch.execute(entry[1] + " site:steampowered.com");
		    
		    //Register checkbox clicked
		    var check_element_str = "#" + entry[1] + "_checkbox";
		    $(check_element_str).click(function(){
				var category;
				var platform;
				var mysql_db_name;
				
				// Set category text to match MySQL DB name
				switch (entry[2]) {
					case "Favourite":
						category = "Fav";
						break;
					default: 
						category = entry[2];
						break;
				}
				
				//Set platform text to match MySQL Db name
				switch (entry[3]) {
					case "Linux":
						platform = "linux";
						break;
					case "Windows":
						platform = "win";
						break;
					default: 
						platform = "NA";
						break;
				}
				mysql_db_name = category + "_" + entry[3];
				if ($(check_element_str).prop("checked")) {
				
					// Finished game - add to finished list on MySQL DB
					
					alert("You have finished " + entry[0] + "(" + mysql_db_name + "), eh? Hope it was fun!!"); 
				} else {
					alert("Changed your mind? " + entry[0] + " is unchecked.");
				}
			
		    });
		});
	    }
	    
	    // jQuery document.ready
	    $(document).ready(function() {
	    
	    //Show if not logged in
		var loginStatus = getCookie('logged_in');
		
		//If not logged in
		if (loginStatus != "true") {
		    //Unhide login form
		    $('#login_form').show();
		    
		// Logged in
		} else if (loginStatus == "true"){
		    //Set login name and unhide login info
		    $('#login_name').text(getCookie('username'));
		    $('#login_info').show();
		    // Fade-in starred button
		    $('.starred_game').fadeIn('slow');
		}
	    });
	    
	    google.setOnLoadCallback(OnLoad);
	</script>
    </div>
</div>
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
	<script src="https://www.google.com/jsapi"></script>
	<script src="http://crypto-js.googlecode.com/svn/tags/3.1.2/build/rollups/md5.js"></script>
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
		    $('#' + div_id).addClass('animated flipInY')
		    $('#' + div_id).fadeIn('slow');
		}
	    }
	</script>
<?php
	    // TODO: 
	    // Add 'played > 20mins' button to add to played list (calls separate php page with game url variable)
	    // When on played list, game gets removed from current after the week ends
	    // New game gets picked either way, creating a list of games in a particular category
	    $current_games_dir = "./pages/misc/games_lists/current_games/";
	    $current_new_linux = file($current_games_dir . "new-linux.txt");
	    $current_new_win = file($current_games_dir . "new-win.txt");
	    $current_fav_linux = file($current_games_dir . "fav-linux.txt");
	    $current_fav_win = file($current_games_dir . "fav-win.txt");
	    $current_other_linux = file($current_games_dir . "other-linux.txt");
	    $current_other_win = file($current_games_dir . "other-win.txt");
	    $games_lists = array(array("new", "linux", $current_new_linux),
				array("new", "win", $current_new_win),
				array("fav", "linux", $current_fav_linux),
				array("fav", "win", $current_fav_win),
				array("other", "linux", $current_other_linux),
				array("other", "win", $current_other_win));
	    $current_games = array();
	    // TODO: fix game names to remove spaces and other characters for JS and p id names
	    function clean_name($name)
	    {
			$name = substr($name, 0, strlen($name) - 1);
			$name = preg_replace("#[^A-Za-z0-9]#", "", $name);
			$name = str_replace(' ', '-', $name);
			return $name;
	    }
	    $game_count = 0;
	    
	    // Generate HTML for List of games
	    foreach ($games_lists as $game_list){
			$category_text = "";
		
			switch($game_list[0]){
				case "new":
					$category_text = "New";
					break;
				case "fav":
					$category_text = "Favourite";
					break;
				case "other":
					$category_text = "Other";
					break;
				default:
					$category_text = "Bad category!! Fix me!";
					break;
			}
			$platform_text = "";
			switch($game_list[1]){
				case "linux":
					$platform_text = "Linux/Cross-Platform";
					break;
				case "win":
					$platform_text = "Windows-Only";
					break;
				default:
					$platform_text = "Amiga! (Code broken, sadface)";
					break;
			}
			echo "\t\t<div id=\"game_category_$game_count\">\n";
			echo "\t\t\t<p class=\"subHeadingText\">$category_text Pick-Up And Play Game For $platform_text</p>\n";
			echo "\t\t\t<ul>\n";
			foreach ($game_list[2] as $game_name){
				$cl_game_name = clean_name($game_name);
				$game_name = substr($game_name, 0, strlen($game_name) - 1);
				echo "\t\t\t\t<li class=\"chosen_game\">$game_name<p hidden id=\"$cl_game_name\"></p></li>\n";
				echo "\t\t\t\t<p hidden class=\"played_game\"><label for=\"played_$cl_game_name\">Played >20mins</label><input type = \"checkbox\" id=\"" . $cl_game_name . "_checkbox\"/></p>\n";
				array_push($current_games, array($cl_game_name, $game_name, $category_text, $platform_text));  
			}
			echo "\t\t\t</ul>\n";
			echo "\t\t</div>\n";
			$game_count++;
	    }
	?>
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
						$('.played_game').show();
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
				
				// Hide played buttons
				$('.played_game').fadeOut('slow');
			});
	    });
	    
	    // Google onLoad
	    function OnLoad() {
	    
		// list generated by PHP
		var games_list = <?php echo json_encode($current_games); echo "\n"; ?>
		
		// Loop through games
		games_list.forEach(function(entry){
		
		    // ImageSearch instance
		    var tmpImgSearch = new google.search.ImageSearch();
		    tmpImgSearch.setSearchCompleteCallback(this, searchComplete, [tmpImgSearch, entry[0]]);
		    
		    // Find a beautiful game screenshot
		    tmpImgSearch.execute(entry[1] + " game screenshot");
		    
		    //Register checkbox clicked
		    var check_element_str = "#" + entry[0] + "_checkbox";
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
					case "Linux/Cross-Platform":
						platform = "linux";
						break;
					case "Windows-Only":
						platform = "win";
						break;
					default: 
						platform = "NA";
						break;
				}
				mysql_db_name = category + "_" + platform;
				if ($(check_element_str).prop("checked")) {
				
					// Finished game - add to finished list on MySQL DB
					
					alert("You have finished " + entry[1] + "(" + mysql_db_name + "), eh? Hope it was fun!!"); 
				} else {
					alert("Changed your mind? " + entry[1] + " is unchecked.");
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
		    // Fade-in played button
		    $('.played_game').fadeIn('slow');
		}
	    });
	    
	    google.setOnLoadCallback(OnLoad);
	</script>
    </div>
</div>
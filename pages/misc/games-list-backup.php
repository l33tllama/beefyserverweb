
		<p class="subHeadingText">New Pick-up and Play for Linux (at least)</p>
		<ul>
		    <?php foreach($current_new_linux as $game_name) {
			$cl_game_name = clean_name($game_name);
			echo "<li>$game_name<p id=\"$cl_game_name\"></p></li>\n";
			array_push($current_games, array($cl_game_name, substr($game_name, 0, strlen($game_name) - 1)));
		    }
		    ?>
		</ul>
		<p class="subHeadingText">New Pick-up and Play for Windows only</p>
		<ul>
			<?php foreach($current_new_win as $game_name) {
			$cl_game_name = clean_name($game_name);
			echo "<li>$game_name<p id=\"$cl_game_name\"></p></li>\n";
			array_push($current_games, array($cl_game_name, substr($game_name, 0, strlen($game_name) - 1)));
		    }
		    ?>
		</ul>
		<p class="subHeadingText">Favourite Pick-up and Play for Linux (at least)</p>
		<ul>
			<?php foreach($current_fav_linux as $game_name) {
			$cl_game_name = clean_name($game_name);
			echo "<li>$game_name<p id=\"$cl_game_name\"></p></li>\n";
			array_push($current_games, array($cl_game_name, substr($game_name, 0, strlen($game_name) - 1)));
		    }
		    ?>
		</ul>
		<p class="subHeadingText">Favourite Pick-up and Play for Windows only</p>
		<ul>
			<?php foreach($current_fav_win as $game_name) {
			$cl_game_name = clean_name($game_name);
			echo "<li>$game_name<p id=\"$cl_game_name\"></p></li>\n";
			array_push($current_games, array($cl_game_name, substr($game_name, 0, strlen($game_name) - 1)));
		    }
		    ?>
		</ul>
		<p class="subHeadingText">Other Pick-up and Play for Linux (at least)</p>
		<ul>
			<?php foreach($current_other_linux as $game_name) {
			$cl_game_name = clean_name($game_name);
			echo "<li>$game_name<p id=\"$cl_game_name\"></p></li>\n";
			array_push($current_games, array($cl_game_name, substr($game_name, 0, strlen($game_name) - 1)));
		    }
		    ?>
		</ul>
		<p class="subHeadingText">Other Pick-up and Play for Windows only</p>
		<ul>
			<?php foreach($current_other_win as $game_name) {
			$cl_game_name = clean_name($game_name);
			echo "<li>$game_name<p id=\"$cl_game_name\"></p></li>\n";
			array_push($current_games, array($cl_game_name, substr($game_name, 0, strlen($game_name) - 1)));
		    }
		    ?>
		</ul>
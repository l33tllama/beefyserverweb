<div class="post">
    <p class="headingText">Web Creations</p>
    <div class="contentText">
    <?php foreach($all_pages as $page){
	    if($page["link_name"] != null && $page["query_name"] == "my_creations"){
		foreach ($page["children"] as $category){
		    if ($category["query_name"] == "web") {
			$alt = false;
			$backg = "";
			foreach($category["children"] as $game_page){
			    $alt = !$alt;
			    $backg = $alt? "a" : "b";
			    echo "<div class=\"listItem-$backg\">\n";
			    echo "\t<a  href=\"?page=" . $game_page["query_name"] . "\">\n";
			    echo "\t\t<img src=\"./images/" . $game_page["thumb_image"] . "\"/>\n";
			    echo "\t\t<p class=\"itemName\">" . $game_page["link_name"] . "</p></a>\n";
			    echo "\t\t<p class=\"desc\">" . $game_page["short_desc"] . "</p>\n";
			    echo "\t\n</div>\n";
			}
		    }
		}
	    }
	}
      ?>
      </div>
</div>
<!DOCTYPE HTML>
<?php
/* Plan: Single file for most of the site, dynamically load new pages from here when needed*/
$pagesJson = file_get_contents("pages.json");
$pages = json_decode($pagesJson);
$all_pages = array();
 
/*
 * convertPageToArray
 * Takes a json data structure and converts it into a custom array
 */
function convertPageToArray($pageData){
	
	$page = "Empty page";
	//Get page info from JSON
	$c_page_name = $pageData -> {"link_name"};
	$c_page_query_name = $pageData -> {"query_name"};
	$c_page_type = $pageData -> {"type"};
	$c_page_loc = $pageData->{"location"};
	//description
	$c_page_short_desc = $pageData->{"short_desc"};
	//Thumbnail image
	$c_page_thumb = $pageData -> {"thumb_image"};
	//CSS Stylesheets
	$c_page_css = array();
	foreach($pageData ->{"stylesheets"} as $cssFile){
		array_push($c_page_css, $cssFile);
	}
	//JS Scripts
	$c_page_scripts = array();
	foreach($pageData -> {"scripts"} as $script){
		array_push($c_page_scripts, $script);	
	}
	//Check for child page(s)
	$c_haschild = $pageData -> {'haschild'};
	if($c_haschild == "true"){
		$c_children =  array();
		foreach($pageData ->{"children"} as $child){
			array_push($c_children, convertPageToArray($child));	
		}
		$page = array("link_name" => $c_page_name, "query_name" => $c_page_query_name, "type" => $c_page_type, "location" => $c_page_loc, "scripts" => $c_page_scripts, "stylesheets" => $c_page_css, "thumb_image" => $c_page_thumb, "short_desc" => $c_page_short_desc, "haschild" => $c_haschild, "children" => $c_children);
	} else {
		$page = array("link_name" => $c_page_name, "query_name" => $c_page_query_name, "type" => $c_page_type, "location" => $c_page_loc, "scripts" => $c_page_scripts, "stylesheets" => $c_page_css, "thumb_image" => $c_page_thumb, "short_desc" => $c_page_short_desc, "haschild" => $c_haschild);	
	}
	return $page; 
	
}
$numPages = 0;

// Fill $pages array with page info, gto from convertPageToArray function
foreach($pages as $pageInfo){
	$pageToPush = convertPageToArray($pageInfo);
	if($pageToPush["link_name"] != null){
		array_push($all_pages, $pageToPush);	
		$numPages++;		
	}
}	

$selected_page = $_GET["page"];
$current_page_file = "home.inc";

/* checkAndSetContent(page (array), $selected_page (string))
	checks the input page array, $page with selected page name string,
	$selected_pages
*/
function checkAndSetContent($page, $selected_page){
	//echo "testing " . $page["query_name"] . " with $selected_page<br/>\n";
	$output = array("current_page" => "", "scripts" =>"", "css" => "");
	if($page["query_name"] == $selected_page){
		//For Internal Webpages - load the include file	
		if ($page["type"] == "local"){
			//echo "Dynamic page content loading.. not external site..";
			$current_page_file = $page["location"];	
			$output["current_page"] = $page["location"];
			$output["link_name"] = $page["link_name"];
			//Read the stylesheets and initial JS files
			if(count($page["scripts"]) > 0){
				$output["scripts"] = $page["scripts"];
			}
			if(count($page["stylesheets"]) > 0){
				$output["css"] = $page["stylesheets"];	
			}
			
		// For External link - redirect to the link
		} else if ($page["type"] == "external"){
			//Redirect to other service/page
			//echo "I should have redirected you to external page!!!";
			header( 'Location: ' . "http://".$_SERVER['SERVER_NAME'].$page["location"] ) ;
		} else {
			echo "Bad JSON syntax - not an external or internal site - bad code monkey!!";
		}
		return $output;
	} 
	else {
		return false;
	}
}

$current_page_file = false;
$current_page_name = null;
$jsToLoad = array();
$cssToLoad = array();
//Detect selected page and set content
$found_link = false;
if($selected_page != NULL){
    foreach ($all_pages as $page){
    	if(!$found_link){
    		$page_details = checkAndSetContent($page, $selected_page);
    		$current_page_file = $page_details["current_page"];
    		$jsToLoad = $page_details["scripts"];
    		$cssToLoad = $page_details["css"];
    		$current_page_name = $page_details["link_name"];
    		//Mark as found
    		if($page_details != false) {
    			$found_link = true;
    			
    			break;
    		}
    	}
    	// Child pages
    	if(!$found_link && $page["haschild"] == "true"){
    		foreach($page["children"] as $child){
    			if(!$found_link){
    				$page_details = checkAndSetContent($child, $selected_page);
    				$current_page_file = $page_details["current_page"];
    				$jsToLoad = $page_details["scripts"];
    				$cssToLoad = $page_details["css"];
    				$current_page_name = $page_details["link_name"];
    				//Mark as found
    				if($page_details != false) {
    					 $found_link = true;
    					 break;
    				}	
    			}
    			//Grandchild pages
    			if(!$found_link && $child["haschild"] == "true"){
    				foreach ($child["children"] as $grandChild){
    					if(!$found_link){
    						$page_details = checkAndSetContent($grandChild, $selected_page);
    						$current_page_file = $page_details["current_page"];
    						$jsToLoad = $page_details["scripts"];
    						$cssToLoad = $page_details["css"];
    						$current_page_name = $page_details["link_name"];
    						//Mark as found
    						if($page_details != false) {
    							$found_link = true;
    							break;
    						}
    					}
    				}
    			}
    		}
    	}
    }
    if($current_page_file == false) {
        $current_page_file = "404.inc";	
        $cssToLoad = array("default.css");
    }
    
} else {
    $current_page_file = "home.inc";
    $cssToLoad = array("default.css", "normalize.css");
    $current_page_name = "Home";
}


?>
<html>
	<head>
<?php 
	foreach($jsToLoad as $jsFile){
		echo "\t\t<script src=\"./js/" . $jsFile . "\"></script>\n";
	}
	foreach($cssToLoad as $cssFile){
		echo "\t\t<link rel=\"stylesheet\" type=\"text/css\" href=\"./css/$cssFile\"/>\n";
	}
?>
		<title>Beefy Server | <?php echo $current_page_name; ?></title>
	</head>
	<body>
		<div id="title">
			<h1>Beefy Server </h1>
			<!--<p>Now with 25% more beef.</p>-->
		</div>
		<div id="navigation">
			<ul class="navMenu">	
<?php
	$count = 0;
	foreach($all_pages as $page){
		$class = "";
		if($count == 0){
			$class = "firstLink";
		} else if ($count >0 && $count < $numPages){
			$class = "midLink";
		} else if ($count == $numPages) {
			$class = "lastLink";
		}
		echo "\t\t\t\t";
		echo "<li class = \"$class\"><a href=\"?page=";
		echo $page["query_name"];
		echo "\">";
		echo $page["link_name"];
		echo "</a>\n";
		if($page["haschild"] == "true"){
			echo "\t\t\t\t\t";
			echo "<ul>\n";
			foreach($page["children"] as $child){
				echo "\t\t\t\t\t\t";
				echo "<li><a href=\"?page=";
				echo $child["query_name"];
				echo "\">";
				echo $child["link_name"];
				echo "</a>";	
				if($child["haschild"] == "true"){
					echo "\n\t\t\t\t\t\t\t<ul>";
					foreach($child["children"] as $grandChild){
						echo "\n\t\t\t\t\t\t\t\t";
						echo "<li><a href=\"?page=";
						echo $grandChild["query_name"] ."\">";
						echo $grandChild["link_name"];
						echo "</a></li>\n";	
					}
					
					echo "\n\t\t\t\t\t\t</ul>";
					echo "</li>";
				} else {
					echo "</li>";
				}
				
			}
			echo "\n\t\t\t\t\t\t</ul>\n";						
		}
		$count++;
		echo "\t\t\t\t\t\t</li>\n";
		// echo "\t\t\t\t\t\t<img class=\"separator\" src=\"./images/separator.png\"/></li>\n";
	}
?>
		</ul> <!-- end nav list -->
		</div> <!-- end navigation div -->	
		
		<div id="main">
			<div id="main_content">	
				<!-- TODO: Navigation indicator in main content section -->
<?php include_once("./pages/" . $current_page_file);?>
				<div id="footer">
				<?php include_once('./pages/footer.inc');?>
				</div>
			</div>			
		</div>		
	</body>
</html>
<?php
$message_of_the_day = array("I also made Apple's website (no rly).", "This website may contain traces of nuts.", 
							"Steal my HTML - go ahead.", "Powered by expensive HydroTAS electricity.", "My site > yours.",
							"This wesbite is an example of my valuable skills. Hire me.", "My webskills are 1337.",
							"Copyright (c) 2013 BeefyOle Industries.", "This footer contains many useful facts.",
							"Has anyone really been far even as decided to use even go want to do look more like?"
							);

?>
<h id="footer"><?php echo $message_of_the_day[rand(0, count($message_of_the_day)-1)];?></h>

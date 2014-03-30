<?php /*$footerQuotes = array(
		"Mind-blowing quote goes here.", 
		"Made with <a href=\"http://www.codiad.com\">Codiad</a> + <a href=\"http://www.aptana.com\">Aptana Studio</a>."
		);
//echo $footerQuotes[rand(0, count($footerQuotes)-1)]; */?>
<script type="text/javascript" src="http://www.brainyquote.com/link/quotebr.js"></script>
<small>
    <i><a href="http://www.brainyquote.com/quotes_of_the_day.html" target="_blank">more Quotes</a></i>
</small><br/>
My <a href="https://github.com/l33tllama">GitHub</a>, where I share my source.<br/>
<?php 
$latest_results = "";
exec("sh /home/leo/public_html/find_last_modified_fast.sh", $latest_results);
echo "Site last modified: " . substr($latest_results[0], 0, 16);

/*$self = "index.php"; echo "Site last modified: " . date ("F d Y H:i.", filemtime($self));*/?><br/>

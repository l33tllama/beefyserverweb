<!DOCTYPE HTML>
<?php
if(isset($_GET["toSpeak"])){
    $toSpeak = $_GET["toSpeak"];
    
    shell_exec("/home/leo/public_html/newsite/pages/misc/speaker.sh $toSpeak");
    //passthru("./home/leo/public_html/newsite/pages/misc/speaker.sh $toSpeak");
    //exec("echo \"$toSpeak\" | festival --tts &");
    echo "$toSpeak spoken?";
}
//exec("echo \"Hello\" | festival --tts");
//echo "test";
?>
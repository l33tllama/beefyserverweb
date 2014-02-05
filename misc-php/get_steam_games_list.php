<!DOCTYPE HTML>
<?php

$games_url = "http://steamcommunity.com/id/l33t_llama/games/?tab=all&xml=1";
// header($games_url);

function get_url_contents($url){
        $crl = curl_init();
        $timeout = 5;
        curl_setopt ($crl, CURLOPT_URL,$url);
        curl_setopt ($crl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($crl, CURLOPT_CONNECTTIMEOUT, $timeout);
        $ret = curl_exec($crl);
        curl_close($crl);
        return $ret;
}

$str = get_url_contents($games_url);
echo $str;
/*
$str = file_get_contents($games_url);
echo $str;*/
?>
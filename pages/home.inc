<div class="post">
	<p class="headingText">Welcome to Beefy Server</p>
	<div class="contentText">
		<b>Welcome</b> to the front-page of my home server. <br/>
		Minecraft players, click <a href="?page=minecraft">Minecraft</a> or the link above for handy info, including recent, current players and server details.<br/><br/>
		The purposes of this site is for experimenting and improving my web skills (PHP, CSS, JS, JSON, etc), a <a href="?page=my_creations">portfolio</a> of my past creations (web, games, etc), and information about running services, in particular, <a href="?page=minecraft_main">Minecraft</a> <br/>
		I hope that it doesn't suck. <br/>
		The server is being hosted on a low-power, always-on machine, so uptime is usually very good. (You can play Minecraft at 3AM if you wish!)<br/>		
	</div>
</div>
<div class="post">
	<p class="headingText">Services Open To Public</p>
	<div class="contentText">
		<p class="subHeadingText">Minecraft Survival MP</p>
		<ul>
			<li>Port 25565</li>
			<li>Currently vanilla CraftBukkit MC with a few plugins.</li>
			<li><a href="?page=minecraft_dynmap">DynMap</a>(Live rendered map of the whole world, zoomable)</li>
			<li>If you find a cool plugin on craftbukkit or elsewhere, feel free to ask me to add it! It's pretty simple.</li>
			<li>New users need to ask for build rights. Most should know who/how to contact me.</li>
		</ul>
		<p class="subHeadingText"><a href="http://<?php echo $_SERVER['SERVER_NAME'];?>:4040">Subsonic</a> - ALL of my legally-acquired music</p>
		<ul>
			<li>Guest access (can't modifiy anything): u:guest p:12345</li>
		</ul>	
		<p class="subHeadingText">Totally not an outdated VNC server, nope.</p>
	</div>		
</div>
<div class="post">
	<p class="headingText">About this Server</p>
	<div class="contentText">
		A small mini-ITX box put together so I could have my own high enough performance, low power, small footprint/volume server on the cheap.<br/>
		Running Ubuntu 12.04 LTS, Apache2 with PHP and MySQL, and other things.
<?php 
		$data = shell_exec('uptime');
  		$uptime = explode(' up ', $data);
		$uptime = explode(',', $uptime[1]);
		$uptime = $uptime[0].', '.$uptime[1];
		
		echo ('Current server uptime: '.$uptime.'.'); ?>
		<br/>
		<a href='http://stats.pingdom.com/4fy02r4eeads'>Pingdom Uptime (Remote Accessibility)</a>
		<p class="subHeadingText">Server Specs</p>
		<table>
			<tr>
				<td class ="align_right"><b>CPU</b></td><td>Dual-core Intel Sandy Bridge Celeron 847 1.1GHz (&lt;17W)</td>
			</tr>
			<tr>
				<td class ="align_right"><b>RAM</b></td><td>6GB SODIMM</td>
			</tr>
			<tr>
				<td class ="align_right"><b>Motherboard</b></td><td>ASUS C8H70HM-I-HDMI </td>
			</tr>
		</table>
	</div>
</div>

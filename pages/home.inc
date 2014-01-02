<div class="post">
	<p class="headingText">Welcome to Beefy Server</p>
	<div class="contentText">
		<b>Welcome</b> to the front-page of my home server. <br/>
		Minecraft players, click the Minecraft link above for handy info!<br/><br/>
		The purposes of this site is for experimenting and improving my web skills (PHP, CSS, JS, JSON, etc), a <a href="?page=my_creations">portfolio</a> of my past creations (web, games, etc), and information about running services, in particular, <a href="?page=minecraft_main">Minecraft</a> <br/>
		I hope that it doesn't suck. <br/>
		The server is being hosted on a low-power, always-on machine, so uptime is usually very good.
	</div>
</div>
<div class="post">
	<p class="headingText">Public Services Running on this Machine</p>
	<div class="contentText">
		<p class="subHeadingText">Minecraft Survival MP (Port 25565)</p>
		Details:
		<ul>
			<li>Pretty much vanilla MC with a few unobtrusive mods.</li>
			<li>DynMap also running (link todo).</li>
			<li>New users need to ask for build rights. Most should know who/how to contact me.</li>
		</ul>
		<p class="subHeadingText">Subsonic (Port 4040)</p>	
	</div>		
</div>
<div class="post">
	<p class="headingText">About this Server</p>
	<div class="contentText">
		<p>This server is running Ubuntu 12.04 LTS, and Apache with PHP and MySQL.</p>
		<?php 
		$data = shell_exec('uptime');
  		$uptime = explode(' up ', $data);
		$uptime = explode(',', $uptime[1]);
		$uptime = $uptime[0].', '.$uptime[1];
		
		echo ('Current server uptime: '.$uptime.'.'); ?><br/>
		<a href='http://stats.pingdom.com/4fy02r4eeads'>Pingdom Uptime (Remote Accessibility)</a>
		<p class="subHeadingText">Server Specs</p>
		<table>
			<tr>
				<td align="right"><b>CPU</b></td><td>Dual-core Intel Sandy Bridge Celeron 847 1.1GHz (&lt;17W)</td>
			</tr>
			<tr>
				<td align="right"><b>RAM</b></td><td>6GB SODIMM</td>
			</tr>
			<tr>
				<td align="right"><b>Motherboard</b></td><td>ASUS C8H70HM-I-HDMI </td>
			</tr>
		</table>
	</div>
</div>
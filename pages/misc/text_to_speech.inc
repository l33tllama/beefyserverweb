<div class="post">
    <p class="headingText">Server-Side Text-To-Speech</p>
    <div class="contentText">
	<p class="subHeadingText" id="bob">Server-side text-to speech</p>
	Enter text to to make server speak aloud!<br/>
	Text will be spoken aloud using the speakers connectd to the server, using a one-way REST Python web app. Annoy me untill I unplug the speakers!<br/>
	<script type="text/javascript">
	function makeHttpObject() {
	  try {return new XMLHttpRequest();}
	  catch (error) {}
	  try {return new ActiveXObject("Msxml2.XMLHTTP");}
	  catch (error) {}
	  try {return new ActiveXObject("Microsoft.XMLHTTP");}
	  catch (error) {}
	
	  throw new Error("Could not create HTTP request object.");
	}

	show(typeof(makeHttpObject()));
	
	function speakIt(){
	    var toSpeak = document.getElementById("toSpeak").value;
	    var words = toSpeak.split(" ");
	    toSpeak = words.join("%20");
	    var speakerUrl = 'http://<? echo $_SERVER['SERVER_NAME'];?>:8081/?say=';
		var request = makeHttpObject();
		if(toSpeak != ""){
			document.getElementById("result").innerHTML = "Speaking..";
			request.open("GET", speakerUrl + toSpeak, false);
			request.onreadystatechange = function() {
			    if (request.readyState == 4) {
				document.getElementById("result").innerHTML = "Spoken!";
			    }
			};
			request.send();
			//Nothing happens after here - no code execution
			
		} else {
			document.getElementById("result").innerHTML = "Nothing entered.";
		}
	};
	</script>
	<input type="text" name="say" id="toSpeak"/>
	<input type="button" value="Say it!" onClick="speakIt();"/>
	<div id="result"></div>
	<script type="text/javascript">
	document.getElementById('toSpeak').onkeypress=function(e){
	    if(e.keyCode==13){
		speakIt();
	    }
	}
	</script>
	</div>
</div>
	
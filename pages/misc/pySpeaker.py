import subprocess
text = '"Hello world"'
subprocess.call('espeak '+text, shell=True) 

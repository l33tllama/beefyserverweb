#!/usr/bin/env python

from os import listdir
from os.path import isfile, join
import glob
mypath = "/home/leo/public_html/log/players/"
#onlyfiles = [ f for f in listdir(mypath) if isfile(join(mypath,f)) ]

output = ""
#print "test"

for f in listdir(mypath):
    if (isfile(join(mypath, f)) and ".dat" in f):
        output = output + f[:-4] + ","
        #print f[:-4]
print output[:-1]

#print onlyfiles

#print "\n"
#print glob.glob("/home/leo/CraftBukkit/world/players/*.dat")
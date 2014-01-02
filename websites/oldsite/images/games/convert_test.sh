#!/bin/sh
folder="./resized/";
endBit="_thumb.png";
for i in *.png; 
    do echo "$i";   
    noExt=echo ${i:0:-4};
    convert "$i" -resize 155x155 -gravity center -extent 155x155 -bordercolor white -border 1x1 -alpha set -channel RGBA -fuzz 2% -fill none -floodfill +0+0 white -shave 1x1 $folder$noExt$endBit; done

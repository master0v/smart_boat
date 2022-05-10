#!/bin/bash

cd /home/captain/smart_boat/scripts
filename=mppt

echo "Making the plots from InfluxDB"
./plot_from_influx.py

# === TOP HALF ==
#
# convert grafana dashboard to a png
#./webpage_to_image.py

# make it monochrome and the right size
# convert solar.png -resize 1135x877 -monochrome solar_m.png

# === BOTTOM HALF ===
#
# --- DURING HURRICANE SEASON ---
#
# get NHC image
#curl -O https://www.nhc.noaa.gov/xgtwo/two_atl_5d0.png

# make it monochrome and the right size
#convert two_atl_5d0.png -resize 824x877 -set colorspace Gray -separate -average -negate weather_m.png

# --- DURING THE SAILING SEASON ---
#rm 012.png
##rm PYEA11.gif

# weatherfax Atlantic
##wget https://tgftp.nws.noaa.gov/fax/PYEA11.gif

# weatherfax Caribbean
#wget https://tgftp.nws.noaa.gov/fax/PYEK10.gif

# windward islands
#wget https://www.passageweather.com/maps/windward/wind/012.png

# leeward islands
#wget https://www.passageweather.com/maps/leeward/wind/012.png

# entire atlantic
#wget https://www.passageweather.com/maps/arc/wind/012.png

# -monochrome -resize 824x877
#convert PYEK10.gif -rotate -90 weather_m.png

echo "Composing everything on the template"

# put both halfs on a template
# -crop WIDTH x HEIGHT + HORIZONTAL CUT LEFT + VERTICAL CUT TOP
# -repage +FROM THE LEFT+FROM THE TOP 
# 745 
convert template.png \( solar.png -crop 824x1135+0+0 -repage +5+195 \) \
  \( -font DejaVu-Sans -pointsize 22 -fill black -gravity NorthEast -annotate +15+15 "$(date)" \) \
  \( -font DejaVu-Sans -pointsize 22 -fill black -gravity SouthWest -annotate +15+15 "$(/home/captain/smart_boat/scripts/alerts.py)" \) \
  -mosaic $filename.png

#     \( weather_m.png -crop 824x800+0+40 -repage +10+900 \) \

# uncomment for debug
# display $filename.png
# exit

echo "Cleaning eInk"

# upload an image
scp $filename.png root@192.168.2.2:/tmp/img/

# clear the screen
ssh root@192.168.2.2 /usr/sbin/eips -g /tmp/img/black.png
ssh root@192.168.2.2 /usr/sbin/eips -g /tmp/img/white.png

echo "Updating kindle"

# display the file we just uploaded
ssh root@192.168.2.2 /usr/sbin/eips -g /tmp/img/$filename.png

echo "Done!"
#!/usr/bin/python3
# -*- coding: utf-8 -*-

from gps import *

def get_gps_coordinates(debug=False):
    
  session = gps(mode=WATCH_ENABLE)
  session.stream(WATCH_ENABLE|WATCH_NEWSTYLE)
  
  for report in session:
      
    if (session.fix.latitude >0):
        
      if debug:
        print(report)
        
      return report
    

if __name__ == '__main__':
    # Running as a script
    print("Running as a script")
    msg = get_gps_coordinates(True)
    if msg is None:
      print("nada")
    else:
      print("Lat:", msg.lat)
      print("Lon:", msg.lon)

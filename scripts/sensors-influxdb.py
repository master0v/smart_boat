#!/usr/bin/python3

# https://github.com/alexanderjacuna/ds18b20-influxdb

from influxdb import InfluxDBClient
from time import sleep

import get_gps2
import cpu_temp
# import ds18b20_temp
# import tank_levels
# import wifi
#import digicel
# import get_ping
# import get_preassure

import logging
logging.basicConfig(format='%(name)-8s: %(levelname)-6s %(message)s') # %(asctime)-10s 
logger = logging.getLogger(__name__)
#logger.setLevel(logging.DEBUG)

#SET CONNECTION INFO
host = "localhost"
port = 8086
user = "user"
password = "password"
dbname = "solar"

def getDataAndWriteToInfluxDB():
  logger.debug("Creating the InfluxDBClient")

  #CREATE CLIENT OBJECT
  client = InfluxDBClient(host, port, user, password, dbname)

  logger.debug("Getting GPS")

  gpsMsg = get_gps2.get_gps_coordinates()

  logger.debug("Getting CPU Temperature")

  cpuTemp = cpu_temp.get_cpu_temperature()

  if (gpsMsg == None ):
    data = [{
      "measurement": "sensors",
      "fields": {
        "cpuTemp" : cpuTemp
      }
    }]
  else:
    data = [{
      "measurement": "sensors",
      "fields": {
        "cpuTemp" : cpuTemp,
        "lat" : gpsMsg.lat,
        "lon" : gpsMsg.lon
      }
    }]
  
  # logger.debug("Getting DS18B20 temperature")
  # data[0]["fields"].update(ds18b20_temp.ds18b20_temp())
  #
  # logger.debug("Getting tank levels")
  # data[0]["fields"].update(tank_levels.tank_levels())
  #
  # logger.debug("Getting WIFI signal")
  # data[0]["fields"].update(wifi.get_wifi())
  #
  # logger.debug("Pinging the router")
  # data[0]["fields"].update(get_ping.ping_router())
  #
  # #logger.debug("Getting atmospheric pressure")
  # data[0]["fields"].update(get_preassure.atmospheric_preassure())
  #data[0]["fields"].update(digicel.get_gigabytes())

  logger.debug(data)

  #WRITE DATA
  logger.debug("Writing data to the DB")
  client.write_points(data)
  
  logger.debug("----------------- sleeping -----------------")
  

if __name__ == '__main__':
  getDataAndWriteToInfluxDB()

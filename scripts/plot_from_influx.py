#!/usr/bin/python3

from influxdb import InfluxDBClient
import matplotlib
import matplotlib.pyplot as plt
import matplotlib.ticker as ticker
import matplotlib.dates
from dateutil import tz


def getData(measurement, series, go_back, divisor=1):
  
  host = "expeditious"
  port = 8086
  user = "user"
  password = "password"
  dbname = "solar"
  
  client = InfluxDBClient(host, port, user, password, dbname)
  
  # SELECT mean("V") FROM "power" WHERE time >= now() - 12h and time <= now() GROUP BY time(30s) fill(null);
  
  query = f"SELECT mean(\"{series}\") FROM \"{measurement}\" WHERE time >= now() - {go_back} AND time <= now() GROUP BY time(1m);"
  print(query)
  results = client.query(query)
  
  times = []
  values = []
  
  for point in results.get_points():
    if point['mean'] != None:
      times.append(point['time'])
      values.append(float(point['mean']) / divisor)
    
  return [matplotlib.dates.date2num(times), values]


if __name__ == '__main__':
  
  
  period = "24h" # 15 min
  series = [
    ["power", "V", 1, "volts"],
    ["power", "I", 1000, "amps"],
    ["sensors", "cpuTemp", 1, "C"]
  ]
  
  plt.style.use("grayscale")
  
  fig, axs = plt.subplots(len(series), figsize=(9.4, 10))
  count = 0
  
  for pair in series:
  
    data = getData(pair[0], pair[1], period, pair[2])
    
    axs[count].set_ylabel(pair[3])
  
    axs[count].plot_date(data[0], data[1], linestyle='solid', marker='None') # linewidth=1.0
    axs[count].xaxis.set_major_formatter(
      matplotlib.dates.DateFormatter('%H:%M', tz=tz.gettz("America/New_York")))
      
      
    count+=1

  plt.savefig("solar.png", bbox_inches='tight') # , , , dpi=myDpi
  
  # for debug on a Mac
  import os
  os.system(f"open \"solar.png\"")

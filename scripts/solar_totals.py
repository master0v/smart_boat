#!/usr/bin/python3

from influxdb import InfluxDBClient

host = "expeditious"
port = 8086
user = "user"
password = "password"
dbname = "solar"

client = InfluxDBClient(host, port, user, password, dbname)

def getData(measurement, series):
  query = f"SELECT last(\"{series}\") FROM \"{measurement}\";"
  results = client.query(query)
  return float(results.raw['series'][0]['values'][0][1])


if __name__ == '__main__':
  
  today = getData('today', 'H20')
  today_ah = round(today*1000.0/13.1, 1)
  print(f"Total today: {today} kWh (~ {today_ah} Ah)")
  
  yesterday = getData('today', 'H22')
  yesterday_ah = round(yesterday*1000.0/13.1, 1)
  print(f"Total yesterday: {yesterday} kWh (~ {yesterday_ah} Ah)")
  total = getData('today', 'H19')
  total_ah = round(total*1000/13.1)  
  print(f"Uptime {getData('today', 'HSDS'):.0f} days: {total} kWh (~ {total_ah:,} Ah)")
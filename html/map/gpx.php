<?php
$time_interval = $_GET['from'] . " - " . $_GET['to'];
header("Content-Type: application/force-download; name=\"$time_interval expeditious track.gpx");
header("Content-type: text/xml"); 
header("Content-Transfer-Encoding: binary");
header("Content-Disposition: attachment; filename=\"$time_interval expeditious track.gpx");
header("Expires: 0");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
/*
  <metadata>
    <link href="http://www.masterov.us">
      <text>Alexei Masterov</text>
    </link>
  </metadata>
*/
?><?xml version="1.0"?>
<gpx version="1.1" creator="eXpeditious" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://www.topografix.com/GPX/1/1" xmlns:gpxx="http://www.garmin.com/xmlschemas/GpxExtensions/v3" xsi:schemaLocation="http://www.topografix.com/GPX/1/1 http://www.topografix.com/GPX/1/1/gpx.xsd" xmlns:opencpn="http://www.opencpn.org">
  <trk>
    <name><?php echo $time_interval; ?></name>
    <trkseg>
<?php  
/* http://www.webm.in/influxdb-php/ */
// This part changes every time, so it needs to be done here, all else is static javascript,
// so in can be cached on the browser
//
// $q = 'SELECT last("lat") as lat, last("lon") as lon FROM "sensors"';
//  $q = 'SELECT "lat", "lon" FROM "sensors" WHERE time > \'2020-07-30T14:54:00Z\' AND time <= \'2020-07-30T16:05:00Z\' ORDER BY ASC';
  
  $q = 'SELECT "lat", "lon" FROM "sensors" WHERE time > \'' . $_GET['from'] . 
  ':00Z\' AND time <= \'' . $_GET['to'] .
  ':00Z\' ORDER BY ASC'; 
  
$ch = curl_init();
$query = urlencode($q);
//print_r($query); 
curl_setopt($ch, CURLOPT_URL, "http://localhost:8086/query?db=solar&amp;q=" . $query);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 0);

$headers = array();
$headers[] = "Content-Type: application/x-www-form-urlencoded";
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);
if (curl_errno($ch)) {
  echo 'Error:' . curl_error($ch);
}
curl_close($ch);
$array = json_decode($result, true);
foreach ($array["results"][0]["series"][0]["values"] as $value) {
  echo "      <trkpt lat=\"". $value[1] . "\" lon=\"". $value[2] ."\">\n";
  echo "        <time>". $value[0] . "</time>\n";
  echo "      </trkpt>\n";
}
unset($value);
?>
    </trkseg>
  </trk>
</gpx>
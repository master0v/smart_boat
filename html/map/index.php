<!DOCTYPE html>
<html lang="en">
  <head>
    <title>eXpeditious — Last ~4 days</title>
    <link rel="stylesheet" href="/css/ol.css" type="text/css">
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <script src="/js/popper.min.js"></script>
    <script src="/js/jquery-2.2.3.min.js"></script>
    <script src="/js/bootstrap.min.js"></script>
    <style>
      .map {
        width:100%;
        height:100%;
        position:fixed;
        padding:0;
        margin:0;
        top:0;
        left:0;
        background:rgba(255,255,255,0.5);
      }
      .gpx {
          position:absolute;
          top:10px;
          right:10px;
          padding:3px;
        }
    </style>
    <script src="/js/ol.js"></script>
    <script>
      <?php
        /* http://www.webm.in/influxdb-php/ */
        // This part changes every time, so it needs to be done here, all else is static javascript,
        // so in can be cached on the browser
        //
        // $q = 'SELECT last("lat") as lat, last("lon") as lon FROM "sensors"';
        
        if ($_GET['action']) {
            //$q = 'SELECT LAST("lat"), LAST("lon") FROM "sensors" WHERE time > \'' . $_GET['from'] . 
            //':00Z\' AND time <= \'' . $_GET['to'] .
            //':00Z\' GROUP BY time(5m) ORDER BY DESC';
            $q = 'SELECT "lat", "lon" FROM "sensors" WHERE time > \'' . $_GET['from'] . 
            ':00Z\' AND time <= \'' . $_GET['to'] .
            ':00Z\' ORDER BY DESC'; 
        } else { // GET
            // DEFAULT QUERY: select dots with 5 minute interval
            $q = 'SELECT LAST("lat"), LAST("lon") FROM "sensors" GROUP BY time(5m) ORDER BY DESC LIMIT 1000';
        }
        echo "<!-- $q -->\n";
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
        $js_array = json_encode($array["results"][0]["series"][0]["values"]);
        // Draw map
        echo "var points_matrix = ". $js_array . ";";
      ?>      
    </script>
  </head>
  <body>
    <div id="map" class="map"></div><div id="popup"></div>
    <script src="index.js"></script>
    <div id="gpx" class="gpx">
        <table>
        <tr>
          <form action="index.php" method="get">
            <td>From:&nbsp;</td>
            <td><input type="datetime-local" name="from" value="<?php if($_GET['from']) echo $_GET['from']; else echo date('Y-m-d\TH:i', time()-3600); ?>" class="form-control" id="fromDateTime"></td>
            <td>&nbsp;To:&nbsp;</td>
            <td><input type="datetime-local" name="to" value="<?php if($_GET['to']) echo $_GET['to']; else echo date('Y-m-d\TH:i', time()); ?>" class="form-control" id="toDateTime"></td>
            <td>&nbsp;&nbsp;</td>
            <td><button type="submit" class="btn btn-primary" name="action" value="Update">Update</button></td>
          </form>
<?php if($_GET['action']) : ?>
          <form action="gpx.php">
            <input type="hidden" name="from" value="<?php echo $_GET['from']; ?>">
            <input type="hidden" name="to" value="<?php echo $_GET['to']; ?>">
            <td>&nbsp;&nbsp;</td>
            <td><button type="submit" class="btn btn-primary" name="action" value="GPX">Get GPX</button></td>
          </form>
<?php endif; ?>
        </tr>
        </table>
    </div>
  </body>
</html>
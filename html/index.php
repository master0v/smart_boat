<?php
# /var/www/html/index.php
# alerts
$alerts = shell_exec('/home/captain/smart_boat/scripts/alerts.py 2>&1');
if ($alerts) {
  echo "<pre>$alerts</pre>";
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>eXpeditious</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="css/uikit.min.css" />
  <link rel="stylesheet" href="css/style.css" />
  <script src="js/socket.io.min.js"></script>
  <script src="js/jquery.min.js"></script>
  <script src="js/uikit.min.js"></script>
  <script src="js/uikit-icons.min.js"></script>
  <script>
    function setHref() {
      document.getElementById('modify-me1').href = "http://" + window.location.hostname + ":3001/";
      document.getElementById('modify-me2').href = "http://" + window.location.hostname + ":8002/";
    };
  </script>
  <script type="text/javascript" charset="utf-8">
    var socket;
    $(document).ready(function() {
      console.log("hi");
      socket = io({ 'path': "/switches/socket.io/"} );

      // socket.on('connect', function() {
      //   console.log("connected");
      //   socket.emit('my event', {data: 'I\'m connected!'});
      // });
      
      $('#switches :checkbox').change(function() {
        // this contains a reference to the checkbox
        socket.emit('switch', {'name': $(this).attr('id'), 'state': this.checked});
        console.log($(this).attr('id') + ':' + this.checked);

      });

      // Message recieved from server
      socket.on('switch', function(data) {
          console.log("received " + data.name + '=' + data.state);
          $( "#" + data.name ).prop('checked', data.state);
      });
      
    });
  </script>
</head>
<body onload="setHref()">
    <div class="uk-container">
        <div class="uk-section-xsmall">
            <div class="uk-container">
                &nbsp;
            </div>
        <div>
    
        <div class="uk-section-muted">
            <div class="uk-padding-large uk-card uk-card-default uk-grid-collapse uk-child-width-1-2@s uk-margin uk-grid">
                <div class="uk-card-media-left uk-cover-container">
                  <a href="/">
                    <img src="img/x412.png" alt="Sail Plan and Deck Layout">
                  </a>
                </div>
            <div>
            <div class="uk-card-body uk-primary">
                <h1 class="uk-heading-divider uk-text-center">Welcome aboard</h1>
                <br>
                <!-- SWITCHES -->
                <form id="switches" class="uk-grid-small uk-child-width-1-1@s uk-flex-center uk-text-center" uk-grid>
                    <div class="uk-card uk-card-default uk-card-body">
                      <div>Anchor light</div>
                      <div>
                        <label class="switch">
                          <input type="checkbox" id="led">
                          <span class="slider round"></span>
                        </label>
                      </div>
                    </div>
                  
                </form>
                <!-- grafana dashboard -->
                <p>
                    <a class="uk-link-text" href="#" id="modify-me1" target="_blank">
                        <button class="uk-button uk-button-primary uk-width-1-1 uk-align-center">Dashboard</button>
                    </a>
                </p>
                <!-- chart -->
                <p>
                    <a class="uk-link-text" href="map/" target="_blank">
                        <button class="uk-button uk-button-primary uk-width-1-1 uk-align-center">Chart</button>
                    </a>
                </p>
                <!-- grafana dashboard -->
                <p>
                    <a class="uk-link-text" href="#" id="modify-me2" target="_blank">
                        <button class="uk-button uk-button-primary uk-width-1-1 uk-align-center">WebIOPi</button>
                    </a>
                </p>
        <!--  https://www.boat-specs.com/sailing/sailboats/x-yachts/x-412 -->
                <table class="uk-table uk-table-divider">
                    <tbody>
                        <tr>
                            <td>Length</td>
                            <td>41’</td>
                            <td>12.5 m</td>
                        </tr>
                        <tr>
                            <td>LOW</td>
                            <td>34’ 2”</td>
                            <td>10.43 m</td>
                        </tr>
                        <tr>
                            <td>Beam (width)</td>
                            <td>12’ 7”</td>
                            <td>3.9 m</td>
                        </tr>
                        <tr>
                            <td>Mast (height)</td>
                            <td>61’</td>
                            <td>18.6 m</td>
                        </tr>
                        <tr>
                            <td>Ballast</td>
                            <td>7,716 lbs</td>
                            <td>3500 kg</td>
                        </tr>
                        <tr>
                            <td>Displacement</td>
                            <td>16,314 lbs</td>
                            <td>7400 kg</td>
                        </tr>
                    </tbody>
                </table>
                <br>
                <!-- <div class="uk-text-muted uk-text-center">
                    Connect to WiFi<br>
                    <img class="uk-width-1-2" src="images/eXpeditiousWiFi.png" alt="QR Code">
                </div> -->
            </div>
        </div>
    </div>

    <div class="uk-section-xsmall">
        <p class="uk-text-center uk-text-muted uk-text-small"> &copy; 2022 Alexei Masterov</p>
    </div>
</body>
</html>

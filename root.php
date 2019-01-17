<?php

    require_once './vendor/autoload.php';
    use Kreait\Firebase\Factory;
    use Kreait\Firebase\ServiceAccount;
    require("dbcontroller.php");
    $db_handle = new DBController();

    $today= date('Y-m-d');
    $mobile_number = "9876543210";

    class Users 
    {
        protected $database;
        protected $dbname = 'online_drivers';

        public function __construct(){
            $acc = ServiceAccount::fromJsonFile(__DIR__ . '/secret/fnimerchant-33df8-9453789fad77.json');
            $firebase = (new Factory)->withServiceAccount($acc)->create();

            $this->database = $firebase->getDatabase();
        }

        public function get(string $userID = NULL){
            if (empty($userID) || !isset($userID)) { return FALSE; }

            if ($this->database->getReference($this->dbname)->getSnapshot()->hasChild($userID)){
                return $this->database->getReference($this->dbname)->getChild($userID)->getValue();
            } else {
                return FALSE; 
            }
        }
    }

    $users = new Users();

    $info = $users->get($mobile_number);
    $latitude =  $info['lat'];
    $longitude = $info['lng'];

?>
<html>
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>FOODNI ORDER TRACKING</title>
        <style type="text/css">
            body { font: normal 10pt Helvetica, Arial; }
            #map-canvas {  height: 70%;width: 90%; border: 0px; padding: 0px; }
        </style>
        
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDYFo4wCajCn1NpiAmNXsq_fpAGFMUVAfA"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>

     </head>
     <body style="margin:0px; border:0px; padding:0px;" >
   
         <center>
             <div id="map-canvas" style="margin-top:20px"></div>
         </center>
     <br>
      <form action="#" method="post">

    <?php
            
     $phno = "9876543210";
      $sql = "SELECT `post_id` FROM `wp_postmeta` WHERE `meta_value` = '$phno' ";
      $result = $db_handle->runQuery($sql);

        foreach ($result as  $orderID) 
        { 
           $order_id = $orderID['post_id'];
            
         $sql1 = "SELECT `meta_key`,`meta_value` FROM `wp_postmeta` where `post_id` = '$order_id' 
           AND `meta_key` in ('_billing_phone', '_shipping_address_index','foodni_d_date','contectName','contectNo')";
           
           $result1 = $db_handle->runQuery($sql1);

            foreach ($result1 as $orderinfo) 
            {
               $value[] = $orderinfo['meta_value'];
            }
        }

    ?>
<center>
<table class="table" style="width: 90%;">
  <thead class="thead-dark">
    <tr>
      <th scope="col">orderID</th>
      <th scope="col">client_poc</th>
      <th scope="col">client_address</th>
      <th scope="col">delivery_date</th>
      <th scope="col">merchantpoc_name</th>
      <th scope="col">merchantpoc_number</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <th scope="row"><?php echo $order_id;?></th>
      <td><?php echo $value[0];?></td>
      <td><?php echo $value[1];?></td>
      <td><?php echo $value[2];?></td>
      <td><?php echo $value[3];?></td>
      <td><?php echo $value[4];?></td>
    </tr>
  </tbody>
</table>
</center>

    <?php

        $address ="Pragati Resorts, Hyderabad, Telangana, India"; // Google HQ
        $prepAddr = str_replace(' ','+',$address);
        $apiKey = 'AIzaSyDYFo4wCajCn1NpiAmNXsq_fpAGFMUVAfA';
        $url = "https://maps.googleapis.com/maps/api/geocode/json?address=" . $prepAddr."&sensor=false&key=".$apiKey;
        $geocode=file_get_contents($url); 
        $output= json_decode($geocode);
        $flat = $output->results[0]->geometry->location->lat;
        $flon = $output->results[0]->geometry->location->lng;

        $address = $value[1]; // Google HQ
        $prepAddr = str_replace(' ','+',$address);
        $apiKey = 'AIzaSyDYFo4wCajCn1NpiAmNXsq_fpAGFMUVAfA';
        $url = "https://maps.googleapis.com/maps/api/geocode/json?address=" . $prepAddr."&sensor=false&key=".$apiKey;
        $geocode=file_get_contents($url); 
        $output= json_decode($geocode);
        $tlat = $output->results[0]->geometry->location->lat;
        $tlon = $output->results[0]->geometry->location->lng;

    ?>

    <div >
     <script type="text/javascript">

    function initialize() {
    var mapOptions = {
    zoom: 13,
    featureType: 'road',
    center: new google.maps.LatLng(<?php echo $latitude;?>,<?php echo $longitude;?>),
     mapTypeId: google.maps.MapTypeId.HYBRID, 

     mapTypeControlOptions: {
      mapTypeIds: google.maps.MapTypeId.ROADMAP, google.maps.MapTypeId.HYBRID,
          google.maps.MapTypeId.SATELLITE]
    },
    };

    var map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);

        var image = 'https://img.icons8.com/color/50/000000/marker.png';
        marker = new google.maps.Marker({
        position: new google.maps.LatLng(<?php echo $flat;?>,<?php echo $flon;?>),
        map: map,
        title: '<?php echo "Pragati Resorts, Hyderabad, Telangana, India";?>',
        icon:image
      });

        var image = 'https://img.icons8.com/color/50/000000/finish-flag.png';
        marker = new google.maps.Marker({
        position: new google.maps.LatLng(<?php echo $tlat;?>,<?php echo $tlon;?>),
        map: map,
        title: '<?php echo $value[1];?>',
        icon:image

      });   
        
        var image = 'https://img.icons8.com/color/50/000000/fiat-500.png';
        marker = new google.maps.Marker({
        position: new google.maps.LatLng(<?php echo $latitude;?>,<?php echo $longitude;?>),
        map: map,
        icon:image

      });   

    

    var flightPlanCoordinates = [
    <?php
    $address = "Pragati Resorts, Hyderabad, Telangana, India"; // Google HQ
    $prepAddr = str_replace(' ','+',$address);
    $apiKey = 'AIzaSyDYFo4wCajCn1NpiAmNXsq_fpAGFMUVAfA';
    $url = "https://maps.googleapis.com/maps/api/geocode/json?address=" . $prepAddr."&sensor=false&key=".$apiKey;
    $geocode=file_get_contents($url); 
    $output= json_decode($geocode);
    $lat = $output->results[0]->geometry->location->lat;
    $lon = $output->results[0]->geometry->location->lng;

    //Echo out the users start location

    echo 'new google.maps.LatLng('.$lat.', '.$lon.'),';

    //Assuming route that lat and long coordinates are in multiple records and not in a array within a single record
    //Loop through all records and echo out the positions
 
   echo 'new google.maps.LatLng('.$latitude.', '.$longitude.')';

/*    $address = $to; // Google HQ
    $prepAddr = str_replace(' ','+',$address);
    $apiKey = 'AIzaSyDYFo4wCajCn1NpiAmNXsq_fpAGFMUVAfA';
    $url = "https://maps.googleapis.com/maps/api/geocode/json?address=" . $prepAddr."&sensor=false&key=".$apiKey;
    $geocode=file_get_contents($url); 
    $output= json_decode($geocode);
    $lat2 = $output->results[0]->geometry->location->lat;
    $lon2 = $output->results[0]->geometry->location->lng;    
    echo 'new google.maps.LatLng('.$lat2.', '.$lon2.')';*/

    ?>

    ];

    var flightPath = new google.maps.Polyline({
        path: flightPlanCoordinates,
        geodesic: true,
        strokeColor: 'blue',
        strokeOpacity: 2.0,
        strokeWeight: 5
    });
    flightPath.setMap(map);
    }
    google.maps.event.addDomListener(window, 'load', initialize);

    </script>
    <div id="response"></div>
    </div>
    </form>
    <script type="text/javascript">
    function doRefresh(){
        document.getElementById("map-canvas").style.display = "";
    }
    $(function() {
        setInterval(doRefresh, 3000);
    });
</script>
     </body>
     </html>
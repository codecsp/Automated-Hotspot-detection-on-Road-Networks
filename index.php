 <head>
    <title>AHDORN</title>
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Open+Sans">
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
    <style>
      html, body, #map_shown { height: 100%; margin: 0px; padding: 0px }
          #panel { position: absolute;z-index: 3;         background-color: #f2f2f2;
          height:100%;
}

table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

td{
  border: 1px solid #dddddd;
  text-align: left;
  padding: 8px;
}

th {
  border: 1px solid #dddddd;
  text-align: left;
  padding: 8px;
  color:white;
  text-align:center;
  font-weight: bold;
  background-color: #4CAF50;
}


     
.input{
        font-family: 'Open-Sans', sans-serif;
        font-size: 14px;
        margin: 0;
        min-height: 180px;
        padding:0;
        width: 415px;
    }
#feature
{
  font-family: 'Open-Sans', sans-serif;
  font-size: 14px;
  margin: 0;
  min-height: 30px;
  padding:0;
  width: 265px;
}
    h1{
        font-family: 'Menlo',monospace;
        font-size: 20px;
        font-weight: 400;
        margin:0;
        color: #2f5876;
        text-align:center;
    }
    a:link, a:visited{
        color: #000;
        outline: 0;
        text-decoration: none;
    }
    img{
        width: 30px;
    }

    .modal-header{
        align-items: center;
        border-bottom: 0.5px solid #dadada;

    }

    
    .modal-icons{
        border-top:0.5px solid #dadada;
        height: 50px;
        width: 100%;
    }

    .logo{
        padding:16px;
    }

    .logo-icon{
        vertical-align: text-bottom;
        margin-right: 12px;
    }

    .version{
        color:#444;
        font-size: 18px;
    }

input[type=text] {
  width: 100%;
  padding: 12px 20px;
  margin: 8px 0;
  display: inline-block;
  border: 1px solid #ccc;
  border-radius: 4px;
  box-sizing: border-box;
}

input[type=submit] {
  width: 100%;
  background-color: #4CAF50;
  color: white;
  padding: 14px 20px;
  margin: 8px 0;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

input[type=submit]:hover {
  background-color: #30a049;
}

.input_details {
  
  background-color: #f2f2f2;
  padding: 20px;
}     

    </style>
    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&key=AIzaSyCcJZojV1izegrek8uCFqX499DzaC3qSss"></script>
    
    
    <script type="text/javascript">
        var directionsDisplay;
        var directionsService = new google.maps.DirectionsService();
        var map;
        var displayedAngleInput = false;
        var shiv;
        var want_angle;
        var want_intersection;
        var want_accident;
        var lat=[];
        var lng=[];
        var num_routes;
        var accident_lat=[];
        var accident_lng=[];
        var acc_angle=[];
        var lat_lng=0;
        var acc_lat_angle=[];
        var acc_long_angle=[];
        var dist=[];
        var time=[];
        var lat_long_from_db = [];
        var source_lat,source_lng,dest_lat,dest_lng;
        var no_of_route_intersections = [];                        
        var no_of_route_accidents = [];
        var set_intersection = false;
        var ranks=[];
        var min_angle;
        var max_angle;
        var angle_cnt_lt_30;
        var angle_cnt_bw_30_45;
        var angle_cnt_bw_45_75;
        var angle_cnt_gt_75;
        function initialize() {
            //alert("in initialze");
            directionsDisplay = new google.maps.DirectionsRenderer();

            var mapOptions = {
                zoom: 12,
                center: new google.maps.LatLng(16.8524, 74.5815)
            };
            map = new google.maps.Map(document.getElementById('map_shown'), mapOptions);
            directionsDisplay.setMap(map);
        }

        function calcRoute() {
            var start = document.getElementById('origin').value;
            var end = document.getElementById('destination').value;
            want_angle = document.getElementById("angle").checked;
            want_accident = document.getElementById("accidents").checked;
            min_angle = document.getElementById("minangle").value;
            max_angle = document.getElementById("maxangle").value;
            if(min_angle == "")
              min_angle = 15;
            
            if(max_angle == "")
              max_angle = 180;

            //alert(min_angle);
            want_intersection = document.getElementById("intersection").checked;
            //aert(start);
            var request = {
                origin: start,
                destination: end,
                travelMode: google.maps.TravelMode.DRIVING,
                provideRouteAlternatives: true
            };
            

            directionsService.route(request, function (response, status) {

                if (status == google.maps.DirectionsStatus.OK) {
                    for(var i=0, len=response.routes.length;i < len; i++)
                    {
                        dist[i]=response.routes[i].legs[0].distance.text;
                        time[i]=response.routes[i].legs[0].duration.text;
                        source_lat = response.routes[i].legs[0].start_location.lat();
                        source_lng = response.routes[i].legs[0].start_location.lng();
                        dest_lat = response.routes[i].legs[0].end_location.lat();
                        dest_lng = response.routes[i].legs[0].end_location.lng();
                        start = response.routes[i].legs[0].start_address;
                        end = response.routes[i].legs[0].end_address;
                        num_routes = len;
                        //alert(num_routes);
                        angle_cnt_lt_30 = [];
                        angle_cnt_bw_30_45 = [];
                        angle_cnt_bw_45_75 = [];
                        angle_cnt_gt_75 = [];
                        for(var b = 0;b < num_routes;b++)
                        {
                            no_of_route_intersections[b] = 0;
                            no_of_route_accidents[b] = 0;
                            angle_cnt_lt_30[b] = 0;
                            angle_cnt_bw_30_45[b] = 0;
                            //alert(angle_cnt_bw_30_45[b]);
                            angle_cnt_bw_45_75[b] = 0;
                            angle_cnt_gt_75[b] = 0;
                        }

                        lat[i]=[];
                           lng[i]=[];
                            var points = response.routes[i].overview_path;
                            var sequence_desc = points.length;
                            for (var j = 0; j < points.length; j++) {


                                lat[i][j]=points[j].lat();
                                lng[i][j]=points[j].lng();
                                insert_ajax(start, end, lat[i][j], lng[i][j], (i+1),(j+1), sequence_desc--);
                                //alert("lat "+lat[i][j]);
                            }

                           var render= new google.maps.DirectionsRenderer({
                                map: map,
                                directions: response,
                                routeIndex: i
                            });
                        //alert(response.routes[i].legs[0].distance.text);

                        }

                      calAndStoreAccidenProneLocations(num_routes);

                        var lengths=[];
                        for(var x=0;x<num_routes;x++)
                        {
                            lengths[x]=acc_lat_angle[x].length;
                        }
                        lengths.sort();
                        for(var x=0;x<num_routes;x++)
                        {
                            ranks[x]=lengths.indexOf(acc_lat_angle[x].length);
                        }

                        if(want_angle)
                       displayMarkerAngles(num_routes,ranks);
                      //  alert(start);
                       loadIntersections(start,end);
                       loadAccidents(start,end);
                        //displayMarkerOnAllLatLong(num_routes);
                        //alert(lat_long_from_db[0]["latitude"]);
                        
                        map.addListener('click', function(event) {
                              if (markers.length >= 1) {
                                  deleteMarkers();
                              }

                        
                        
                        alert(event.latLng.lng());
                            });

                        // alert(printTable);
                        
                      //  alert("<?php echo 'hello';   ?>");
                        addSourceAndDestLatLong(start,end,source_lat,source_lng,dest_lat,dest_lng);

                        checkForIntersection(start);
                        checkForIntersection(end);

                    }
                }); 

                }

        function displayMarkerAngles(num_routes, ranks)
        {
            for(var x=0;x<num_routes;x++)
                        {
                          /*  alert("hii");*/
                            //alert(acc_lat_angle[x].length);

                            for(var y=0;y<acc_lat_angle[x].length;y++)
                            {
                                                        //alert("hii");

                                //alert(acc_long_angle[x][y]);
                                var myLatLng={lat: acc_lat_angle[x][y], lng: acc_long_angle[x][y]};
                                var marker = createMarker(myLatLng, " Route:"+(x+1)+" Rank:"+(ranks[x]+1));   
                                
                            }

                        }
        }

        function displayMarkerIntersection(lat_long)
        {
          //  alert("display"+lat_long.length);
            for(var x=0;x<lat_long.length;x++)
                        {
                            no_of_route_intersections[lat_long_from_db[x]["route_no"]]++;
                           // alert("1:"+no_of_route_intersections[lat_long_from_db[x]["route_no"]]);
                            var latitude = lat_long[x]["latitude"];
                            var longitude = lat_long[x]["longitude"];
                          //  alert("for"+latitude+" "+longitude);
                          //alert(want_intersection);
                            if(want_intersection)
                            {
                                    var myLatLng={lat: parseFloat(latitude), lng: parseFloat(longitude)};
                                var marker = createMarkerIntersection(myLatLng, "");   
                            
                            }
                        }
                         var printTable="<table border='1' align='center'><tr><th>Route</th><th>Distance</th><th>Time</th><th>Hotspots</th><th>Rank</th></tr>";
                        for(var q=0;q<num_routes;q++)
                        {
                            //alert("2:"+no_of_route_intersections[q]);
                            printTable=printTable+"<tr><td>"+(q+1)+"</td><td>"+dist[q]+"</td><td>"+time[q]+"</td><td>"+(acc_lat_angle[q].length+no_of_route_intersections[q]+no_of_route_accidents[q])+"</td><td>"+(ranks[q]+1)+"</td></tr>";
                        }
                        printTable=printTable+"<table>";
                       document.getElementById("tb").innerHTML=printTable;
                        document.getElementById("stat_msg1").style.visibility = "visible";
                        document.getElementById("icon").style.visibility = "visible";
            set_intersection = true;
        }

        function showAddFeature()
        {
          document.getElementById("add_feature").style.visibility = "visible";
          document.getElementById("add_feature_button").style.visibility = "collapse";
        }

        function displayMarkerAccidents(lat_long)
        {
          //  alert("display"+lat_long.length);
            for(var x=0;x<lat_long.length;x++)
                        {
                            no_of_route_accidents[lat_long_from_db[x]["route_no"]]++;
                           // alert("1:"+no_of_route_intersections[lat_long_from_db[x]["route_no"]]);
                            var latitude = lat_long[x]["latitude"];
                            var longitude = lat_long[x]["longitude"];
                          //  alert("for"+latitude+" "+longitude);
                          //alert(want_intersection);
                            if(want_accident)
                            {
                                    var myLatLng={lat: parseFloat(latitude), lng: parseFloat(longitude)};
                                var marker = createMarkerAccidents(myLatLng, "");   
                            
                            }
                        }
                         var printTable="<table border='1' align='center'><tr><th>Route</th><th>Distance</th><th>Time</th><th>Hotspots</th><th>Rank</th></tr>";
                        for(var q=0;q<num_routes;q++)
                        {
                            //alert("2:"+no_of_route_intersections[q]);
                            printTable=printTable+"<tr><td>"+(q+1)+"</td><td>"+dist[q]+"</td><td>"+time[q]+"</td><td>"+(acc_lat_angle[q].length+no_of_route_intersections[q]+no_of_route_accidents[q])+"</td><td>"+(ranks[q]+1)+"</td></tr>";
                        }
                        printTable=printTable+"<table>";
                       document.getElementById("tb").innerHTML=printTable;
                        document.getElementById("stat_msg1").style.visibility = "visible";
                        document.getElementById("icon").style.visibility = "visible";
            //set_intersection = true;
        }
        function displayMarkerOnAllLatLong(num_routes)
        {
            for(var x=0;x<num_routes;x++)
                        {
                          /*  alert("hii");*/
                            //alert(acc_lat_angle[x].length);

                            for(var y=0;y<lat[x].length;y++)
                            {
                                                        //alert("hii");

                                //alert(acc_long_angle[x][y]);
                                var myLatLng={lat: lat[x][y], lng: lng[x][y]};
                                var marker = createMarker(myLatLng, " Route:"+(x+1));   
                                
                            }
                        }
        }

        function checkForIntersection(source)
         {
              var urlString ="source="+source;
                  $.ajax
                  ({
                  url: "mark_as_intersection.php",
                  type : "POST",
                  cache : false,
                  data : urlString,
                  success: function(response)
                  {
                    //alert(response);
                  }
                  });
         }
   
         function addSourceAndDestLatLong(start,end,source_lat,source_lng,dest_lat,dest_lng)
         {
              var urlString ="source="+start+"&destination="+end+"&source_lat="+source_lat+"&source_lng="+source_lng+"&dest_lat="+dest_lat+"&dest_lng="+dest_lng;
                  $.ajax
                  ({
                  url: "add_src_dest_lat_lng.php",
                  type : "POST",
                  cache : false,
                  data : urlString,
                  success: function(response)
                  {
                    //alert(response);
                  }
                  });
         }

         function importDB()
         {
          var urlString = "";
                  $.ajax
                  ({
                  url: "import.php",
                  type : "POST",
                  cache : false,
                  data : urlString,
                  success: function(response)
                  {
                    alert(response);
                  }
                  });
         }
         function exportDB()
         {
                  var urlString = "";
                  $.ajax
                  ({
                  url: "export.php",
                  type : "POST",
                  cache : false,
                  data : urlString,
                  success: function(response)
                  {
                    alert(response);
                  }
                  });
         }
            function insert_ajax(source, destination, latitude, longitude, route_no, seq_asc, seq_desc)
              {

                  var urlString ="source="+source+"&destination="+destination+"&latitude="+latitude+"&longitude="+longitude+"&route_no="+route_no+"&seq_asc="+seq_asc+"&seq_desc="+seq_desc;
                  $.ajax
                  ({
                  url: "insert_ajax.php",
                  type : "POST",
                  cache : false,
                  data : urlString,
                  success: function(response)
                  {
                    //alert(response);
                  }
                  });
              }
                function loadIntersections(source, destination)
                {
                   // alert("load latLng");
                    var ajax = new XMLHttpRequest();
                    var method = "GET";
                    var url = "find_intersections_copy.php?source="+source+"&destination="+destination;
                    var async = true;

                    ajax.open(method, url, async);
                    ajax.send();
                    //alert("Hii");
                    ajax.onreadystatechange = function()
                    {
                        if(this.readyState == 4 && this.status == 200)
                        {
                          //  alert("in ready");
                            lat_long_from_db = JSON.parse(this.responseText);
                           // alert(lat_long_from_db[0]["latitude"]);

                            displayMarkerIntersection(lat_long_from_db);
                        }
                    }
                }
               
               function loadAccidents(source, destination)
                {
                   // alert("load latLng");
                    var ajax = new XMLHttpRequest();
                    var method = "GET";
                    var url = "load_accidents.php?source="+source+"&destination="+destination;
                    var async = true;

                    ajax.open(method, url, async);
                    ajax.send();
                    //alert("Hii");
                    ajax.onreadystatechange = function()
                    {
                        if(this.readyState == 4 && this.status == 200)
                        {
                          //  alert("in ready");
                            lat_long_from_db = JSON.parse(this.responseText);
                           // alert(lat_long_from_db[0]["latitude"]);

                            displayMarkerAccidents(lat_long_from_db);
                        }
                    }
                }

                function calAndStoreAccidenProneLocations(num_routes)
                {
                    //alert(num_routes);

                    calculateAccidentalAngles(num_routes);
                    //calculateAccidentalIntersections(num_routes);
                }

                function calculateAccidentalIntersections(num_routes)
                {

                }

                function calculateAccidentalAngles(num_routes)
                {
                    for(var i=0;i<num_routes;i++)
                    {
            //            alert(lat[i].length);
                        acc_lat_angle[i]=[];
                        acc_long_angle[i]=[];
                        var counter=0;
                        for(var j=1;j<lat[i].length-1;j++)
                        {

                            var angle1=angleFromCoordinate(lat[i][j],lng[i][j],lat[i][j-1],lng[i][j-1]);
                            var angle2=angleFromCoordinate(lat[i][j],lng[i][j],lat[i][j+1],lng[i][j+1]);
                            var angle=0;
                            var categ="";
                            var ang1=angle1,ang2=angle2;
                            if((angle1>=0 && angle1<=45 && angle2>=0 && angle2<=45) || (angle1>=45 && angle1<=180 && angle2>=45 && angle2<=180) || (angle1>=180 && angle1<=270 && angle2>=180 && angle2<=270) || (angle1>=270 && angle1<=360 && angle2>=270 && angle2<=360))
                            {
                                categ="category1";
                                if(angle1<angle2)
                                {
                                    var tmp=angle2;
                                    angle2=angle1;
                                    angle1=tmp;
                                }    
                                angle=angle1-angle2;
                                //alert(angle1+" "+angle2+" "+angle);
                            }
                            else
                                if((angle1<=45 && angle2>=270)||(angle2<=45 && angle1>=270))
                                {
                                    categ="category2";
                                    if(angle1<=45)
                                    {
                                        angle2=360-angle2;
                                    }
                                    else
                                    {
                                        angle1=360-angle1;
                                    }
                                     if(angle1<angle2)
                                        {
                                            var tmp=angle2;
                                            angle2=angle1;
                                            angle1=tmp;
                                        }    
                                        angle=angle1-angle2;
                                        //alert(angle1+" "+angle2+" "+angle);
                                }
                                else
                                    if((angle1<=45 && angle2<=180)||(angle1<=180 && angle2<=45))
                                    {
                                        categ="category3";
                                        if(angle1<angle2)
                                        {
                                            var tmp=angle2;
                                            angle2=angle1;
                                            angle1=tmp;
                                        }    
                                        angle=angle1-angle2;
                                        //alert(angle1+" "+angle2+" "+angle);       
                                    }
                                    else
                                    if((angle1<=45 && angle2<=270)||(angle1<=270 && angle2<=45))
                                    {
                                        categ="category4";
                                        if(angle1<=45)
                                        {
                                            angle2=angle2-180;
                                        }
                                        else
                                        {
                                            angle1=angle1-180;
                                        }
                                        if(angle1<angle2)
                                        {
                                            var tmp=angle2;
                                            angle2=angle1;
                                            angle1=tmp;
                                        }    
                                        angle=angle1-angle2;
                                        //alert(angle1+" "+angle2+" "+angle);       
                                    }
                                    else
                                        if((angle1<=180 && angle1>=45 && angle2>=180 && angle2<=270) || (angle2<=180 && angle2>=45 && angle1>=180 && angle1<=270))
                                        {
                                            categ="category5";
                                            if(angle1<angle2)
                                            {
                                                var tmp=angle2;
                                                angle2=angle1;
                                                angle1=tmp;
                                            }    
                                            angle=angle1-angle2;
                                           // alert(angle1+" "+angle2+" "+angle);
                                        }
                                    else
                                        if((angle1<=180 && angle1>=45 && angle2>=270 && angle2<=360) || (angle2<=180 && angle2>=45 && angle1>=270 && angle1<=360))
                                        {
                                            categ="category6";
                                            if(angle1<=180)
                                            {
                                                angle2=angle2-180;
                                            }
                                            else
                                            {
                                                angle1=angle1-180;
                                            }
                                            if(angle1<angle2)
                                            {
                                                var tmp=angle2;
                                                angle2=angle1;
                                                angle1=tmp;
                                            }    
                                            angle=angle1-angle2;
                                            //alert(angle1+" "+angle2+" "+angle);
                                        }
                                        else
                                            if((angle1<=360 && angle1>=270 && angle2>=180 && angle2<=270) || (angle2<=360 && angle2>=270 && angle1>=180 && angle1<=270))
                                            {
                                                categ="category7";
                                                if(angle1<angle2)
                                                {
                                                    var tmp=angle2;
                                                    angle2=angle1;
                                                    angle1=tmp;
                                                }    
                                                angle=angle1-angle2;
                                                //alert(angle1+" "+angle2+" "+angle);       
                                            }
                                       // alert("categkeory:"+categ+" lat:"+lat[i][j]+" lng:"+lng[i][j]+"main angle1:"+ang1+" main angle2:"+ang2+" angle1:"+angle1+" angle2:"+angle2+" angle:"+angle);


                        if(angle > min_angle && angle <= max_angle)
                        {    
                            /*var myLatLng={lat: lat[i][j], lng: lng[i][j]};
                            var marker = createMarker(myLatLng, "angle:"+angle+" Route:"+(i+1));*/

                        if(angle > 0 && angle < 30){
                          angle_cnt_lt_30[i]++;
                         // alert("cnt"+angle_cnt_lt_30[i]);
                        }
                        else
                          if(angle >= 30 && angle <= 45){
                            angle_cnt_bw_30_45[i]++;
                          //alert("cnt"+angle_cnt_bw_30_45[i]);
                          }
                          else
                            if(angle > 45 && angle < 75){
                            angle_cnt_bw_45_75[i]++;
                          }
                          else if(angle > 75){
                            angle_cnt_gt_75[i]++;
                          }
                            acc_lat_angle[i][counter]=lat[i][j];
                            acc_long_angle[i][counter]=lng[i][j];
                            
                            //alert(acc_lat_angle[i][counter]);
                            counter++;
                        }

                        }
                    }
                }
                function createMarker(pos, t) {
                    var marker = new google.maps.Marker({       
                        position: pos, 
                        map: map,  // google.maps.Map 
                        title: t      
                    }); 
                    return marker;  
                }

                function addFeature()
                {
                  document.getElementById("add_feature").style.visibility = "collapse";
                  document.getElementById("add_feature_button").style.visibility = "visible";
                    var e = document.getElementById("feature");
                    var feature_name = e.options[e.selectedIndex].value;
                    var latitude = document.getElementById("acc_lat").value;
                    var longitude = document.getElementById("acc_lng").value;
                    var urlString ="latitude="+latitude+"&longitude="+longitude+"&feature="+feature_name;
                    $.ajax
                    ({
                    url: "add_feature.php",
                    type : "POST",
                    cache : false,
                    data : urlString,
                    success: function(response)
                    {
                      alert(response);
                    }
                    });
                }

                function displayInputField()
                {
                  displayedAngleInput =  document.getElementById("minangle").style.display;
                 // alert(displayedAngleInput);
                  if(displayedAngleInput == 'none')
                  {
                   // alert("in if");
                    document.getElementById("minangle").style.display = "block";
                    document.getElementById("maxangle").style.display = "block";
                }
                else
                {
                   // alert("in else");
                    document.getElementById("minangle").style.display = "none";
                    document.getElementById("maxangle").style.display = "none";
                }
                }
                function createMarkerIntersection(pos, t) {
                    var marker = new google.maps.Marker({       
                        position: pos, 
                        map: map,  // google.maps.Map 
                        title: t,
                        icon: {
                              url: "http://maps.google.com/mapfiles/ms/icons/yellow-dot.png"
                            }
      
                    }); 
                    return marker;  
                }

                function createMarkerAccidents(pos, t) {
                    var marker = new google.maps.Marker({       
                        position: pos, 
                        map: map,  // google.maps.Map 
                        title: t,
                        icon: {
                              url: "http://maps.google.com/mapfiles/ms/icons/blue-dot.png"
                            }
      
                    }); 
                    return marker;  
                }
                function getDistanceFromLatLonInM(lat1,lon1,lat2,lon2) {
                  var R = 6371; // Radius of the earth in km
                  var dLat = deg2rad(lat2-lat1);  // deg2rad below
                  var dLon = deg2rad(lon2-lon1); 
                  var a = 
                    Math.sin(dLat/2) * Math.sin(dLat/2) +
                    Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) * 
                    Math.sin(dLon/2) * Math.sin(dLon/2)
                    ; 
                  var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a)); 
                  var d = R * c * 1000; // Distance in km
                  return d;
                }

                function deg2rad(deg) {
                  return deg * (Math.PI/180)
                }
                function angleFromCoordinate(lat1, long1, lat2, long2) {

                    lat1 = Math.radians(lat1);
                    lat2 = Math.radians(lat2);
                    long1 = Math.radians(long1);
                    long2 = Math.radians(long2);

                    var dLon = long2 - long1;

                    var y = Math.sin(dLon) * Math.cos(lat2);
                    var x = Math.cos(lat1) * Math.sin(lat2) - Math.sin(lat1) * Math.cos(lat2) * Math.cos(dLon);

                    var brng = Math.atan2(y, x);
                   // alert("bearing "+brng);
                    brng = brng * 180 / Math.PI;
                    brng = (brng + 360)% 360;

                    return brng;
                }

                function showRouteStats()
                {
                //  alert("Show route");
                    var printTable="<p>Minimum Angle: "+min_angle+" Maximum Angle: "+max_angle+"</p><table border='1' align='center'><tr><th rowspan='2'>Route</th><th colspan='5'>Angles</th><th rowspan='2'>Intersections</th><th rowspan='2'>Accidents</th></tr><tr><td><30</td><td>30-45</td><td>30-75</td><td>>75</td><td>total</td></tr>";
                    //printTable=printTable+"<table>";
                      //  alert(printTable);
                      //alert(num_routes)
                    for(var q=0;q<num_routes;q++)
                    {
                      //alert(angle_cnt_gt_75[q]);
                        printTable=printTable+"<tr><td>"+(q+1)+"</td><td>"+angle_cnt_lt_30[q]+"</td><td>"+angle_cnt_bw_30_45[q]+"</td><td>"+angle_cnt_bw_45_75[q]+"</td><td>"+angle_cnt_gt_75[q]+"</td><td>"+acc_lat_angle[q].length+"</td><td>"+no_of_route_intersections[q]+"</td><td>"+no_of_route_accidents[q]+"</td></tr>";
                    }
                    printTable = printTable + "</table>"
                    //alert(printTable)
                    document.getElementById("tb_route_stat").style.visibility = "visible";
                    document.getElementById("tb_route_stat").innerHTML=printTable;
                    document.getElementById("stat_msg2").style.visibility = "visible";
                    document.getElementById("stat_msg1").style.visibility = "collapse";
                }

                function hideRouteStats()
                {
                    document.getElementById("stat_msg1").style.visibility = "visible";
                    document.getElementById("stat_msg2").style.visibility = "collapse";
                    document.getElementById("tb_route_stat").style.visibility = "collapse";
                }
                Math.radians = function(degrees) {
                  return degrees * Math.PI / 180;
                };

        google.maps.event.addDomListener(window, 'load', initialize);
    </script>
    
</head>
<body>
    <div id="panel" style="overflow: scroll; ">


        <div class="input">
                <div class="modal-header">
                    <h1 class="logo">
                        <img src="images/ahdorn_logo.png" class="logo-icon" alt="AHDORN Launcher">Automatic Hotspot Detection<br> on Road Network 
                        <span class="version">(1.0.0)</span> 
                    </h1>
                </div>
                
            <?php
                if(!isset($_POST['source']))
                {
                    echo '  <div class="modal-content">
                        <div class="input_details">
                            <label for="origin">Source</label>
                            <input type="text" id="origin" name="source" placeholder="Enter source name...">
            
                            <label for="destination">Destination</label>
                            <input type="text" id="destination" name="destination" placeholder="Enter destination name...">
                            <input id="angle" type="checkbox" value="angles" name="angles" onclick="displayInputField()" >Angles
                            <input id="intersection" type="checkbox" value="intersections" name="intersections" checked>Intersections
                            <input id="accidents" type="checkbox" value="accidents" name="accidents" checked>Accidents
                            <input type="text" id="minangle" placeholder="min angle(optional)" name="min_angle" style="display:none;">
                            <input type="text" id="maxangle" placeholder="max angle(optional)" name="max_angle" style="display:none;">
                            <input type="submit" value="Get Your Route" onclick="calcRoute()">
                        </div>
                    </div>';
                }
                else
                {
                    $source=$_GET["source"];
                    $destination=$_GET["destination"];
                    echo '
                    
                    <div class="modal-content">
                        <div class="input_details">
                            <label for="origin">Source</label>
                            <input type="text" id="origin" value="'.$source.'">
                            <label for="destination">Destination</label>
                             <input type="text" id="destination" value="'.$destination.'">                
                            <input type="submit" value="Get Your Route" onclick="calcRoute()">
                        </div>
                    </div>';
                }
            ?>
            <div class="input_details">
              <button onclick="importDB()">Import Database</button><button style="margin-left: 75px;" onclick="exportDB()">Export Database</button>
            <input id = "add_feature_button" type="submit" value="Add Feature" onclick="showAddFeature()">
          <div id="add_feature" style="visibility: hidden;">
          Choose Feature:   <select id="feature" style="">
            <option value="accident">Accident</option>
            <option value="petrol_pump">Petrol Pump</option>
            <option value="hospital">Hospital</option>
            <option value="hotel">Hotel</option>
          </select>
          <input type="text" id="acc_lat" placeholder="enter latitude" name="acc_lat">
                            <input type="text" id="acc_lng" placeholder="enter longitude" name="acc_lng">
          <input type="submit" value="Add Feature" onclick="addFeature()">  
          </div>

        </div>

        </div>

        
        <div id="icon" style="visibility: hidden;">
            <table style="border: none;">
           <tr>
               <td>
                <img src="/images/red.png">   
               </td>
               <td>
                   - indicates curves(i.e. angles)
               </td>
           </tr>
           <tr>
               <td>
                <img src="/images/yellow.png">   
               </td>
               <td>
                   - indicates intersections
               </td>
           </tr>
           <tr>
               <td>
                <img src="/images/blue.png">   
               </td>
               <td>
                   - indicates accidents
               </td>
           </tr>
       </table>
        </div>
       <div id="tb"></div>

       <p  id="stat_msg1" onclick="showRouteStats()" style="width:50%;color: #4CAF50;margin: 25px;visibility: collapse;"><u>Show Route Statistics</u></p>
       <div id="tb_route_stat"></div>
               <p id="stat_msg2" onclick="hideRouteStats()" style="width:50%;color: #4CAF50;margin: 25px;visibility: collapse;"><u>Hide Route Statistics</u></p>

    </div>
    <div id="map_shown" style="z-index: 2"></div>   
</body>
</html>

<?php

include("connection.php");

$source = $_GET['source'];
$destination = $_GET['destination'];

function findDistance(
  $latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000)
{
  // convert from degrees to radians
  $latFrom = deg2rad($latitudeFrom);
  $lonFrom = deg2rad($longitudeFrom);
  $latTo = deg2rad($latitudeTo);
  $lonTo = deg2rad($longitudeTo);
  $latDelta = $latTo - $latFrom;
  $lonDelta = $lonTo - $lonFrom;

  $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
    cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
  return $angle * $earthRadius;
}

$arr_lat = array();
$arr_lng = array();
$arr_route_id = array();

//fetch route_id for all routes
$fetch_num_routes = mysqli_query($conn,"select * from routes where source = '$source' and destination = '$destination' order by route_no asc");
$num_routes = mysqli_num_rows($fetch_num_routes);
$i = 0;
while($row = mysqli_fetch_assoc($fetch_num_routes))
{
	$arr_route_id[$i++] = $row["id"];
}

//load current route lat-long
for($i = 0;$i < $num_routes;$i++)
{
	$arr_lat[$i] = array();
	$arr_lng[$i] = array();
	$j = 0;
	$r_id = $arr_route_id[$i];
	$fetch_lat_lng = mysqli_query($conn,"select * from lat_long where route_id = '$r_id' order by seq_asc asc");
	while($row = mysqli_fetch_assoc($fetch_lat_lng))
	{
		$arr_lat[$i][$j] = $row['latitude'];
		$arr_lng[$i][$j++] = $row['longitude'];
	}	
}

//load intersections table in an array
$arr_lat_intersections = array();
$arr_lng_intersections = array();
$p = 0;
$no_of_inter;
$fetch_intersections = mysqli_query($conn," select distinct latitude, longitude from lat_long where route_id not in (select id from routes where source='$source' and destination='$destination');");
if(($no_of_inter = mysqli_num_rows($fetch_intersections)) > 0)
{
	while($row = mysqli_fetch_assoc($fetch_intersections))
	{
		$arr_lat_intersections[$p] = $row["latitude"];
		$arr_lng_intersections[$p] = $row["longitude"];
		$p++;
	}
}

//echo "<br>".$no_of_inter;
//echo '<br>'.findDistance(18.398584,76.601839,18.3986453,76.6016869);
$arr_inter_result = array();
$p = 0;
for($k = 0;$k < sizeof($arr_lat_intersections);$k++)
{
	for($i = 0;$i < $num_routes;$i++)
	{
		$min = 1000;
		$cur_lat;
		$cur_lng;
		for($j = 0;$j < sizeof($arr_lat[$i]);$j++)
		{
				$distance = findDistance($arr_lat[$i][$j], $arr_lng[$i][$j], $arr_lat_intersections[$k], $arr_lng_intersections[$k]);
				if($distance < 15)
				{
					//echo '<br>route no:'.$i.' distance:'.$distance.' '.$arr_lat[$i][$j].' '.$arr_lng[$i][$j].' '.$arr_lat_intersections[$k].' '.$arr_lng_intersections[$k];
					if($distance < $min)
					{
						$min = $distance;
						$cur_lat = $arr_lat[$i][$j];
						$cur_lng = $arr_lng[$i][$j];
					}
				}
		}

		if($min != 1000)
		{
			$arr_inter_result[$p] = array();
			$arr_inter_result[$p]["route_no"]=$i;
			$arr_inter_result[$p]["latitude"] = $cur_lat;
			$arr_inter_result[$p++]["longitude"] = $cur_lng;
		}
	}
}

echo json_encode($arr_inter_result);

?>
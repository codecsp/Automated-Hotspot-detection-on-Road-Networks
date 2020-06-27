<?php
include("connection.php");
$source = $_POST["source"];

$already_present = mysqli_query($conn,"select * from loc_lat_long where loc_name = '$source'");
$row = mysqli_fetch_assoc($already_present);
$degree = $row['degree'];
$src_lat = $row["latitude"];
$src_lng = $row["longitude"];

if($degree < 3)
{
	$arr_route_id  = array();
	$i = 0;
	$routes = mysqli_query($conn,"select * from routes where source = '$source'");
	$no_routes = mysqli_num_rows($routes);
	echo $no_routes.'<br>';
	$marked = array();
	while($row = mysqli_fetch_assoc($routes))
	{
		$arr_route_id[$i] = $row["id"];
		$src = $row['source'];
		$marked[$i] = 0;
		$i++;
	}

	$degree = 0;
	for($route1 = 0;$route1 < $no_routes;$route1++)
	{
		$r1_id = $arr_route_id[$route1];
		$m = 0;
		for($route2 = 0;$route2 < $no_routes;$route2++)
		{
			if($route1 != $route2)
			{
			$r2_id = $arr_route_id[$route2];
			$q;
			$compare;
			$q = '"select * from ((select latitude ,longitude, seq_asc s1 from lat_long where route_id='.$r1_id.' order by seq_asc asc) tb1, (select latitude ,longitude, seq_asc s2 from lat_long where route_id='.$r2_id.' order by seq_asc asc) tb2) where tb1.latitude=tb2.latitude and tb1.longitude=tb2.longitude order by s1;"';
			$compare = mysqli_query($conn,"select * from ((select latitude ,longitude, seq_asc s1 from lat_long where route_id=$r1_id order by seq_asc asc) tb1, (select latitude ,longitude, seq_asc s2 from lat_long where route_id=$r2_id order by seq_asc asc) tb2) where tb1.latitude=tb2.latitude and tb1.longitude=tb2.longitude order by s1;");
			//echo '<br>'.$q;
			
			$matched_lat_long = 0;
			$prev_seq1;
			$cur_seq1;
			$prev_seq2;
			$cur_seq2;
			while ($row = mysqli_fetch_assoc($compare)) {
				$cur_seq1 = $row['s1'];
				$cur_seq2 = $row['s2'];
				if($matched_lat_long != 0)
				{
					if(($cur_seq1 - $prev_seq1) > 10)
					{
						if(($cur_seq2 - $prev_seq2) > 10)
							break;
					}

				}
				$prev_seq1 = $cur_seq1;
				$prev_seq2 = $cur_seq2;
				$matched_lat_long++;
			}
			echo "<br>Matched:".$matched_lat_long." route1:".$r1_id." with route2:".$r2_id;
			if ($matched_lat_long >= 10)
			 {
			 	echo "<br>Marked route2:".$r2_id." as matched";
			 	$marked[$route2] = 1;
			 	$m = 1;
			}
			}
		}
		if($m == 1)
		{
			if( $marked[$route1] == 0)
			{
				$degree++;
				$marked[$route1] = 1;
				echo "<br>else block marked route1:".$r1_id." as matched degree:".$degree;
			}
			else
			{
				echo "<br>route1:".$r1_id." is already matched!!";	
			}
		}
		else
		{
			if( $marked[$route1] == 0)
			{
				$degree++;
				$marked[$route1] = 1;
				echo "<br>else block marked route1:".$r1_id." as matched degree:".$degree;
			}
		}
	}

	echo "<br>degree: ".$degree;

	$marked_source = mysqli_query($conn, "update loc_lat_long set degree = '$degree' where loc_name = '$source'");
		if($marked_source)
			echo "updated degree";
		else
			echo "failed updating";
	if($degree >= 3)
	{
		$inserted_source_as_intersection = mysqli_query($conn,"insert into intersections(loc_name, latitude, longitude) values('$source','$src_lat','$src_lng')");
		if($inserted_source_as_intersection)
			echo "inserted intersection";
		else
			echo "failed inserting";
	}
}
?>
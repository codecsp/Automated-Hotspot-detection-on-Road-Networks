<?php
	include("connection.php");

	$latitude = $_POST['latitude'];
	$longitude = $_POST['longitude'];
	$source = $_POST['source'];
	$destination = $_POST['destination'];
	$route_no = $_POST['route_no'];
	$seq_asc = $_POST['seq_asc'];
	$seq_desc = $_POST['seq_desc'];
	$inserted = true;
	$route_id = 0;
	if($conn)
	{
		$route_exists = mysqli_query($conn, "select * from routes where (source = '$source' and destination = '$destination' and route_no = $route_no)");
		if(mysqli_num_rows($route_exists) == 0)
		{
			if(!mysqli_query($conn, "insert into routes(source, destination, route_no) values('$source','$destination',$route_no)"))
			{
				$inserted = false;
			}
		}

		if($inserted)
		{
			$get_route_id = mysqli_query($conn, "select id from routes where source = '$source' and destination = '$destination' and route_no = $route_no");
			if(mysqli_num_rows($get_route_id) != 0)
			{
				$row = mysqli_fetch_assoc($get_route_id);
				$route_id = $row['id'];	

				$entry_exists = mysqli_query($conn, "select * from lat_long where route_id = $route_id and latitude = '$latitude' and longitude = '$longitude'");
				if(mysqli_num_rows($entry_exists) == 0)
				{
					$result = mysqli_query($conn, "insert into lat_long(route_id, latitude, longitude, seq_asc, seq_desc) values($route_id, '$latitude','$longitude',$seq_asc, $seq_desc)");    
					if($result)
					{
						echo "success!";	
					}
					else
						echo "Failure!".mysqli_error($conn);
					}
				}
	}
	}
?>
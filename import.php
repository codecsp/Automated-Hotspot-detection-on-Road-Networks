<?
include("connection.php");
	if(file_exists("export.csv"))
	{
		$fread = fopen("export.csv","r");
  	$feature = '';
  	while(! feof($fread))  {
		$result = chop(fgets($fread));
		$str_arr = explode("#", $result);
		if($result == 'features' || $result == 'routes' || $result == 'lat_long' || $result == 'loc_lat_long')
		{
		 	//echo "hi";
			$feature = $result;
		 	continue;
		}
		if($result != '' && $feature == 'features')
		{
			
			$inserted = mysqli_query($conn,"insert into features(latitude, longitude, feature) values('$str_arr[0]','$str_arr[1]','$str_arr[2]')");
			//echo $result."i<br>";
		}
		if($result != '' && $feature == 'lat_long')
		{
			// echo $result.'<br>';	
			// echo $str_arr[0];
			$inserted = mysqli_query($conn,"insert into lat_long(route_id, latitude, longitude,seq_asc, seq_desc) values($str_arr[0],'$str_arr[1]','$str_arr[2]',$str_arr[3],$str_arr[4])");
			//echo $result."i<br>";
		}
		if($result != '' && $feature == 'routes')
		{
			//echo $result.'<br>';
			//echo $str_arr[0];	
			$inserted = mysqli_query($conn,"insert into routes(source, destination, route_no) values('$str_arr[0]','$str_arr[1]',$str_arr[2])");
			//echo $result."i<br>";
		}
		if($result != '' && $feature == 'loc_lat_long')
		{
			
			$inserted = mysqli_query($conn,"insert into loc_lat_long(loc_name, latitude, longitude, degree) values('$str_arr[0]','$str_arr[1]','$str_arr[2]','$str_arr[3]')");
			//echo $result."i<br>";
		}
}
fclose($fread);
echo "imported the database";
	}
	else
	{
		echo "'export.csv' does not exist in htdocs folder";
	}
?>
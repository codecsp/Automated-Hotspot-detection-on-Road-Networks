<?php
include("connection.php");
$file_export = 'features';
$res = mysqli_query($conn,"select * from features");
if(mysqli_num_rows($res))
{
	while ($row = mysqli_fetch_assoc($res)) {
		$file_export = $file_export.PHP_EOL.trim($row["latitude"]).'#'.trim($row["longitude"]).'#'.trim($row["feature"]);
	}
}
$file_export = $file_export.PHP_EOL.'routes';
$res = mysqli_query($conn,"select * from routes");
if(mysqli_num_rows($res))
{
	while ($row = mysqli_fetch_assoc($res)) {
		$file_export = $file_export.PHP_EOL.trim($row["source"]).'#'.trim($row["destination"]).'#'.trim($row["route_no"]);
		//echo trim($row["source"]).'#'.trim($row["destination"]).'#'.trim($row["route_no"]);
	}
}
$file_export = $file_export.PHP_EOL.'lat_long';
$res = mysqli_query($conn,"select * from lat_long");
if(mysqli_num_rows($res))
{
	while ($row = mysqli_fetch_assoc($res)) {
		$file_export = $file_export.PHP_EOL.trim($row["route_id"]).'#'.trim($row["latitude"]).'#'.trim($row["longitude"]).'#'.trim($row["seq_asc"]).'#'.trim($row["seq_desc"]);
		//echo trim($row["route_id"]).'#'.trim($row["latitude"]).'#'.trim($row["longitude"]).'#'.trim($row["seq_asc"]).'#'.trim($row["seq_desc"]).'<br>';
	}
}
$file_export = $file_export.PHP_EOL.'loc_lat_long';
$res = mysqli_query($conn,"select * from loc_lat_long");
if(mysqli_num_rows($res))
{
	while ($row = mysqli_fetch_assoc($res)) {
		$file_export = $file_export.PHP_EOL.trim($row["loc_name"]).'#'.$row["latitude"].'#'.trim($row["longitude"]).'#'.trim($row["degree"]);
	}
}

	//echo $file_export;
	$file = fopen("export.csv", "w+") or die("Unable to open file!");
	fwrite($file, $file_export);
	echo "exported the file in export.csv";
	fclose($file);
?>
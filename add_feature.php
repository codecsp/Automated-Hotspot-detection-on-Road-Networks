<?php
include("connection.php");
$latitude = $_POST['latitude'];
$longitude = $_POST['longitude'];
$feature = $_POST['feature'];

$insert_feature = mysqli_query($conn,"insert into features(latitude, longitude, feature) values('$latitude','$longitude','$feature')");
if($insert_feature)
	echo "Added the feature";
else
	echo "Failed to add the feature";
?>
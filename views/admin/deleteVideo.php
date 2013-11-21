<?php

function deleteVideo(Database $db, Request $request){
	$video = $request->get("video");
	deleteFiles($video);
	deleteinDB($db, $video);
	header ("Location: admin.php");
} 

function deleteInDB(Database $db, $unsafeFilename){
	$fileName  = $db->escapeString($unsafeFilename);
	$sql = "DELETE FROM video WHERE filename = '$fileName'";
	$db->query($sql);
	return;
}

function deleteFiles($video){
	unlink("videos/$video.mp4");
	unlink("videos/$video.webm");
	unlink("img/$video.png");
	return;
}
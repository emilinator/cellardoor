<?php

include_once "classes/video.class.php";

function upload(Database $db, Request $request){
	$uploadState = $request->get("uploadState");

	if ($uploadState === "new-video-submitted"){
		$filename =uploadVideo ("videos");

		convertVideo ($filename);
		$thumb = createPoster ($filename);

		insertVideoInDB ($db, $request, $filename, $thumb);
		header("Location:index.php?page=upload");
	}else{

	$out = showUploadForm();
	}
	return $out;
 }

function showUploadForm(){
	$action = "index.php?page=upload&amp;uploadState=new-video-submitted";
	$out = "<form method='post' action='$action' enctype='multipart/form-data'>";
	$out .= "<label>Write a title for the video</label>";
	$out .= "<input type='text' name='video-title' />";
	$out .= "<label>Write a description for the video</label>";
	$out .= "<textarea name='video-description' /></textarea>";
	$out .= "<input type='file' name='video-file' />";
	$out .= "<input type='submit' value='upload' />";
	$out .= "</form>";
	return $out;
}

function uploadVideo ($folder){
	$uploader = new Uploader ("video-file");
	$uploader ->acceptedType ("video/mp4", "video/webm");
	$uploader ->uploadTo ($folder)->upload();
	$filename =$uploader->getFilename();
	return $filename ;
}

function convertVideo($filename){
	$basename = pathinfo($filename, PATHINFO_FILENAME);
	$vid = new Video("videos/$filename");
	if ($vid->type() === "mp4"){
		$vid->saveAsWEBM("videos", "$basename.webm");
	}else{
		$vid->saveAsMP4("videos", "$basename.mp4");
	}
	return;
}

function createPoster($filename){
	$basename = pathinfo($filename, PATHINFO_FILENAME);
	$vid = new Video("videos/$filename");
	$thumbSrc = "img/$basename.png";
	$vid->createPNG ("img", "$basename.png");
	return $thumbSrc;
}

function insertVideoInDB (Database $db, Request $request, $unsafeFilename, $unsafeThumb){
	$filename = $db->escapeString ($unsafeFilename);
	$basename = pathinfo ($filename, PATHINFO_FILENAME);
	$thumb = $db->escapeString ($unsafeThumb);
	$title = $db->escapeString($request->get("video-title"));
	$description = $db->escapeString($request->get("video-description"));
	
	$sql = "INSERT INTO video (filename, title, thumb, description) VALUES('$basename', '$title', '$thumb', '$description' )";
	$db->query($sql);
	return;
}



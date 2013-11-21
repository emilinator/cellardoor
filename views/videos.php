<?php
//controller

include "views/videoComments.php";
function videos( Database $db, Request $request ){

	$selectedVideo = $request->get("video_filename");
	if ($selectedVideo ){
		$out = showVideo($db, $selectedVideo);

include_once "views/videoLikes.php";
        $out .= videoLikes( $db, $request );
        $videoComments = videoComments( $db, $selectedVideo );
$out .= $videoComments -> asHTML(); 
	}else{
		$out = showAllTitles($db);
	}
	return $out;
}
function showAllTitles( Database $db ) {
	$allVideos = getAllTitles( $db);
	$ul ="<ul>";
	foreach ($allVideos as $row){
		$title = $row->get("title");
		$fileName =$row->get("filename");
		$thumbnail = $row->get("thumb");
		$href ="index.php?page=videos&amp;video_filename=$fileName";
		//$ul .= "<li><a href=$href>$fileName</a></li>";
		$ul .= "<li><a href=$href><img src='".$thumbnail."' /></a></li>";

	}
		$ul .="</ul>";
		return $ul;
}

function showVideo( Database $db, $fileName ) {
	$videoData = getVideo ($db, $fileName);
	$title = $videoData ->get("title");

	$description = $videoData ->get("description");
	$thumb = $videoData ->get("thumb");

	$out = "<h1>$title</h1>
			<video controls poster\"$thumb\">
				<source src =\"videos/$fileName.mp4\" type=\"video/mp4\"/>
				<source src =\"videos/$fileName.webm\" type=\"video/webm\"/>

			</video><p>$description</p>";

return $out;
}

function getVideo( Database $db, $fileName ) {
	$fname = $db -> escapeString( $fileName );
	$sql = "SELECT filename, title, description, thumb FROM video WHERE filename = '$fname' ";
	$data = $db -> getData( $sql );
	$row = $data -> getAt( 0 );
	return $row;
}

function getAllTitles( Database $db ) {
	$sql = "SELECT filename, title, thumb FROM video";
	return $db -> getData( $sql );
}
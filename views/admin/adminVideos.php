<?php
//controller


function adminVideos( Database $db, Request $request ){

	$selectedVideo = $request->get("show-video");
	if ($selectedVideo ){
		$out = showVideo($db, $selectedVideo);

		include_once "views/videoLikes.php";
        $out .= videoLikes( $db, $request );

        include "views/admin/deleteVideoComments.php";
        $videoComments = deleteVideoComments( $db, $request );
		$out .= $videoComments; 
	}else{
		$out = showAllTitles($db);
	}
	return $out;
}
function showAllTitles( Database $db ) {
	$allVideos = getAllTitles( $db);
	$out = "<ul id='admin-video-list'>";
	
	foreach ($allVideos as $row){

		$fileName =$row->get("filename");
		
		$thumbnail = $row->get("thumb");
		$showHref = "admin.php?adminPage=adminVideos&amp;show-video=$fileName";
		$deleteHref = "admin.php?adminPage=deleteVideo&amp;video=$fileName";
		$out .= "<li>
					<figure> <img src='$thumbnail' /> </figure>
					<a href='$showHref'>Show video</a>
					<a href='$deleteHref'>Delete video</a>
				</li>";

	}
		$out .= "</ul>";
		return $out;
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
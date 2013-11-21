<?php
function videoLikes(Database $db, Request $request){
	$id = $request->get("video_filename");
	$newLikeSubmitted = $request->get( "new-like" );
	if ( $newLikeSubmitted ){
		insertVideoLike( $db, $id );
		$url = "index.php?page=videos&video_filename=$id";
		header("Location: $url");
	}
	$newLikeSubtracted = $request->get( "dislike" );
	if ($newLikeSubtracted ){
		insertVideoDislike( $db, $id );
		$url = "index.php?page=videos&video_filename=$id";
		header("Location: $url");
	}
	$count = getVideoLikes($db, $id);
	$out = showVideoLikes($id, $count);
	return $out;
}



function getVideoLikes ( Database $db, $id ){
	$sql = "SELECT SUM(value) AS likes
			From Video_likes WHERE video_filename = '$id' ";
	$result = $db->getData($sql);
	$row = $result->getAt(0);
	$count = $row->get("likes");
	return $count;
}

function insertVideoLike( Database $db, $id ){
	$sql = "insert into Video_likes(value, video_filename) value(1, '$id' )";
	$db->query( $sql );	
}

function insertVideoDislike( Database $db, $id ){
	$sql = "insert into Videoikes(value, video_filename) value(-1, '$id' )";
	
	$db->query( $sql );
}


function showVideoLikes($id, $count){
	$form = "
		<form method=post action=index.php?page=videos&amp;video_filename=$id>
			<span><p>The user rating: $count.</p></span><input type=submit name=new-like value=like /> <input type=submit name=dislike value=dislike />
		</form>";
	return $form;
}




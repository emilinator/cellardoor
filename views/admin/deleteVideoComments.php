<?php
function deleteVideoComments( Database $db, Request $request) {
    // get the comment id from the request string
    $commentToDelete = $request->get("delete-comment");
    
    // get the video we are currently viewing from the request string
    $selectedVideo = $request->get("show-video");

    // if we have a comment id, then delete it from the database
    if ($commentToDelete){
        deleteComment($db, $commentToDelete);
    }   

    // show the comments
    $out = showCommentsFor($db, $selectedVideo);
    return $out;
}

function deleteComment(Database $db, $unsafeId){
    $id = $db->escapeString($unsafeId);
    $sql = "DELETE FROM comments WHERE comment_id = $id";
    $db->query($sql);
    return;
}



function showCommentsFor( Database $db, $fileName ) { 
    $allComments = getCommentsFor( $db, $fileName ); 
    
    $out = "<div>";
    foreach ($allComments as $commentData) {
        $user = $commentData->get("author");
        $comment = $commentData->get("comment");
        $commentId = $commentData->get("comment_id");
        $deleteHref = "admin.php?adminPage=adminVideos&amp;show-video=$fileName&amp;delete-comment=$commentId";
        $out .= "<p><a href='$deleteHref'>Delete comment</a></p>
                $user wrote: <p>$comment</p>";

    }

    $out .="</div>";
    return $out;
}

function getCommentsFor( Database $db, $filename ) {
    $sql = "SELECT comment_id, author, comment, filename FROM comments WHERE filename = '$filename' ORDER BY comment_id DESC"; 
return $db->getData($sql);
}


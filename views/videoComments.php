<?php

function videoComments( Database $db, $id ) {
    $out = html("section");

    $formData = new Request("post");
    $newCommentSubmitted = $formData->get("new-comment");  //googla object to string!!
    if ( $newCommentSubmitted ) {
        insertComment( $db, $formData ); 
    }
            $out->append( showForm($id) );
        $out->append( showComments($db, $id) );

    
return $out;
}

function showComments( Database $db, $filename ) { 
    $allComments = getComments( $db, $filename ); 
    $ul = html("ul");
    foreach ( $allComments as $commentData ) {
        $author = $commentData->get("author");
        $comment = $commentData->get("comment"); 
        $ul->append("<li><h2>$author wrote:</h2><p>$comment</p></li>");
}
return $ul; 
}

function getComments( Database $db, $filename ) {
    $sql = "SELECT comment_id, author, comment, filename FROM comments WHERE filename = '$filename' ORDER BY comment_id DESC"; 
return $db->getData($sql);
}

function showForm( $filename ) {
    $action = "index.php?page=videos&amp;video_filename=$filename";
    $form = html("form")->attr("id", "comment_form"); 
    $form->attr("action", $action)->attr("method", "post"); 
    $hidden = emptyTag("input")->attr("type", "hidden"); 
    $hidden->attr("name", "id")->attr("value", $filename); 
    $form->append($hidden);
    $form->append(
                '<table>
                <tr>
                    <td><label><p>Write your name</p></label></td> </tr>

                    <tr>
                    <td><input type="text" name="user-name" /></td></tr>
                    <tr> 
                    <td><label><p>Write your comment</p></label></td> </tr>
                   
                   <tr>
                   <td><textarea name="new-comment"></textarea></td>
                   </tr>

                   <tr> 
                    <td><input type="submit" value="post!" /></td></tr>
                </table>'
    );
return $form; 
}

function insertComment( Database $db, Request $formData ) {

    $filename = $formData->get( "video_filename" );
    $author = $formData->get( "user-name" );
    $comment = $formData->get( "new-comment" );
    $sql = "INSERT INTO comments ( author, comment, filename) 
    VALUES ( '$author', '$comment', '$filename' )";
    
    $db->query( $sql ); 
}


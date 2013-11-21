<?php

function showVideo( Database $db, $whichVideo ) {
    $data = getVideo( $db, $whichVideo );
    $title = $data->get( "title" );
    $filename = $data->get( "filename" );
    $poster = $data->get("thumb");
    $description = $data->get( "description" );
    $out = "<h1>$title</h1>";
   
    $out .= "<video controls poster='img/$poster'>
            <source src='videos/$filename.webm' type='video/webm' />
            <source src='videos/$filename.mp4' type='video/mp4'/>
        </video>
        <p>$description</p>";
    $out.='<div class="addthis_toolbox addthis_default_style">
               <a class="addthis_button_facebook_like" fb:like:layout="button_count"></a>
               <a class="addthis_button_tweet"></a>
               <a class="addthis_button_google_plusone" g:plusone:size="medium"></a>
               <a class="addthis_button_pinterest_pinit"></a>
          </div>
          <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=xa-51a3a8482896645d"></script>
          <br>        

          <div class="fb-comments" 
               data-href="http://daxxi.org/index.php?page=videos&show-video=$filename" 
               data-width="680" data-num-posts="10">
          </div>     
';
    return $out;
}
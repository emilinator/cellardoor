<?php

class Video {

    private $src;
    private $path;

    /**
     * Create a new Video object
     * The Video object can convert a video file to webm and mp4 format
     * The Video object can also create png thumbs of 1 frame, 5 seconds into the video
     * The Video object requires ffmpeg to be installed
     * 
     * @param type $src
     * @param type $path 
     */
    function __construct( $src, $path = "/usr/local/bin/ffmpeg" ) {
        $this->src = $src;
        $this->path = $path;
        if ( $this->isFfmpegAvailable() === false ) {
            trigger_error( "The Video object requires ffmpeg. PHP did not find ffmpeg. Perhaps ffmpeg is not installed or you need to specify a relative path to ffmpeg so PHP can find it. " );
        }
    }

    /**
     * returns the video's filetype, essentially it's extension name
     * @return string filetype
     */
    public function type() {
        return pathinfo( $this->src, PATHINFO_EXTENSION );
    }

    /**
     * Create a png of one video frame at 5 seconds into the video
     * The created png will be saved in the indicated folder under the indicated filename
     * @param type $destinationFolder the path to the folder
     * @param type $destinationFilename the base filename to use (do not indicate suffix)
     */
    public function createPNG( $destinationFolder, $destinationFilename, $destTime = "00:00:02" ) {
        if( substr($destinationFilename, -4) != ".png"){
            $destinationFilename = "$destinationFilename.png";
        }
        $this->checkFolder( $destinationFolder );
        $destination = "$destinationFolder/$destinationFilename";
        $video = $this->src;
        $cmd = "-i $video -an -vcodec png -vframes 1 -ss $destTime $destination";
        $this->execute( $cmd );
    }

    /**
     * 
     * Create a webm version of the video 
     * The created webm will be saved in the indicated folder under the indicated filename
     * @param type $destinationFolder the path to the folder
     * @param type $destinationFilename the base filename to use (do not indicate suffix)
     */
    public function saveAsWEBM( $destinationFolder, $destinationFilename ) {
        if( substr($destinationFilename, -5) != ".webm"){
            $destinationFilename = "$destinationFilename.webm";
        }
        $destination = "$destinationFolder/$destinationFilename";
        $video = $this->src;
        $this->execute( "-i $video $destination" );
        
    }

    /**
     *
     * Create a mp4 version of the video 
     * The created mp4 will be saved in the indicated folder under the indicated filename
     * @param type $destinationFolder the path to the folder
     * @param type $destinationFilename the base filename to use (do not indicate suffix)
     */
    public function saveAsMP4( $destinationFolder, $destinationFilename ) { 
        if( substr($destinationFilename, -4) != ".mp4"){
            $destinationFilename = "$destinationFilename.mp4";
        }
        $destination = "$destinationFolder/$destinationFilename";
        $video = $this->src;
        $this->execute( "-i $video $destination" );
    }

    private function execute( $cmd ) {
        $path = $this->path;
        $code =  "$path -y $cmd >/dev/null 2>/dev/null &";
        exec($code, $out, $ret);
        //print_r($out);
    }

    private function checkFolder( $folder ) {
        if ( !is_writable( $folder ) ) {
            trigger_error( "Cannot create a file in the $folder folder. Change permission settings" );
        }
    }

    private function isFfmpegAvailable() {
        exec( $this->path . " -version 2>&1", $out, $ret );
        if ( $ret ) {
            $isAvailable = false;
        } else {
            $isAvailable = true;
        }
        return $isAvailable;
    }

}


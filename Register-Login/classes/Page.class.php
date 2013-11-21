<?php

class Page {
    private $title;
    private $completePage;
    private $bodyContent;
    private $cssHrefs = array();
    private $cssElements;
    private $scriptSrcs = array();
    private $scriptElements;
    
    public function css( $href ){
        array_push($this->cssHrefs, $href);
    }
    
    public function addScript( $src ){
        array_push($this->scriptSrcs, $src);
    }
    
    private function buildCSSElements(){
        foreach( $this->cssHrefs as $href ){
            $this->cssElements .= "<link href='$href' type='text/css' rel='stylesheet' />";
        }
    }
    
    public function buildScriptElements(){
        foreach( $this->scriptSrcs as $src ){
            $this->scriptElements .= "<script src='$src' type='text/javascript'></script>";
        }
    }


    private function buildPage(){
        $this->buildCSSElements();
        $this->buildScriptElements();
        $this->completePage = '<!DOCTYPE html>
            <html><head>
                    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
                        <title>'. $this->title .'</title>
                        '. $this->cssElements .'
                </head>
                <body> '. $this->bodyContent .'
                        '. $this->scriptElements .'</body></html>';
    }
    
    
    public function body( $content ){
        $this->bodyContent .= $content;
    } 
    
    public function __construct( $title = "" ) {
        $this->title = $title;
    }
    
    public function asHTML(){
        $this->buildPage();
        return $this->completePage;
    }
}



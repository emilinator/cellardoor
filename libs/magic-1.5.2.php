<?php
/* #######################################################
 * @author: Thomas Blom Hansen
 * @version 1.5.1
 * www.magic-php.net
 * 
 * Version date 08-02-2013
 * @copyright This script may be used for non-violent purposes, completely free of obligations. 
 * Change it as you see fit. 
 * 
 * Code sections
 * 
 * 1) configuration
 * 2) html generation
 * 3) data storage datatypes
 * 4) various utility classes, including 
 *        Mailer for sending e-mails
 *        ImageFile for manipulating images
 *        Request for controlling http requests
 *        Uploader for controlling file uploads
 *        Session for controlling sessions
 * 5) MySQL database connection class

 * 
 * 6) code from other developers integrated into magic-php
 *        CleanOutput: for outputing nicely indented HTML
 * 
 * 7) alias' and functions for method chaining
 * 
 * 
 * ########## Change log for 1.5.2 #############################
 * @todo simplify Request $_REQUEST 
 * remember to test with Uploader and clear without redirect
 * 
 * Error message added for Uploader::__construct()
 * 
 * ########## Change log for 1.5.1 #############################
 * 
 * Database::host() added
 * error messages for sql errors added
 * function html() returns EmptyHtmlElm if passed $tag is void, else it returns HtmlElm
 * 
 * ##########Change log for 1.5 ################################
 * 
 * Renamed objects and functions
 * Controller renamed to Request
 * FileController renamed to Uploader
 * SessionController renamed to Session
 * Page::getURL renamed to Page::url
 * Session:destroy renamed to Session::clear ( interface similar to Request)
 * 
 * 
 * Updates
 * Object->get( $key ) now returns FALSE if no key was found
 * Uploader::acceptedType now accepts a comma-separated list of mime types as parameter
 * Request now inherits from Object
 * javascripts appended to the end of <body>, not inside <head>
 * 
 * 
 * New methods:
 * AbstractHTMLElement::after()
 * AbstractHTMLElement::before()
 * HtmlElm::prepend
 * Page::attr()
 * Page::indent()
 * 
 * 
 * Extra error messages added:
 * mysqli connect error
 * SQL errors 
 * multiple includes of magic
 * multiple includes of any file
 * header already sent forced by Request::clear() or whitespace
 * Line number error reporting improved
 * 
 * 
 * Deleted:
 * Class Img - use emptyTag"("img") instead
 * ArrayList::length - use ArrayList::count() instead
 * ArrayList::getLast - use something like Arraylist::getAt( ArrayList::count()-1 ) instead
 * Request::method - pass method parameter on instantiation instead
 * AbstractHtmlElm::addClass - use attr("class", "newclass") instead
 * 
 * 
  ####################################################### */









/* #######################################################
 * 
 * 1) configuration
 * 
  ####################################################### */




//use highest level of error reporting
error_reporting( E_STRICT );
//but don't show default php error messages
ini_set( 'display_errors', 0 );


set_error_handler( 'magicErrorHandler' );

//fatal errors escape the normal error handler for arcane reasons probably
register_shutdown_function( 'fatalErrorShutdownHandler' );

function magicErrorHandler( $code, $message, $file, $line ) {

    $path = explode( "/", $file );
    $f = array_pop( $path );
    $folder = array_pop( $path );
    if ( $code == E_ERROR || E_COMPILE_ERROR ) {
        $showErrorLine = true;
        /* special case - mysqli connect error */
        if ( preg_match( "/mysqli_connect()/", $message ) ) {
            $message = "PHP was unable to connect to your database. You must have used an incorrect database name, database username or database password in your PHP code.";
            $showErrorLine = false;
        }
        
         /* special case - magic-php included more than once */
        if ( preg_match( "/Cannot redeclare/", $message ) ) {
   
            $errorar = debug_backtrace();
            $include = basename($errorar[0]["args"][2]);
            $message = "It seems you have included $include twice. You should only include the same file once. Search your project for additional include statements.";
            $showErrorLine = false;
            
        }


        /* special case - magic-php included more than once */
        if ( preg_match( "/Cannot redeclare magicErrorHandler()/", $message ) ) {
            $message = "You have included the magic PHP library more than once in your PHP code - that will not work. 
                Look through all your include statements and delete the superfluous include statement";
            $showErrorLine = false;
        }

        /* special case: headers already sent error forced by Request::clear() */
        if ( preg_match( "/Cannot modify header information/", $message ) ) {
            $origMessage = $message;

            $error_array = debug_backtrace();
            foreach ( $error_array as $error ) {
                if ( isset( $error["function"] ) ) {

                    //error occurred after calling Request::clear()
                    if ( $error["function"] === "clear" ) {

                        $f = basename( $error["file"] );
                        $l = $error["line"];
                        //$stdMessage = str_replace("output started at ", "", $stdMessage);
                        $stdMessageArray = explode( ":", $origMessage );
                        $outputStartedFile = $stdMessageArray[0];
                        $outputStartedLine = $stdMessageArray[1];
                        $outputStartedLine = str_replace( ")", " ", $outputStartedLine );

                        $message = "Headers are already sent and cannot be modified. ";
                        $message .= "A header has been sent before you called Request::clear() in $f, line $l";

                        $fString = str_replace( "Cannot modify header information - headers already sent by (output started at ", "", $origMessage );
                        $fString = str_replace( ")", " ", $fString );
                        $fPath = explode( "/", $fString );
                        $file = array_pop( $fPath );
                        $folder = array_pop( $fPath );
                        $fArray = explode( ":", $file );
                        $f = $fArray[0];


                        $f = "$folder/$f";


                        $message .= "<br><b>Try to look in</b> $f, in line $outputStartedLine. Perhaps you have a php end tag nearby? Try deleting it!";
                        $showErrorLine = false;
                    }
                }
            }
        }



        /* special case: file permission error on upload */
        if ( preg_match( "/move_uploaded_file.+failed to open stream: Permission denied/", $message ) ) {
            $message = "PHP was unable to write file to disk. The upload destination folder on your server seems to need read & write permission";
            $showErrorLine = false;
        }

        /* special case, passing an instance of AbstractHTMLElm wrapped in " " as parameter */
        if ( preg_match( "/Object of class (Img|HtmlElm|EmptyHtmlElm) could not be converted to string/", $message ) ) {
            //die("!");
            //print_r( debug_backtrace() );
            $errorAr = debug_backtrace();
            $pathAr = explode( "/", $errorAr[1]["file"] );
            $file = array_pop( $pathAr );
            $folder = array_pop( $pathAr );
            $line = $errorAr[1]["line"];
            $functionError = " ";
            if ( isset( $errorAr[1]["function"] ) ) {
                $fn = $errorAr[1]["function"];
                $functionError = " when you called function $fn()";
            }
            $message .= ".<br> Perhaps you combined a string and an object into one parameter$functionError?<br>";
        }
        $msg = "<h1>php found an error in your code</h1>
                <p><b>Error was:</b> $message<br>";
        if ( $showErrorLine ) {
            $msg .= "<b>Found in:</b> $folder/$file, line $line </p>";
        }
    } else {

        if ( $code == E_WARNING || $code == E_USER_WARNING || $code == E_USER_NOTICE ) {

            echo $code;
            $msg = "<h1>php found something that might be an error</h1> 
                     <p>It was not a big problem at all, your code runs, but you might as well look into it right away
                     since the problem might cause greater problems later</p>";
            //print_r( debug_backtrace() );
        } else {
            $msg = "<h1>php found an error in your code</h1>";
        }
        $msg .= "<p><b>Error was:</b> $message in $folder/$f in line $line</p>";
        $msg .= "<h3>There are a few places you might find the troublesome code</h3>";

        $list = "<ul>";
        //let's refine the default php error messages so info is a little more informative for beginners 
        $erArray = debug_backtrace();
        foreach ( $erArray as $er ) {
            $l = $er["line"] || "unknown";
            $file = $er["file"] || "unknown file";
            $path = @explode( "/", $file );
            //remove file
            $f = @array_pop( $path );
            $folder = @array_pop( $path );
            $fn = $er["function"] || "unknown function";
            if ( $fn == "magicErrorHandler" ) {
                $fnText = "";
            } else {
                $fnText = "at function $fn";
            }
            $list .= "<li>Line $l in $folder/$f $fnText";
            if ( $f == "magic.php" ) {
                $list .= " though it is very unlikely that the error is here";
            }
            $list .= "</li>";
        }
        $list .= "</ul>";
        $msg .= $list;
    }
    die( $msg );
}

function fatalErrorShutdownHandler() {
    $last_error = error_get_last();
    $errorType = $last_error['type'];
    if ( $errorType === E_ERROR || $errorType === E_COMPILE_ERROR || $errorType === E_PARSE ) {
        magicErrorHandler( $last_error['type'], $last_error['message'], $last_error['file'], $last_error['line'] );
    }
}

@session_start();






/* #######################################################
 * 
 * 2) HTML generation
 * 
  ####################################################### */

abstract class AbstractHTMLElement {

    protected $after = "";
    protected $before = "";
    protected $tag_name;
    protected $attributes = "";

    public function __construct( $tag ) {
        $this->tag_name = $tag;
    }

    /**
     *
     * Declare an attribute 
     * @param string $attr : the attribute name
     * @param type $value : (optional) the attribute value
     * @return an HTML object with the attribute declared
     */
    public function attr( $attr, $value = "" ) {
        $this->attributes .= " $attr";
        if ( $value != "" ) {
            $this->attributes .= "=\"$value\"";
        }
        return $this;
    }
    
       /**
     * Declares a new id attribute on the created HTML element 
     * @param type $id
     * @return \Page 
     */
    public function id($id){
        $this->attr("id", $id);
        return $this;
    }

    public function after() {
        $num_of_args = func_num_args();
        if ( $num_of_args == 1 && gettype( func_get_arg( 0 ) ) == "array" ) {
            $args = func_get_arg( 0 );
            $num_of_args = count( $args );
        } else {
            $args = func_get_args();
        }
        for ( $i = 0; $i < $num_of_args; $i++ ) {
            $arg = $args[$i];
            if ( $arg instanceof AbstractHTMLElement ) {
                $this->after .= $arg->asHTML();
            } else {
                $this->after .= $arg;
            }
        }
        return $this;
    }

    public function before() {
        $num_of_args = func_num_args();
        if ( $num_of_args == 1 && gettype( func_get_arg( 0 ) ) == "array" ) {
            $args = func_get_arg( 0 );
            $num_of_args = count( $args );
        } else {
            $args = func_get_args();
        }
        for ( $i = 0; $i < $num_of_args; $i++ ) {
            $arg = $args[$i];
            if ( $arg instanceof AbstractHTMLElement ) {
                $this->before = $arg->asHTML() . $this->before;
            } else {
                $this->before = $arg . $this->before;
            }
        }
        return $this;
    }

    /**
     * returns the HTML object as a string ready to be echoed 
     */
    public abstract function asHTML();
}

class HtmlElm extends AbstractHTMLElement {

    protected $content;
    protected $objects = array();

    /**
     * Declares a new HTML container element
     * @param string $tag: the tag name
     * @param string $content: the content of the element
     */
    public function __construct( $tag, $content = "" ) {
        if ( $content instanceof AbstractHTMLElement ) {
            $content = $content->asHTML();
        }
        $type = gettype( $content );
        if ( $type != "string" ) {
            $this->constructorError( $type, $content );
        }

        $this->tag_name = $tag;
        $this->content = $content;
    }

    private function constructorError( $type, $content ) {
        $class = @get_class( $content );
        if ( $type == "object" ) {
            $type = "$class() $type";
        }
        $error = "The second parameter for HTML objects must be a string. You passed a $type.\n\r";
        $error_r = debug_backtrace();
        $file = $error_r[1]['file'];
        $line = $error_r[1]['line'];
        $error .= "<p>The error occurs in $file in line $line.</p>";
        Magic::alert( $error );
    }

    /**
     * prepends (or adds) content as the first child inside the HTML element
     * @param: string or HTML object to be prepend. 
     * Multiple parameters may be supplied comma-separated  
     * @return the HTML element with content prepended
     */
    public function prepend() {
        $num_of_args = func_num_args();
        if ( $num_of_args == 1 && gettype( func_get_arg( 0 ) ) == "array" ) {
            $args = func_get_arg( 0 );
            $num_of_args = count( $args );
        } else {
            $args = func_get_args();
        }
        for ( $i = 0; $i < $num_of_args; $i++ ) {
            $arg = $args[$i];
            if ( $arg instanceof AbstractHTMLElement ) {
                $arg = $arg->asHTML();
            }
            $this->content = $arg . $this->content;
        }
        return $this;
    }

    /**
     * appends (or adds) content inside the HTML element
     * @param: string or HTML object to be appended. 
     * Multiple parameters may be supplied comma-separated  
     * @return the HTML element with content appended
     */
    public function append() {
        $num_of_args = func_num_args();
        if ( $num_of_args == 1 && gettype( func_get_arg( 0 ) ) == "array" ) {
            $args = func_get_arg( 0 );
            $num_of_args = count( $args );
        } else {
            $args = func_get_args();
        }
        for ( $i = 0; $i < $num_of_args; $i++ ) {
            $arg = $args[$i];
            if ( $arg instanceof AbstractHTMLElement ) {
                $this->content .= $arg->asHTML();
            } else {
                $this->content .= $arg;
            }
        }
        return $this;
    }

    /**
     * Returns the created object as HTML string
     * @return html 
     */
    public function asHTML() {
        return "$this->before<$this->tag_name$this->attributes>$this->content</$this->tag_name>$this->after";
    }

}

class EmptyHtmlElm extends AbstractHTMLElement {

    protected $tag_name;
    protected $attributes = "";

    /**
     * Create a void HTML element
     * @param type $tag: the HTML tag 
     */
    public function __construct( $tag ) {
        parent::__construct( $tag );
    }

    /**
     * Returns the created object as HTML string
     * @return html 
     */
    public function asHTML() {
        return "$this->before<$this->tag_name$this->attributes />$this->after";
    }

}

class Page {

    private $doctype = "<!DOCTYPE html>";
    private $xhtmlDoctype = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
    private $html;
    private $head;
    private $body;
    private $title = " ";
    private $indent = true;
    private $scripts = "";


    public function contentHeader( $stuff ) {
        $this->body->append( "<div id='header'>".$stuff."</div>" );
        return $this;

    }
    /**
     * Creates a well-formed HTML page
     * By default, it'll create an HTML5 page template, but you can switch to xhtml Strict  
     */
    public function __construct() {
        $this->html = new HtmlElm( "html" );
        $head = new HtmlElm( "head" );
        $meta = new EmptyHtmlElm( "meta" );
        $this->head = $head->append( $meta->attr( "http-equiv", "Content-Type" )->attr( "content", "text/html;charset=utf-8" ) );
        $this->body = new HtmlElm( "body" );
    }

    /**
     * returns the absolute url including any query string
     * @return string 
     */
    public function url() {
        return Magic::url();
    }

    /**
     *  switch to xhtml 1.0 Strict doctype
     * @return \Page 
     */
    public function xhtmlDTD() {
        $this->doctype = $this->xhtmlDoctype;
        $this->html->attr( "xmlns", "http://www.w3.org/1999/xhtml" );
        return $this;
    }

    /**
     * Sets a title for the created HTML page
     * @param string $t the title to create
     * @return \Page 
     */
    public function title( $t ) {
        $this->title = $t;
        return $this;
    }

    /**
     * Establish a <link> to an external stylesheet
     * If you call method several times, you will have links to several stylesheets
     * @param string $href the href of the stylesheet to use
     * @return \Page 
     */
    public function css( $href ) {
        $link = new EmptyHtmlElm( "link" );
        $this->head->append( $link->attr( "href", $href )->attr( "rel", "stylesheet" ) );
        return $this;
    }

    /**
     * Adds an external javascript to the page
     * @param string $src the source of the script to use
     * @return \Page 
     */
    public function addScript( $src ) {
        $script = html( "script" )->attr( "src", $src )->attr( "type", "text/javascript" );
        $this->scripts .= $script->asHTML();
        return $this;
    }

    /**
     * Add content to the page <head>
     * @return Page 
     */
    public function head() {
        $num_of_args = func_num_args();
        for ( $i = 0; $i < $num_of_args; $i++ ) {
            $this->head->append( func_get_arg( $i ) );
        }
        return $this;
    }

    /**
     * Add content to the page <body>
     * @return Page
     */
    public function body() {
        $this->body->append( func_get_args() );
        return $this;
    }

    /**
     * Declares an html attribute on the <body> element
     * 
     * @param string $attr the attribute to declare
     * @param string $value the attribute's value
     * @return \Page 
     */
    public function attr( $attr, $value ) {
        $this->body->attr( $attr, $value );
        return $this;
    }
    
 

    private function buildPage() {
        $this->head->append( new HtmlElm( "title", $this->title ) );
        $this->body->append( $this->scripts );
        $head = $this->head->asHTML();
        $body = $this->body->asHTML();
        return $this->html->append( $head, $body )->asHTML();
    }

    /**
     * Should HTML for page be indented or not? 
     * By default page output will be indented for readability. 
     * Call Page::indent(false) to prevent indentation
     * 
     * @param boolean $indent 
     */
    public function indent( $indent = true ) {
        $this->indent = $indent;
    }

    /**
     * returns the entire page as HTML
     * @return type 
     */
    public function asHTML() {
        $page = $this->doctype;
        $page .= $this->buildPage();
        if ( $this->indent ) {
            $cleaning_object = new CleanOutput();
            $page = $cleaning_object->clean( $page );
        }
        return $page;
    }

}

/* #######################################################
 * 
 * 3) Data storage classes
 * 
  ####################################################### */

class ArrayList implements Iterator {

    protected $list;
    protected $index = 0;

    /**
     * Create a new ArrayList object
     * Optional: you may pass elements as comma-separated parameters 
     */
    public function __construct() {
        if ( func_num_args() > 0 ) {
            $this->list = func_get_args();
        } else {
            $this->list = array();
        }
    }

    public function inspect() {
        $msg = "Inspecting an ArrayList\n\r";
        $found = print_r( $this->list, true );
        $out = preg_replace( '/Array|\(|\)/', "", $found );
        $msg .= $out;
        Magic::alert( $msg );
    }

    public function add( $el ) {
        array_push( $this->list, $el );
        return $this;
    }

    public function deleteAt( $position ) {
        array_splice( $this->list, $position, 1 );
        return $this;
    }

    /**
     * checks if arraylist has a given element
     * Returns true or false
     * @param string $needle
     * @return boolean 
     */
    public function has( $needle ) {
        $found = false;
        if ( gettype( array_search( $needle, $this->list ) ) == "integer" )
            $found = true;
        return $found;
    }

    /**
     * returns the index of a given element in arraylist
     * if element is not found, method returns false
     * @param type $needle
     * @return type 
     */
    public function indexOf( $needle ) {
        if ( !is_string( $needle ) ) {
            $type = gettype( $needle );
            $error = "You called ArrayList::indexOf() with $type, but only string is accepted";
            Magic::alert( $error );
        }
        return array_search( $needle, $this->list );
    }

    /**
     * returns the number of elements in arraylist
     * @return integer the number of elements 
     */
    public function count() {
        return count( $this->list );
    }

    /**
     * get an element out of arraylist by its index
     * @param integer $i the index
     * @return element 
     */
    public function getAt( $i ) {
        if ( $i >= 0 && $i < $this->count() ) {
            return $this->list[$i];
        }
        $l = count( $this->list ) - 1;
        if ( $l > 0 ) {

            $error = "<h1>Magic error alert!</h1> <p>You tried to retrieve index $i
    The higest possible index was $l.<br> 
    Look in your code where you call method ArrayList::getAt(). You must
    supply an integer between 0 and $l. </p>";
        } else {
            $error = "<h1>Magic error alert!</h1>
                    <p>You are trying to get data from an empty Arraylist object. \nArraylist objects must contain data, before you can get data out of them</p>
                  <p>Perhaps you have used the Database::getData() method? \nIf that's the case perhaps your SQL returns an empty data set from the database? </p>";
        }
        Magic::alert( $error );
    }

    public function rewind() {
        $this->index = 0;
        return $this;
    }

    public function next() {
        $this->index++;
        return $this;
    }

    public function hasNext() {
        return ($this->index < count( $this->list ));
    }

    public function valid() {
        return (bool) $this->current();
    }

    public function key() {
        return $this->index;
    }

    public function current() {
        if ( $this->hasNext() ) {
            return $this->list[$this->index];
        }
    }

}

class Object extends stdClass {

    public function __construct( stdClass $obj = null ) {
        if ( $obj != null ) {
            foreach ( $obj as $prop => $val ) {
                $this->$prop = $val;
            }
        }
    }

    public function inspect() {
        $result = print_r( $this, true );
        $out = preg_replace( '/Array|\(|\)/', "", $result );
        Magic::alert( $out );
    }

    public function add( $key, $value ) {
        $this->$key = $value;
        return $this;
    }

    public function has( $key ) {
        return isset( $this->$key );
    }

    public function get( $key ) {
        $out = false;
        if(isset($this->$key)) $out = $this->$key;
        return $out;
    }

}

/* #######################################################
 * 
 * 4) Various utility classes
 * 
  ####################################################### */

class Request extends Object{

    /**
     * create a Request object
     * default method used is http get
     * @param string $method 
     */
    public function __construct( ) {
        foreach( $_REQUEST as $property=>$value){
            $this->$property = $value;
        }
        
    }

    /**
     * clears all controller data to avoid multiple form submissions from users who refreshes browser 
     */
    public function clear() {
        header( "Location:" . $this->url() );
    }
    
    
      /**
     * Returns complete url with protocol and query string 
     * 
     * @return string url
     */
    public function url(){
        $protocol = "http://";
        if ( !empty( $_SERVER['HTTPS'] ) ) {
            $protocol = "https://";
        }
        $url = $protocol . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
        return $url;
    }   

    /**
     * Returns http GET query string
     * 
     * @return string 
     */
    public function queryString() {
        return $_SERVER['QUERY_STRING'];
    }

}

class Uploader {

    private $fileData;
    private $filename;
    private $filetype;
    private $file;
    private $filesize;
    private $destination;
    private $name;
    private $acceptedTypes = array();
    private $typeErrorDetected = false;
    public static $TYPE_ERROR = 99;

    /**
     * Create an uploader object to enable magical file upload. 
     * Pass the name attribute of the relevant file input
     * 
     * @param string $name the name of the file input
     * 
     */
    public function __construct( $name ) {
        if ( isset($_FILES[$name]) == false ){
            $errArr = debug_backtrace();
            $file = basename($errArr[0]["file"]);
            $line = $errArr[0]["line"];
            
            $message = "index $name not found. 
            Possibly you forgot to declare an enctype attribute on the upload form,
            or perhaps no image file was selected when form was submitted.
            ";
            
            magicErrorHandler( E_ERROR, $message, $file, $line);
        }
        $this->name = $name;
        $this->fileData = $_FILES[$name];
        $this->filename = $this->fileData["name"];
        $this->file = $this->fileData["tmp_name"];
        $this->filetype = $this->fileData["type"];
        $this->filesize = $this->fileData["size"];
        $this->restrictFileTypes = false;
    }

    /**
     * Specify a file type that will be accepted for upload. 
     * You can pass multiple, comma-separated mime types in one call.
     * 
     * @param string $type the file type to accept
     * @return Uploader 
     * 
     * Possible image file types could be "image/jpeg", "image/png", "image/gif"
     * You could also use "text/css" or perhaps "application/pdf"
     * You can in fact use any valid mime type
     */
    public function acceptedType() {
        foreach ( func_get_args() as $type ) {
            array_push( $this->acceptedTypes, $type );
        }
        return $this;
    }

    /**
     * Sets a new filename for the uploaded file ie rename the file
     * @param string $name the new filename
     * @return Uploader 
     */
    public function setFilename( $name ) {
        $this->filename = $name;
        return $this;
    }

    public function getFilename() {
        return $this->filename;
    }

    /**
     * Specify which server-side folder to upload new image to
     * @param string $folder: folder name
     * @return Uploader 
     */
    public function uploadTo( $folder ) {
        $this->destination = $folder;
        return $this;
    }

    private function isValidFile() {
        $out = true;
        if ( count( $this->acceptedTypes ) > 0 ) {
            $out = false;
            $thisType = $_FILES[$this->name]['type'];
            if ( in_array( $thisType, $this->acceptedTypes ) ) {
                $out = true;
            }
        }
        return $out;
    }

    /**
     * upload file to server-side folder
     * @return boolean 
     */
    public function upload() {
        $out = false;

        if ( $this->isValidFile() ) {
            $newImage = $this->destination . "/" . $this->filename;
            if ( move_uploaded_file( $this->file, $newImage ) ) {
                $out = true;
            }
        } else {
            $this->typeErrorDetected = true;
        }
        return $out;
    }

    public function getFiletype() {
        return $this->filetype;
    }

    public function getFilesize() {
        return $this->filesize;
    }

    /**
     * @todo: create meaningful error messages
     * @return type 
     */
    public function getErrorCode() {
        $error = $_FILES[$this->name]['error'];
        //assume no special cases will take effect
        $errorCode = $error;
        //print_r($_FILES);
        if ( $this->typeErrorDetected ) {

            $errorCode = self::$TYPE_ERROR;
        }
        //if there is a standard error code, use that to overwrite self::$TYPE_ERROR
        if ( $error != UPLOAD_ERR_OK ) {
            $errorCode = $error;
        }
        return $errorCode;
    }

    /**
     * method returns true if no error is found in upload
     * So the method must be called AFTER uploading a file
     * @return boolean 
     */
    public function isUploaded() {
        return ($this->getErrorCode() == UPLOAD_ERR_OK);
    }

    /**
     * TODO I never get access to these error messages since a PHP error is thrown at move_uploaded_file
     * @return string 
     */
    public function getErrorMessage() {
        $message = "File uploaded without errors";
        $errCode = $this->getErrorCode();
        if ( $errCode == UPLOAD_ERR_OK ) {
            $message = "File uploaded without errors";
        } elseif ( $errCode == UPLOAD_ERR_INI_SIZE ) {
            $message = "File size exceeds the max file size defined in php.ini ";
        } elseif ( $errCode == UPLOAD_ERR_FORM_SIZE ) {
            $message = "File size exceeds the MAX_FILE_SIZE defined in form";
        } elseif ( $errCode == UPLOAD_ERR_PARTIAL ) {
            $message = "The file was partially uploaded";
        } elseif ( $errCode == UPLOAD_ERR_NO_FILE ) {
            $message = "No file was uploaded, you probably clicked upload without pointing to a file first";
        } elseif ( $errCode == UPLOAD_ERR_NO_TMP_DIR ) {
            $message = "Missing a temp directory. Check if php.ini defines an upload_tmp_dir";
        } elseif ( $errCode == UPLOAD_ERR_CANT_WRITE ) {
            $message = "PHP was unable to write file to disk. Check destination folder's file permissions. You should enable read & write.";
        } elseif ( $errCode == UPLOAD_ERR_EXTENSION ) {
            $message = "A PHP extension stopped the file upload. It's probably time to consult google and the php forums...";
        } elseif ( $errCode == self::$TYPE_ERROR ) {
            $message = "The supplied filetype is not accepted for upload";
        }
        return $message;
    }

    public function inspect() {
        $out = "<pre>Inspecting Uploader Object\n\r";

        $out .= "\n\rupload to folder:" . $this->destination;
        $out .= "\n\rfilename of file to upload:" . $this->filename;
        $out .= "\n\rtype of file to upload:" . $this->filetype;
        $out .= "\n\rsize of file to upload:" . $this->filesize . " bytes";

        $out .= "\n\rLooking for filedata under name:" . $this->name;
        $out .= "\n\rfiledata found:";
        $data = print_r( $this->fileData, true );
        $out .= preg_replace( '/Array|\(|\)/', "", $data );

        $out .= "</pre>";
        Magic::alert( $out );
    }

}

class Session {

    /**
     * declare a new session variable
     * @param string $key the key of the new session variable
     * @param string $value the value of the new session variable
     */
    public function add( $key, $value = true ) {
        $_SESSION[$key] = $value;
        return $this;
    }

    /**
     * Get the value of a session variable
     * @param string $key the key of the session variable
     * @return string the value of the session variable
     */
    public function get( $key ) {
        if (isset($_SESSION[$key])){
            $out = $_SESSION[$key];
        }else{
            $out = false;
        }
        return $out;
    }

    /**
     * Tests if a session variable exists
     * @param string $key the key of the session variable to test for
     * @return Boolean 
     */
    public function has( $key ) {
        return isset( $_SESSION[$key] );
    }

    /**
     * Deletes one session variable
     * @param string $key the session variable to delete
     */
    public function delete( $key ) {
        unset( $_SESSION[$key] );
        return $this;
    }


    /**
     * inspect all available session variables
     */
    public function inspect() {
        $out = "No session variables found!";
        if ( count( $_SESSION ) > 0 ) {
            $out = "Inspect session variables.\n\rThey are displayed as [key] => value";
            $sessions = print_r( $_SESSION, true );
            $out .= preg_replace( '/Array|\(|\)/', "", $sessions );
        }
        return Magic::alert( $out);
    }
    
    /**
     * Destroy the session which essentially deletes all session variables 
     */
    public function clear(){
        @session_destroy();
        @session_start();
    }

}

class Magic {

    public static function alert( $message ) {
        $out = "<h4 style=\"color:red;font-family:monospace;\">########## MAGIC ALERT #####################</h4><pre>";
        $out .= $message;
        $out .= "</pre><h4 style=\"color:red;font-family:monospace;\">############################################</h4>";
        echo $out;
        die();
    }

    public static function url() {
        $protocol = "http://";
        if ( !empty( $_SERVER['HTTPS'] ) ) {
            $protocol = "https://";
        }
        $url = $protocol . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
        return $url;
    }

}

// ImageFile is a modified version of Simon Jarvis' SimpleImage class
// SimpleImage Author: Simon Jarvis
// SimpleImage Copyright: 2006 Simon Jarvis
// Link: http://www.white-hat-web-design.co.uk
class ImageFile {

    protected $image;
    protected $width;
    protected $height;
    protected $mimetype;
    protected $folder = "";

    /**
     * Read an image file into memory
     * @param string $filename path and filename of an existing image
     */
    function __construct( $filename ) {

        if ( !file_exists( $filename ) ) {
            Magic::alert( "You are trying to create a new image from $filename. But it seems $filename does not exist. Check that you are pointing to the right file in the right folder." );
        }
        // extract image information
        $info = getimagesize( $filename );
        $this->width = $info[0];
        $this->height = $info[1];
        $this->mimetype = $info['mime'];

        if ( $this->mimetype == 'image/jpeg' ) {
            $this->image = imagecreatefromjpeg( $filename );
        } elseif ( $this->mimetype == 'image/gif' ) {
            $this->image = imagecreatefromgif( $filename );
        } elseif ( $this->mimetype == 'image/png' ) {
            $this->image = imagecreatefrompng( $filename );
        }
    }

    /**
     * get the width of the image
     * @return integer the image width
     */
    protected function width() {
        return $this->width;
    }

    /**
     * get the height of the image
     * @return integer the image height 
     */
    protected function height() {
        return $this->height;
    }

    /**
     * sends a header and the entire bitstream of the image to browser 
     */
    public function display() {
        //send header to browser
        header( "Content-type: {$this->mimetype}" );

        //output image
        if ( $this->mimetype == 'image/jpeg' ) {
            imagejpeg( $this->image );
        }
        if ( $this->mimetype == 'image/png' ) {
            imagepng( $this->image );
        }
        if ( $this->mimetype == 'image/gif' ) {
            imagegif( $this->image );
        }
    }

    /**
     * Save a copy of the image
     * @param string $newName the new filename of the image
     * @return \ImageFile 
     */
    public function saveAs( $newName ) {
        if ( $this->folder != "" ) {
            $folder = $this->folder . "/";
        } else {
            $folder = "";
        }
        if ( $this->mimetype == 'image/jpeg' ) {
            imagejpeg( $this->image, "$folder$newName" );
        }
        if ( $this->mimetype == 'image/png' ) {
            imagepng( $this->image, "$folder$newName" );
        }
        if ( $this->mimetype == 'image/gif' ) {
            imagegif( $this->image, "$folder$newName" );
        }

        return $this;
    }

    /**
     * Resize image to any dimension
     * Notice relative image dimensions can be distorted through this method
     * If you want to scale an image and retain relative dimension, try using other methods
     * such as scaleToWidth() or scaleToHeight()
     * 
     * @param integer $width the new width of the resized image
     * @param integer $height the new height of the resized image
     * @return \ImageFile
     * @TODO check transparency less than 100% for png notice line 679
     */
    function resize( $width, $height ) {
        $new_image = imagecreatetruecolor( $width, $height );
        if ( $this->mimetype == 'image/gif' || $this->mimetype == 'image/png' ) {
            $current_transparent = imagecolortransparent( $this->image );
            if ( $current_transparent != -1 ) {
                $transparent_color = imagecolorsforindex( $this->image, $current_transparent );
                $current_transparent = imagecolorallocate( $new_image, $transparent_color['red'], $transparent_color['green'], $transparent_color['blue'] );
                imagefill( $new_image, 0, 0, $current_transparent );
                imagecolortransparent( $new_image, $current_transparent );
            } elseif ( $this->mimetype == 'image/png' ) {
                imagealphablending( $new_image, false );
                $color = imagecolorallocatealpha( $new_image, 0, 0, 0, 127 );
                imagefill( $new_image, 0, 0, $color );
                imagesavealpha( $new_image, true );
            }
        }
        imagecopyresampled( $new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->width, $this->height );
        $this->image = $new_image;
        return $this;
    }

    /**
     * resize image while preserving original relative dimensions
     * @param integer $height the height of the scaled image
     * @return \ImageFile
     */
    public function scaleToHeight( $height ) {
        $factor = $this->height / $height;
        $width = (int) $this->width / $factor;
        $this->resize( $width, $height );
        return $this;
    }

    /**
     * resize image while preserving original relative dimensions
     * @param integer $width the width of the scaled image
     * @return \ImageFile
     */
    public function scaleToWidth( $width ) {
        $factor = $this->width / $width;
        $height = (int) $this->height / $factor;
        $this->resize( $width, $height );
        return $this;
    }

    /**
     * Set which folder to use if saving an image copy
     * @param string $f foldername
     * @return \ImageFile 
     */
    public function folder( $f ) {
        $this->folder = $f;
        return $this;
    }

}

/**
 * Instantiates an object that can help you generate and send e-mail
 */
class Mailer {

    private $to, $replyTo = "noreply@nowhere.net", $message, $subject, $success = false, $headers;

    /**
     * Declare where to send the email 
     * @param string $email the email address
     * @return Mailer
     */
    public function to( $email ) {
        $this->to = $email;
        return $this;
    }

    /**
     * Declare the main text of the email
     * @param string $msg the email message
     * @return Mailer
     */
    public function message( $msg ) {
        $this->message = $msg;
        return $this;
    }

    /**
     * Declare subject text of the email
     * @param type $s the email subject
     * @return Mailer
     */
    public function subject( $s ) {
        $this->subject = $s;
        return $this;
    }

    /**
     * Declare a reply to header
     * @param string $email the email address to use as replyTo header
     * @return Mailer
     */
    public function replyTo( $email ) {
        $this->replyTo = $email;
        return $this;
    }

    /**
     * Tests if mail was accepted for delivery
     * Does not test if mail was actually sent
     * @return Boolean true if acceppted, false if not
     */
    public function wasSent() {
        return $this->success;
    }

    /**
     * send the mail 
     */
    public function send() {
        $this->headers = "MIME-Version: 1.0" . "\r\n";
        $this->headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";
        $this->headers .="From:" . $this->replyTo . "\r\n";
        $this->success = mail( $this->to, $this->subject, $this->message, $this->headers );
    }

    /**
     * inspect Mailer object data
     */
    public function inspect() {
        $message = "Trying to send this:\r\n";
        $message .= "mail(" . $this->to . ", " . $this->subject . ", " . $this->message . ", " . $this->headers . " )";
        Magic::alert( $message );
    }

}

/* #######################################################
 * 
 * 5) MySQL Database connection
 * 
  ####################################################### */

class Database {

    private $error;
    private $db_host = "localhost", $db_user, $db_name, $db_pw, $connected = false, $connection;

    /**
     * Establish a connection to a mysql database
     * @param string $databaseName - the name of the database to connect to 
     */
    public function __construct( $databaseName = "" ) {
        if ( $databaseName != "" ) {
            $this->db_name = $databaseName;
        }
    }

    /**
     * Indicate database username
     * @param string $u - the username
     * @return \Database 
     */
    public function user( $u ) {
        $this->db_user = $u;
        return $this;
    }
    
    /**
     * Indicate database host
     * @param string $h - the host
     * @return \Database 
     */
    public function host( $h ) {
        $this->db_host = $h;
        return $this;
    }
    
    /**
     * Indicate database name
     * @param string $n - the database name
     * @return \Database 
     */
    public function name( $n ) {
        $this->db_name = $n;
        return $this;
    }

    /**
     * Indicate database password
     * @param string $pw - the password
     * @return \Database 
     */
    public function password( $pw ) {
        $this->db_pw = $pw;
        return $this;
    }

    private function isConnected() {
        return ($this->connected == true);
    }

    /**
     * establish a connection to a mysql databaseß
     * @return \Database 
     */
    public function connect() {
        $this->connection = mysqli_connect( $this->db_host, $this->db_user, $this->db_pw, $this->db_name );
        mysqli_query( $this->connection, 'SET CHARACTER SET utf8' );
        $this->connected = true;
        return $this;
    }

    /**
     * get the result of an SQL query as a PHP array
     * @param string $sql the query
     * @return ArrayList
     */
    public function getData( $sql ) {
        if ( $this->isConnected() == false )
            $this->connect();
        $table = mysqli_query( $this->connection, $sql );
        if ( !$table ) {
            $this->error();
        }
        $result = new ArrayList();
        while ($row = mysqli_fetch_object( $table )) {
            $r = new Object( $row );
            $result->add( $r );
        }

        return $result;
    }

    private function error() {
        $this->error = mysqli_error( $this->connection );
        $errorAr = debug_backtrace();
        $path = $errorAr[1]["file"];
        $pathParts = explode( "/", $path );
        $f = array_pop( $pathParts );
        $line = $errorAr[1]["line"];
        magicErrorHandler( E_RECOVERABLE_ERROR, $this->error, $f, $line );
    }

    /**
     * send an SQL query to database
     * @param string $sql the  statement
     */
    public function query( $sql ) {
        $out = true;
        if ( $this->isConnected() == false ) {
            $this->connect();
        }

        $result = mysqli_query( $this->connection, $sql );
        if ( !$result ) {
            $this->error();
        }
        return $out;
    }

    public function getError() {
        return $this->error;
    }

    public function escapeString( $str ) {
        return mysqli_real_escape_string( $this->connection, $str );
    }

    public function getLastId() {
        return mysqli_insert_id( $this->connection );
    }

}

/* #######################################################
 * 
 * 6) code from other developers
 * 
  ####################################################### */

/**
 * @projectDescription
 * CleanOutput
 * 
 * @description Properly indents XML code
 * @author Jon Gjengset <jon@thesquareplanet.com>
 * @version 2.0 ( 2009 )
 * @copyright This script may be used for any non-violent purpose. Please let me know know if you're using it though, as I, as any other developer, love to hear about how others utilize my work =)
 */
class CleanOutput {

    private $cleanedXML = '';
    private $cleanedHash = '';
    private $indentBy = '    ';

    /**
     * Sets the indentation string
     * @param string $str[optional] Indentation string, defaults to four spaces
     */
    public function setIndentation( $str = '    ' ) {
        $this->indentBy = $str;
    }

    /**
     * Properly formats the given XML with proper indentation
     * @return $cleanedXML
     * @param string $xml The XML code to be formatted
     */
    public function clean( $xml ) {
        if ( md5( $xml ) === $this->cleanedHash ) {
            return $this->cleanedXML;
        }

        /**
         * Variables for determining the type of block we're in 
         * 
         * @var $inComment Are we in an HTML/XML comment block?
         * @var $inCode Are we in a code block? ( JavaScript, CSS... )
         * @var $inPreformat Are we in a preformatted block? ( Textarea, pre )
         * @var $inInline Are we inside an inline tag?
         * @var $inSpecial Are we inside doctype, CDATA or similar?
         */
        $inComment = false;
        $inCode = false;
        $inPreformat = false;
        $inInline = false;
        $inSpecial = false;

        /**
         * @var $openedInSpecial Count opened tags in special blocks
         */
        $openedInSpecial = 0;

        /**
         * Various tag categories
         */
        $inlineTags = array("a", "basefont", "bdo", "font",
            "iframe", "map", "param", "q",
            "span", "sub", "sup", "abbr", "acronym",
            "cite", "del", "dfn", "em", "kbd",
            "strong", "var", "b", "big", "i",
            "s", "small", "strike", "tt", "u",
            "span", "title", "img");
        $preformatTags = array('pre', 'textarea', 'code');
        $codeTags = array('script', 'style');

        /**
         * Controls the indentation
         */
        $indentLevel = 0;

        /**
         * Have we just begun a new line?
         */
        $newLine = true;


        /**
         * Tracks newlines in code|preformat|comment
         */
        $startedNewLine = false;

        /**
         * Output variable
         */
        $cleanedXML = '';

        /**
         * Are we in the opening or closing of a tag?
         */
        $inATag = false;

        /**
         * And so it begins.. We now loop through all the characters in our XML
         * Before version 2.0, we used to loop lines and use regular expressions, 
         * but this approach is a lot more versatile
         */
        for ( $char = 0; $char < strlen( $xml ); $char++ ) {
            /**
             * Case: We're not in a comment, and the next characters is an opening bracket
             * Meaning: An XML tag has just been encountered
             * Action: We figure out what kind of tag has been encountered, and act acordingly 
             */
            if ( $xml[$char] === '<' && !$inComment ) { // We're starting or ending a tag
                /**
                 * We find out what kind of tag we have by looping through the following characters
                 * until we encounter a closing bracket or a space. We then take the corresponding
                 * substring.
                 */
                $inTag = 'comment';
                if ( $xml[$char + 1] !== '!' ) {
                    $findFirstSpace = strpos( $xml, ' ', $char );
                    $findFirstEnd = strpos( $xml, '>', $char );
                    if ( $findFirstSpace === false )
                        $inTag = trim( substr( $xml, $char + 1, $findFirstEnd - $char - 1 ), '/' );
                    elseif ( $findFirstEnd === false )
                        $inTag = trim( substr( $xml, $char + 1, $findFirstSpace - $char - 1 ), '/' );
                    else
                        $inTag = trim( substr( $xml, $char + 1, min( $findFirstEnd, $findFirstSpace ) - $char - 1 ), '/' );
                }

                /**
                 * Case: The first character after the opening bracket is a forward slash
                 * Meaning: We have encountered an XML ending tag
                 * Action: See comments for the if-else block
                 */
                if ( $xml[$char + 1] === '/' ) {
                    /**
                     * If the tag is not an inline tag, we decrease the indentation level
                     * Also, if we're not in a preformat block, we also add a newline and indentation
                     * before we add then ending tag. We then set the newline flag so we will get a new line
                     * after the closing tag.
                     */
                    if ( !in_array( $inTag, $inlineTags ) ) {
                        $indentLevel--;
                        if ( !$inPreformat && !($inTag === 'script' && $xml[$char - 1] === '>') ) { // We want empty <script></script> tags on a single line.
                            $cleanedXML .= "\n";
                            $cleanedXML .= $this->getIndentation( $indentLevel, $this->indentBy );
                        }
                        $cleanedXML .= '</' . $inTag . '>';
                        $newLine = true;
                    }
                    /**
                     * If, on the other hand, we are ending an inline tag, we only append the ending tag
                     * to the output, set the inline flag to false and unset the newline flag in case
                     * it has been set.
                     */ else {
                        $cleanedXML .= '</' . $inTag . '>';
                        $inInline = false;
                        $newLine = false;
                    }

                    // If we're in a special block, we decrement the counter for opened tags in the block
                    if ( $inSpecial )
                        $openedInSpecial--;

                    // If we're ending a preformat tag, we unset the preformat flag
                    if ( in_array( $inTag, $preformatTags ) )
                        $inPreformat = false;
                    // If we're ending a code tag, we unset the code flag
                    elseif ( in_array( $inTag, $codeTags ) )
                        $inCode = false;

                    // Since we've already appended the ending tag, we skip that amount of characters from input
                    $char += strlen( '</' . $inTag . '>' ) - 1;
                }
                /**
                 * Case: The first character after the opening bracket is an exclamation mark
                 * Meaning: We're entering a special XML block ( comment, Doctype, CDATA... )
                 * Action: We determine the type of block, and set the appropriate flags.
                 * For special blocks, we also initialize a counter for opened tags in the block
                 * so we know when the block ends ( by looking at when the counter hits zero again )
                 */
                elseif ( $xml[$char + 1] === '!' ) {
                    // Comment
                    if ( $xml[$char + 2] === '-' ) {
                        $inComment = true;
                    }
                    // Other special blocks
                    else {
                        $inSpecial = true;
                        $openedInSpecial = 0;
                        $inATag = true;
                    }
                    $indentLevel++;
                    $newLine = false;
                    $cleanedXML .= $xml[$char];
                }
                /**
                 * Case: The first character is neither a forward slash or an exclamation mark
                 * Meaning: We're opening a new tag
                 * Action: We find the type of tag we're opening, and act acordingly
                 */ else {
                    /**
                     * We only want to add a new line if we're either entering a block
                     * element OR the $newLine flag has been set.
                     * We then unset the newline flag so that we don't get an extra newline.
                     */
                    if ( $newLine || !in_array( $inTag, $inlineTags ) ) {
                        $cleanedXML .= "\n";
                        $cleanedXML .= $this->getIndentation( $indentLevel, $this->indentBy );
                    }
                    $newLine = false;

                    // Set the inline flag if we're opening an inline tag
                    if ( in_array( $inTag, $inlineTags ) ) {
                        $inInline = true;
                    }

                    // If we're opening a preformat tag, we set the appropriate tag
                    if ( in_array( $inTag, $preformatTags ) )
                        $inPreformat = true;
                    // Similarily, we set the code flag if we're opening a code tag
                    elseif ( in_array( $inTag, $codeTags ) )
                        $inCode = true;

                    // We then add the opening bracket to the output and set the "in a tag" flag
                    $cleanedXML .= '<';
                    $inATag = true;

                    /**
                     * We now need to find out if this is a self closing tag or not.
                     * If it is, we shouldn't increase the indent level, and we should also
                     * Append the tag to output directly and bypass the reading of those
                     * since it will make everything easier for us
                     * 
                     * We also don't want to increase the indentlevel if we're in an inline tag
                     */
                    $endOfTag = strpos( $xml, '>', $char );
                    if ( $xml[$endOfTag - 1] !== '/' && !$inInline ) { // Not self closing, and not inline
                        $indentLevel++;
                        // Remember the counter
                        if ( $inSpecial ) {
                            $openedInSpecial++;
                        }
                    } elseif ( $xml[$endOfTag - 1] === '/' ) { // Self closing
                        // We find the complete contents of the tag, output it, increase $char and get on our way
                        $tagContent = substr( $xml, $char + 1, $endOfTag - $char );
                        $cleanedXML .= $tagContent;
                        $char += strlen( $tagContent );

                        // If its a block element, we still want a new line
                        if ( !$inInline ) {
                            $newLine = true;
                        } else {
                            $newLine = false;
                        }

                        // We now need to unset some of the flags since the tag has been closed
                        $inInline = false;
                        $inATag = false;
                    }
                }
            }
            /**
             * Case: We're in a comment block, and we've enountered -->
             * Meaning: The comment is ending
             * Action: Skip characters, unset comment flag, add newline and append to output
             */ elseif ( $inComment && $xml[$char] === '-' && $xml[$char + 1] === '-' && $xml[$char + 2] === '>' ) {
                $char += 3 - 1;
                $indentLevel--;
                $inComment = false;
                $cleanedXML .= '-->';
                $newLine = true;
            }
            /**
             * Case: We've encountered a newline, tab or carriage return whithout any "weird" flags set
             * Meaning: We've hit a special character that should not be included in the output
             * Action: Nothing... That's what you do when you skip something
             */ elseif ( in_array( $xml[$char], array("\t", "\r", "\n") ) && !$inComment && !$inCode && !$inPreformat ) {
                
            }
            /**
             * Case: We've encountered a closing bracket, we're in a special block and the opened tags counter is zero
             * Meaning: We've hit the ending of the special tag
             * Action: Unset the special and in a tag flag, and decrement the indentation counter
             */ elseif ( $xml[$char] === '>' && $inSpecial && $openedInSpecial === 0 ) {
                $inSpecial = false;
                $inATag = false;
                $indentLevel--;
                $cleanedXML .= '>';
            }
            /**
             * Case: The next character is a closing bracket, we're in a tag definition, and we're not in a special block
             * Meaning: We've hit the end of an opening tag
             * Action: Unset the in-a-tag flag, and add newline if appropriate
             */ elseif ( $xml[$char] === '>' && $inATag && !$inSpecial ) {
                $inATag = false;
                $cleanedXML .= '>';
                // We don't want to add a newline if we've just opened an inline tag or are in preformat mode
                if ( $inInline || $inPreformat )
                    $newLine = false;
                else
                    $newLine = true;
            }
            /**
             * Case: We've hit an ordinary character
             * Meaning: Well.. Nothing special...
             * Action: Get the character to output
             */
            else {
                /**
                 * Remember to add a newline if the flag is set.
                 * We don't want to obey the newline flag if the first character on the line is an "empty" character
                 * In that case, we don't do anything, and wait until we hit a non-empty character.
                 */
                if ( $newLine && !in_array( $xml[$char], array("\r", "\n", "\t", " ") ) ) {
                    $cleanedXML .= "\n" . $this->getIndentation( $indentLevel, $this->indentBy );
                    $newLine = false;
                }

                // We're in a code or comment block
                if ( $inCode || $inComment ) {
                    // Inside code and comment blocks, all newlines should cause the next line to be indented
                    if ( $xml[$char] === "\n" ) {
                        $cleanedXML .= "\n" . $this->getIndentation( $indentLevel, $this->indentBy );
                        $startedNewLine = true;
                    }
                    // If we've hit a non-empty character, we print it
                    elseif ( !in_array( $xml[$char], array("\r", "\t", " ") ) ) {
                        $cleanedXML .= $xml[$char];
                        $startedNewLine = false;
                    }
                    // If we've hit a space, and are not at the beginning of a line, we output it as well
                    elseif ( $xml[$char] === " " && !$startedNewLine ) {
                        $cleanedXML .= " ";
                    }
                }
                // We're in a preformatted block, so we jusdt output the character
                elseif ( $inPreformat ) {
                    $cleanedXML .= $xml[$char];
                }
                // If the previously outputted character was not a newline or the last character in the indentation string
                // And the character is not an empty character, we output it
                elseif ( !in_array( $cleanedXML[strlen( $cleanedXML ) - 1], array("\n", $this->indentBy[strlen( $this->indentBy ) - 1]) ) ||
                        !in_array( $xml[$char], array("\r", "\n", "\t", " ") ) ) {
                    $cleanedXML .= $xml[$char];
                }
            }
        }

        $this->cleanedHash = md5( $xml );
        $this->cleanedXML = $xml;

        // Return!
        return $cleanedXML;
    }

    /**
     * Prints a before and after code of the cleaning process in HTML
     * 
     * @return XML for preview of the code before and after cleaning 
     * @param XML $xml THe XML to be cleaned
     * @param bool $return[optional] Should the code be returned or printed?
     */
    public function beforeAfter( $xml, $return = false ) {
        ob_start();
        ?>
        Before:<br />
        <pre style="border:1px solid black;padding:5px 5px 5px 5px;height:40%;width:90%;overflow:scroll;"><?php echo htmlspecialchars( $xml ); ?></pre>
        <br />
        After:<br />
        <pre style="border:1px solid black;padding:5px 5px 5px 5px;height:40%;width:90%;overflow-x:scroll;"><?php htmlspecialchars( $this->clean( $xml ) ); ?></pre>
        <br />
        <?php
        $out = ob_get_clean();
        ob_end_clean();

        if ( $return )
            return $out;
        else
            echo $out;
    }

    /**
     * Returns the cleaned XML with all tags and attributes in lowercase
     * @return XML
     */
    public function lowercaseTags() {
        if ( empty( $this->cleanedXML ) ) {
            trigger_error( "Output has not yet been cleaned", E_USER_NOTICE );
        }

        $this->cleanedXML = preg_replace( "<(\/?)([^<>\s]+)>/Ue", "'<'.'\\1'.lc('\\2').'>'", $this->cleanedXML );
        $this->cleanedXML = preg_replace( "<(\/?)([^<>\s]+)(\s?[^<>]+)>/Ue", "'<'.'\\1'.lc('\\2').'\\3'.'>'", $this->cleanedXML );

        return $this->cleanedXML;
    }

    /**
     * Prints $indentation $indentLevel times
     * @return string indentstring
     * @param int $indentLevel
     * @param string $indentation
     */
    private function getIndentation( $indentLevel, $indentation ) {
        $out = '';
        for ( $i = 0; $i < $indentLevel; $i++ ) {
            $out .= $indentation;
        }
        return $out;
    }

}

##########################################################################
#
#             7 - functions for method chaining          
#
###########################################################################

class Controller extends Request{
    
}

function html( $tag, $content = "" ) { 
   $voidTags = array("area", "base", "br", "col", "command", "embed", "hr", "img", "input", "keygen", "link", "meta", "param", "source", "track", "wbr");
   if( in_array( $tag, $voidTags ) ){
       return new EmptyHtmlElm( $tag );
   }else{
        return new HtmlElm( $tag, $content );
   }
}

function emptyTag( $tag ) {
    return new EmptyHtmlElm( $tag );
}




<?php
//begin class definition
	class Form extends HtmlElm{
		function __construct() {
			parent::__construct( "form" );
		}

		function action( $a ){
			$this->attr( "action", $a );
			return $this;
		}

		function method( $m ){
			$this->attr( "method", $m );
			return $this;

		}

		function enableFileUploads(){
			$this->attr( "enctype", "multipart/form-data" );
			return $this;
		}
	}

	/**
* Create a new Form object
* @param $method, the Form's method (use "post" or "get")
* @param $action, the Form's action (use a valid URL)
*/
	function form( $method, $action ){
		$f = new Form();
		$f->method($method)->action($action);
		return $f;
	}

	function label( $content ){
		return new HtmlElm("label", $content);
	}

class Textarea extends HtmlElm{
	function __construct() {
		parent::__construct( "textarea" );
	}
/**
* declare a cols attribute for the <textare> element
* @param integer $c = the number of cols
* @return \Textarea
*/

	function cols( $c ){
		$this->attr( "cols", $c );
		return $this;
	}
/**
* declare a rows attribute for the <textarea> element
* @param integer $r = the number of rows
* @return \Textarea
*/

	function rows( $r ){
		$this->attr( "rows", $r );
		return $this;
	}
/**
* declare a name attribute for the <textarea> element
* @param string $n = the name to use
* @return \Textarea
*/

	function name( $n ){
		$this->attr( "name", $n );
		return $this;
	}
}

	function textarea( $name ){
		$t = new Textarea();
		$t->name( $name );
		return $t;
	}

class Fieldset extends HtmlElm{
	function __construct( $legend ) {
		parent::__construct( "fieldset" );
		if ( $legend != "" ) $this->legend( $legend );
	}
/**
* create a <legend> for the <fieldset>
* @param string $l = the legend value to use
* @return \Fieldset
*/

	function legend( $l ){
		$this->prepend( "<legend>$l</legend>" );
		return $this;
	}
}
/**
* create an object for making <fieldset> elements
* If you pass a string parameter it will be used as <legend>
*
* Examples:
* - fieldset()->asHTML() returns a <fieldset> without a <legend>
* - fieldset("test")->asHTML() returns a <fieldset> with a <legend>
* - fieldset()->legend("test") also returns a <fieldset> with a <legend>
*
* @param string $legend (optional) the legend to use
* @return \Fieldset
*/
function fieldset( $legend = "" ) {
	return new Fieldset( $legend );
}

class Input extends EmptyHtmlElm{
	function __construct( $type = "text" ) {
		parent::__construct( "input" );
		$this->attr("type", $type);
	}

	function name( $n ){
		$this->attr("name", $n);
		return $this;
	}

	function value( $v ){
		$this->attr("value", $v);
		return $this;
	}

}

/**
* create an <input type="text" />
* @return \Input
*/
function input(){
	return new Input();
}
/**
* create an <input type="hidden" />
* @return \Input
*/
function hidden(){
	return new Input( "hidden" );
}
/**
* create an <input type="file" />
* @return \Input
*/
function fileBtn(){
	return new Input( "file" );
}
/**
* create an <input type="radio" />
* @return \Input
*/
	function radioBtn(){
return new Input( "radio" );
}
/**
* create an <input type="checkbox" />
* @return \Input
*/
	function checkBox(){
return new Input( "checkbox" );
}
/**
* create an <input type="password" />
* @return \Input
*/
	function password(){
return new Input( "password" );
}
/**
* create an <input type="submit" />
* @param string $value = the value to use
* @return \Input
*/
function submitBtn( $value ){
	$btn = new Input( "submit" );
return $btn->value( $value );
}
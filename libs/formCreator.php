<?php



class Form extends HtmlElm{

	function __construct(){
			parent::__construct("form");
	}

	function action($a){
		$this->attr("action", $a);
		return $this;
	}

	function method($m){
		$this->attr("method", $m);
		return $this;
	}

	function enableFileUploads(){
		$this->attr("enctype", "multipart/form-data");
		return $this;
	}
}

	function form($method, $action){
		$f =new Form();
		$f->method($method)->action($action);
		return $f;
	}

	function label($content){
		return new HtmlElm("label", $content);
	}


Class Textarea extends HtmlElm{

	function __construct(){
		parent::__construct("textarea");
	}

	function cols($c){
		$this->attr("cols", $c);
		return $this;
	}

	function rows($r){
		$this->attr("rows, $r");
		return $this;
	}

	function name ($n){
		$this->attr("name", $n);
		return $this;
	}


}

	function textarea($name){
		$t = new Textarea();
		$t->name($name);
		return $t;
	}


class Fieldset extends HtmlElm{

	function __construct($legend){
		parent:__construct("fieldset");
		if ($legend !="") $this->legend($legend);
	}

	function legend($l){
		$this->prepend("<legend>$l</legend>");
		return $this;
	}
}

	function fieldset($legend =""){
		return new Fieldset($legend);
	}


class Input extends EmptyHtmlElm{
	
	function __construct($type = "text"){
		parent::__construct("input");
		$this->attr("type", $type);

	}


	function name ($n){
		$this->attr("type", $n);
		return $this;
	}

	function value($v){
		$this->attr("value", $v);
		return $this;
	}	
}


function input($type="text"){
	$elm = new Input($type);
	return $elm;
}

		function hidden(){
		return new Input ("hidden");
	}

		function fileBtn(){
		return new Input ("file");
	}

		function radioBtn(){
		return new Input("radio");
	}

		function checkBox(){
		return new Input("checkbox");
	}


		function password(){
		return new Input("password");
	}

		function submitBtn($value){
		$btn = new Input ("submit");
		return $btn->value($value);
	}

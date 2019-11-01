<?php

class pattern {

    public $content = '';

    public function __construct($filename = '', $array = array(), $return = false) {
        if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/../php/pattern/' . $filename . '.tpl'))
	    $this->content = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/../php/pattern/' . $filename . '.tpl');
    }

    public function set_var($name = '', $value = '') {
	$this->content = str_replace(
	    '{!' . strtoupper($name) . '!}', $value, $this->content
	);
    }

    public function result() {
	return $this->content;
    }

}

?>

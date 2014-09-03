<?php

class Singleton {
	private $props = array(); 
	private static $instance; 

	private function __construct() {}

	public static function get_instance() {
		if (empty ( self::$instance ) ) {
			self::$instance = new Singleton(); 
		}
		return self::$instance; 
	}

	public function __set($key, $val) {
		$this->props[$key] = $val;
	}	

	public function __get($key){
		if (isset($this->props[$key])) return $this->props[$key]; 

		return false; 
	}
}

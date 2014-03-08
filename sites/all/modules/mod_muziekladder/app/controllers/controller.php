<?php
abstract class Controller {
	protected $view; 

	protected function init_view() {
		$callers = debug_backtrace(); 
		$filename = MUZIEK_VIEW_DIR . '/' . strtolower(get_class($this)) . '/' . $callers[1]['function'] . '.xml';
		if (!is_file($filename)){
			throw new Exception ( $filename .' is not a file');
		}
		$this->view = simplexml_load_file($filename, 'SimpleXMLElement', LIBXML_NOCDATA);
	}

	protected function set_head_title($title) {
		$s = Singleton::get_instance();
		$s->head_title = $title;
	}

	protected function set_title($title) {
		$s = Singleton::get_instance();
		$s->title = $title;
	}

	protected function set_meta_desc($content){
		$s = Singleton::get_instance();
		$s->desc_metatag = check_plain($content);
	}

	protected function get_city_menu(){
		$s = Singleton::get_instance();
		$s->city_menu = file_get_contents(MUZIEK_COMPONENTS.'/citymenu.html');
	}
}

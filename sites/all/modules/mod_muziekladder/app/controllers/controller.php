<?php
abstract class Controller {
	protected $view; 
  protected $countrynames = array('Nederland','België' );
  protected $countrynames_EN = array('the Netherlands','Belgium' );

  
  protected function crumbs(Array $items){
    $s = Singleton::get_instance();
    $s->crumbs = theme('crumb_trail',array(
      'items'=>$items
    )); 
  }

	protected function add_body_class($title) {
		$s = Singleton::get_instance();
		$s->body_class = $title;
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

	protected function set_after_content($content){
		$s = Singleton::get_instance();
		$s->after_content = $content; 
	}

	protected function get_city_menu(){
		$s = Singleton::get_instance();
		$s->city_menu = file_get_contents(MUZIEK_COMPONENTS.'/citymenu.html');
	}
	protected function setSession($bin,$data){
		  session_cache_set($bin, $data);
	}

	protected function getSession($bin){
		return  session_cache_get($bin); 
	}
}

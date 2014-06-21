<?php 

class Front extends Controller {
	public function index () {
		//front page 
	
    $this->set_head_title('Muziekladder : agenda voor live muziek, concerten, feesten en optredens in Nederland');
    $this->set_title('Live muziek, concerten, feesten en optredens in heel Nederland');
    $this->set_meta_desc('Agenda, muziek nieuws, en locaties voor live muziek, concerten, feesten en optredens in Nederland');
    $this->get_city_menu();
	}
}






<?php 

class Front extends Controller {
	public function index () {
		//front page 
	
        $this->set_head_title('Muziekladder : agenda voor live muziek, concerten, feesten en optredens');
        $this->set_title('Live muziek, concerten, feesten en optredens');
        $this->set_meta_desc('Agenda, muzieknieuws, en locaties voor live muziek, concerten, feesten en optredens in Nederland en omstreken');
        $this->get_city_menu();
        $this->set_after_content('<div class="waiting"></div>');

	}
}






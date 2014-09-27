<?php 

class Nieuws extends Controller {
	public function ajax () {
       return array('html_fragment' => file_get_contents(MUZIEK_NEWSPORTAL));
	}
}


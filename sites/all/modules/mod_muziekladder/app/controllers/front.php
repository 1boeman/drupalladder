<?php
require 'muziek.php';
class Front extends Controller {
	public function index () {
      global $language;

      $this->set_head_title(t('Muziekladder: calendar for live music, concerts and parties in &amp; around the Netherlands '));
      if ($language->prefix == 'en'){
        $this->set_meta_desc('Calendar, locations and tips for live music, concerts, parties and shows in the Netherlands and surrounding areas.');
        $this->set_title('Music, concerts, parties and festivals');
        $ga_naar = 'Tips archive';

      }else{
        $this->set_meta_desc('Agenda, locaties en tips voor live muziek, concerten, feestjes en shows  in Nederland en omstreken.');
        $this->set_title('Muziek, concerten, feesten en festivals');
        $ga_naar = 'Tips archief';

      }

      $s = Singleton::get_instance();
      $m = new Muziek();
      $s->agenda = ' <a class="naar-agenda-link btn btn-inverse" href="/'.$language->prefix.'/archief">'.$ga_naar.' &raquo;</a>';

      $cities = Muziek_db::get_cities_by_ids(array(1,8,5,1412801590,1413406572,4,7,15,6,17,100,1439757759));
      shuffle($cities);
      $s->city_menu = theme('city_menu',array('cities' => $cities));


   	}
}

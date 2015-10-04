<?php
require 'muziek.php'; 
class Front extends Controller {
	public function index () {
      global $language; 
      	
      $this->set_head_title(t('Muziekladder: calendar for live music, concerts and parties in &amp; around the Netherlands '));
      if ($language->prefix == 'en'){
        $this->set_meta_desc('Calendar, locations and tips for live music, concerts, parties and shows in the Netherlands and surrounding areas.');
        $this->set_title('Music, concerts, parties and festivals');
      }else{
        $this->set_meta_desc('Agenda, locaties en tips voor live muziek, concerten, feestjes en shows  in Nederland en omstreken.');
        $this->set_title('Muziek, concerten, feesten en festivals');
      }

      $s = Singleton::get_instance();
      $ga_naar = t('Open calendar' );
      $m = new Muziek();
      $s->agenda = ' <a class="naar-agenda-link btn btn-inverse" href="/muziek">'.$ga_naar.' &raquo;</a>';
      /**
      $agenda = $m->ajax_agenda();

      $agenda_title = '<a href="/muziek">'.t("Music calendar").'</a>';  
      $s->agenda = '<div id="agenda" class="clearfix"><div id="agenda-front-head"><a class="naar-agenda-link btn btn-inverse" href="/muziek">'.
      $ga_naar.' &raquo;</a><h2>'.$agenda_title. '</h2></div><div id="agenda-front-body">' .$agenda['html_fragment'] 
      . '</div><div class="agenda-front-footer">'.
      ' <a class="naar-agenda-link btn btn-inverse" href="/muziek">'.$ga_naar.' &raquo;</a></div><div></div></div>';
      **/ 
      $cities = Muziek_db::get_cities_by_ids(array(1,8,5,1412801590,1413406572,4,7,15,6,17,100,1439757759));
      shuffle($cities);
      $s->city_menu = theme('city_menu',array('cities' => $cities)); 


      /*
      $list = ''; 

      $s->all_cities;      
      $cities = Muziek_db::get_cities();  
      $list = '<nav><h2>'.t('City calendars').'</h2><ul class="city_agendas">';
      $lang_prefix = Muziek_util::lang_url();  
      
      foreach($cities as $key=>$value){
        $list .= '<li><a href="'.$lang_prefix.'muziek/'.$value['Id'].'-'.$value['Name'].'">'.$value['Name'].'</a></li>';
      } 
      $list .= '<ul></nav>';
      $this->set_after_content($list); 
      */
	}
}


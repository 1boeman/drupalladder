<?php
require 'muziek.php'; 
class Front extends Controller {
	public function index () {
      global $language; 
		//front page 
      	
      $this->set_head_title(t('Muziekladder: calendar for live music, concerts and parties in &amp; around the Netherlands '));
      $this->set_title(t('Music, concerts and parties'));
      $this->set_meta_desc(t('Calendar and loction maps for live music, concerts, parties and shows in the Netherlands and surrounding areas'));

      $s = Singleton::get_instance();
      $ga_naar = t('Open calendar' );
      $m = new Muziek();
      $agenda = $m->ajax_agenda();

      $agenda_title = '<a href="/muziek">'.t("Music calendar").'</a>';  
      $s->agenda = '<div id="agenda" class="clearfix"><div id="agenda-front-head"><a class="naar-agenda-link btn btn-inverse" href="/muziek">'.
      $ga_naar.' &raquo;</a><h2>'.$agenda_title. '</h2></div><div id="agenda-front-body">' .$agenda['html_fragment'] 
     . '</div><div class="agenda-front-footer">'.
     ' <a class="naar-agenda-link btn btn-inverse" href="/muziek">'.$ga_naar.' &raquo;</a></div><div></div></div>';
	}
}


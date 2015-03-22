<?php 
class Front extends Controller {
	public function index () {
		//front page 
	
      $this->set_head_title('Muziekladder : agenda voor live muziek, concerten, feesten en optredens');
      $this->set_title('Live muziek, concerten, feesten en optredens');
      $this->set_meta_desc('Agenda, muzieknieuws, en locaties voor live muziek, concerten, feesten en optredens in Nederland en omstreken');
      $s = Singleton::get_instance();
      $d = new DateTime();  
      
        
      $agenda_title = '<a href="/muziek">Muziek agenda '. t($d->format('l')).'</a>';  
      $s->agenda = '<div id="agenda" class="clearfix"><div id="agenda-front-head"><a class="naar-agenda-link btn btn-inverse" href="/muziek">Ga naar de agenda &raquo;</a><h2>'.$agenda_title. '</h2></div><div id="agenda-front-body">'.$content.'</div><div class="agenda-front-footer"><a class="naar-agenda-link btn btn-inverse" href="/muziek">Ga naar de agenda &raquo;</a></div></div>'; 
	}
}


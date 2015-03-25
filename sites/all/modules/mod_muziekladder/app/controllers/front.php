<?php 
class Front extends Controller {
	public function index () {
		//front page 
	
      $this->set_head_title('Muziekladder : agenda voor live muziek, concerten, feesten en optredens');
      $this->set_title('Live muziek, concerten, feesten en optredens');
      $this->set_meta_desc('Agenda, muzieknieuws, en locaties voor live muziek, concerten, feesten en optredens in Nederland en omstreken');
      $s = Singleton::get_instance();
      $cities = Muziek_db::get_cities();  
      $list = '<nav><ul class="city_agendas">';
      foreach($cities as $key=>$value){
        $list .= '<li><a href="/muziek/'.$value['Id'].'-'.$value['Name'].'">'.$value['Name'].'</a></li>';
        
      } 
      $list .= '<ul></nav>';
      $agenda_title = '<a href="/muziek">Muziekagenda: </a>';  
      $s->agenda = '<div id="agenda" class="clearfix"><div id="agenda-front-head"><a class="naar-agenda-link btn btn-inverse" href="/muziek">Ga naar de agenda &raquo;</a><h2>'.$agenda_title. '</h2></div><div id="agenda-front-body">'
      
     . '</div><div class="agenda-front-footer">'.
     ' <a class="naar-agenda-link btn btn-inverse" href="/muziek">Ga naar de agenda &raquo;</a></div><div></div></div>'.$list;
	}
}


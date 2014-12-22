<?php 
require ('muziek.php');
class Front extends Controller {
	public function index () {
		//front page 
	
      $this->set_head_title('Muziekladder : agenda voor live muziek, concerten, feesten en optredens');
      $this->set_title('Live muziek, concerten, feesten en optredens');
      $this->set_meta_desc('Agenda, muzieknieuws, en locaties voor live muziek, concerten, feesten en optredens in Nederland en omstreken');
      $muziek = new Muziek(); 
      $datefile = $muziek->getDateFile(0);
      $agendafile = $datefile['file']; 
      $xml = simplexml_load_file($agendafile, 'SimpleXMLElement', LIBXML_NOCDATA);
      $this->set_after_content('<div></div>');

      $s = Singleton::get_instance();
      $content =  str_replace('span10','',$xml->content); 
      $content = str_replace('container','',$content);
      $content = str_replace('##controls##','',$content);
      $content = str_replace('row','',$content);
      $agenda_title = explode('-',$xml->title);
      $agenda_title = '<a  href="/muziek">Agenda vandaag - '.$agenda_title[0].'</a>';  
      $s->agenda = '<div id="agenda" class="clearfix"><div id="agenda-front-head"><a class="naar-agenda-link btn btn-inverse" href="/muziek">Ga naar de agenda &raquo;</a><h2>'.$agenda_title. '</h2></div><div id="agenda-front-body">'.$content.'</div><div class="agenda-front-footer"><a class="naar-agenda-link btn btn-inverse" href="/muziek">Ga naar de agenda &raquo;</a></div></div>'; 
	}
}


<?php

class Search extends Controller {
	public function index() {

		$page = isset($_REQUEST['p']) ? $_REQUEST['p'] : 1; 
		$rowsperpage = 10;
		$maxPages = 10;
		$start = '';
		$bodyClass = 'search';
		$pages = 0;
		$content = '';
		$titletag = 'Zoeken op Muziekladder';
		$view = $this->init_view();

		if (isset($_REQUEST['query']) && strlen(trim($_REQUEST['query']))){
			$q = $_REQUEST['query'];	
		}

		if ($page-1){
		  $start = '&start='.($page-1)*$rowsperpage;
		}

		if (!isset($q)){
			$content =   Muziek_util::template(Array(),$this->view->nosearchterm);
			$content .=  Muziek_util::template(Array('searchTerms'=>''),$this->view->searchbox);
		}else{
			$url =  MUZIEK_SOLRHOST.'select?q='.urlencode("{!q.op=AND}text:$q").'&wt=phps';
			$url2 = $url.'&rows='.$rowsperpage ;
			$query = $url2.$start;

			$curl = curl_init();
			curl_setopt_array($curl, array(
						 CURLOPT_RETURNTRANSFER => 1,
						 CURLOPT_URL => $query,
			));
			$resp = curl_exec($curl);
			curl_close($curl);

			$resp = unserialize($resp); 
			$numFound = (int)$resp['response']['numFound']; 
			$pagination = array();

			$content =  Muziek_util::template(Array('searchTerms'=>$q),$this->view->searchbox);
			if ($numFound === 0 ) {
								
			}elseif ($numFound === 1){
				$resultaat = ' resultaat';
			}else{
				$resultaat = ' resultaten';
				
				if ($numFound > $rowsperpage){
					$pages = ceil($numFound / $rowsperpage);
					for ($i=0; $i<$pages; $i++){
						$p = $i+1;
						if ( $p != $page ){
							$link = '/search/?query='.$q.'&p='.$p;
							$pagination []='<li><a href="'.$link.'">'.$p.'</a></li>';
						}else{
							$nextLink = '<li><a href="/search/?query='.$q .'&p='. ($p+1) .'">&raquo;</a></li>';
							$pagination []='<li><span>'.$p.'</span></li>';
							$prevLink ='<li><a href="/search/?query='.$q .'&p='. ($p-1) .'">&laquo;</a></li>';
						}
					}		
				}	
				
				if (count($pagination) > $maxPages){
					$av = $page-ceil($maxPages/2)-1;
					$offset = $av > 0 ? $av : 0;
					$pagination = array_slice($pagination,$offset,$maxPages);
				}	
			}

			if (isset($resultaat)){
				$pagination = implode('',$pagination);
				if ($page > 1){
					$pagination = $prevLink.$pagination;
				}
				if ($page < $pages){
					$pagination.= $nextLink;
				}

				setlocale(LC_TIME, 'nl_NL');

				$content .= Muziek_util::template(Array(
					'numFound'=>$resp['response']['numFound'] .$resultaat,
					'searchTerms'=>$q,
					'pagination'=>$pagination),$this->view->resultheader);
				
					foreach ($resp['response']['docs'] as $doc ) {
						$dsc = $doc['content'] ? $doc['content'] : '';
						
						$content .= Muziek_util::template(Array(
						'title'=>$doc['title'],
						'sourcelink'=>$doc['sourcelink'],
						'desc'=>Muziek_util::shorten($dsc,50).'...',
						'date'=> strftime("%A %e %B %Y", strtotime($doc['date'])),
						'id'=>$doc['id'],
						'location'=>implode(', ',$doc['location']),
						'keywords'=>implode(',',$doc['keywords'])),$this->view->result);
					}

				$content .= Muziek_util::template(Array('pagination'=>$pagination),$this->view->resultfooter);
			}else{
				$content .= Muziek_util::template(Array('searchTerms'=>$q),$this->view->noresults);	
			}	
			$titletag=$q.' - zoekresultaten';
			$this->set_head_title($titletag);
		}
		return Array('html'=>$content); 
	}
}

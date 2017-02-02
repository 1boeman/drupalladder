<?php

class Search extends Controller {
  public function suggest(){
    $rv = array(
      'suggestions' => array()
    );
    $url = MUZIEK_SOLRHOST.'suggest?suggest=true&wt=json&suggest.dictionary=mySuggester&suggest.q=';

    if (isset($_REQUEST['query']) && strlen(trim($_REQUEST['query']))){
      $q = rawurlencode($_REQUEST['query']);  
      $rv['query'] = $q; 
    }
    $query = $url.$q; 

    $resp = $this->curl_get($query);
    $resp = json_decode($resp);
    $result = (array)$resp->suggest->mySuggester;
     
    $suggestions = array_pop($result);
    $terms =(array)$suggestions->suggestions;
    foreach($terms as $term) {
      $rv['suggestions'][]=$term->term;
    }
    drupal_json_output($rv);
    exit;
  }
  
  private function curl_get( $query ){
    $curl = curl_init();
    curl_setopt_array($curl, array (
      CURLOPT_RETURNTRANSFER => 1,
      CURLOPT_URL => $query,
    ));
  
    $resp = curl_exec($curl);
    curl_close($curl);
    return $resp; 
  }

  public function index() {
    $lang_prefix = Muziek_util::lang_url(); 
    
    $page = isset($_REQUEST['p']) ? $_REQUEST['p'] : 1; 
    $rowsperpage = 10;
    $maxPages = 10;
    $start = '';
    $bodyClass = 'search';
    $pages = 0;
    $content = '';
    $titletag = t('Search the Muziekladder Calendar');
    $sort = false;
    $facet_labels = array('city'=>t('City'),'venue_facet'=>t('Location'));

    if (isset($_REQUEST['query']) && strlen(trim($_REQUEST['query']))){
      $q = rawurlencode($_REQUEST['query']);  
    }
       
    if (isset($_REQUEST['orderBy']) && strlen(trim($_REQUEST['orderBy']))){
      $sort = $_REQUEST['orderBy'];
      drupal_add_js(array('muziekladder_search_orderby'=>$sort), 'setting');
    }

    if ($page-1){
      $start = '&start='.($page-1)*$rowsperpage;
    }

    if (!isset($q)){
      $content = theme('searchresult',array(
        'nosearchterm'=>1,
        'searchform'=>1,
        'hideadvanced'=>1,
        'searchTerms'=>''
      ));
    }else{
      $url = MUZIEK_SOLRHOST.'select?q='. $q .'&wt=phps&indent=true&q.op=AND&defType=edismax'; 
      $url .= '&qf=title+content+city+date+venue+venue_facet+zip+sourcelink&stopwords=true&lowercaseOperators=true'; 
      $url .= '&facet=true&facet.sort=count&facet.limit=-1&facet.field=city&facet.field=venue_facet&facet.mincount=1';
      
      if ($sort && $sort !='relevance') {
        if ($sort == 'date') $url .= '&sort=date+asc';
        if ($sort == 'city') $url .= '&sort=city+asc';
        if ($sort == 'venue') $url .= '&sort=venue+asc';
      }

      $active_filters = array();
      $filter_string = '';
      foreach($_GET as $key => $value){
        if (stristr($key,'fq_')){
          $value = str_replace('’','\'', $value); 
          $url .= '&fq='.urlencode($value);
          $active_filters[]=$value;
          $filter_string .= '&'.$key.'='.$value;          
        }
      }

      $url2 = $url.'&rows='.$rowsperpage;
      $query = $url2.$start;

      $resp = $this->curl_get($query);

      global $user;
      if (is_array($user->roles) && in_array('administrator', $user->roles)) {
        drupal_set_message($query);
      }
 
      $resp = unserialize($resp); 
      $numFound = (int)$resp['response']['numFound']; 
      $pagination = array();

      $content =  theme('searchresult',array('searchTerms'=>rawurldecode($q)));
      if ($numFound === 0 ) {
                
      }elseif ($numFound === 1){
        $resultaat = ' '.t('result');
      }else{
        $resultaat = ' '.t('results');
        
        if ($numFound > $rowsperpage){
          $pages = ceil($numFound / $rowsperpage);
                    $sortstring = $sort ? '&orderBy='.$sort : ''; 
          for ($i=0; $i<$pages; $i++){
            $p = $i+1;
            if ( $p != $page ){
              $link = $lang_prefix.'search/?query='.$q.'&p='.$p. $sortstring .$filter_string;
              $pagination []="<li><a href='".$link."'>".$p."</a></li>";
            } else {
              $nextLink = "<li><a href='".$lang_prefix."search/?query=".$q ."&p=". ($p+1) . $sortstring . $filter_string."'>&raquo;</a></li>";
              $pagination []='<li class="selected_search_page"><span>'.$p.'</span></li>';
              $prevLink ="<li><a href='".$lang_prefix."search/?query=".$q ."&p=". ($p-1) . $sortstring . $filter_string."'>&laquo;</a></li>";
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

        $content .= theme('searchresult',Array(
          'searchform'=>1,
          'lang_prefix'=> $lang_prefix,
          'searchTerms'=>rawurldecode($q))); 
 
        $content .= theme('searchresult',Array(
          'resultheader'=>1,
          'query_string' => urldecode($_SERVER['QUERY_STRING']),
          'lang_prefix' => $lang_prefix,
          'facet_labels' => $facet_labels,
          'response_header' => $resp['responseHeader'],
          'numFound'=>$resp['response']['numFound'] .$resultaat,
          'pagination'=>$pagination));
      
        $facets = false; 
        if (isset ($resp['facet_counts'])){
          $facets = true;
          $content .= '<div class="row">'; 
          $content .= theme('searchresult',array(
            'facets_block' => 1,
            'active_filters' => $active_filters,
            'facet_labels' => $facet_labels,
            'query_string' => $_SERVER['QUERY_STRING'],
            'facet_counts' => $resp['facet_counts'],
            'lang_prefix' => $lang_prefix
          ));
        }

        if ($facets) $content .='<div class="span9"><div class="row">';
        foreach ($resp['response']['docs'] as $doc ) {
          $dsc = isset($doc['content']) ? $doc['content'] : '';
          $ts = strtotime($doc['date']);
          $content .= theme('searchresult',array(
              'searchresult'=>1,
              'facets' => $facets,
              'lang_prefix'=>$lang_prefix,
              'title'=>$doc['title'],
              'sourcelink'=>$doc['sourcelink'],
              'desc'=>Muziek_util::shorten($dsc,50),
              'date'=>t(date('l',$ts)).' '.date('j',$ts). ' ' .t(date('F',$ts)).' '.date('Y',$ts), 
              'id'=>$doc['id'],
              'location'=>$doc['venue'],
              'city'=>$doc['city'],
              'internallink'=> 'id='.$doc['id'].'&datestring='.substr($doc['date'],0, strpos($doc['date'],'T')) .'&g='.rawurlencode($doc['sourcelink']),
          ));
        } 
        
        if ($facets) $content.='</div></div></div>';        


        $content .= theme('searchresult',Array(
          'resultfooter'=>1,
          'pagination'=>$pagination));
      } else {
        $content .= theme('searchresult',Array(
          'noresults'=>1,
          'lang_prefix'=> $lang_prefix,
          'searchTerms'=>rawurldecode($q))); 
      } 
      $titletag=$q.' - zoekresultaten';
      $this->set_head_title($titletag);
    }
    return Array('html'=>$content); 
  }
}

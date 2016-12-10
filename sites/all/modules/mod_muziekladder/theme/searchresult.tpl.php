<?php
if (isset($resultheader)): ?>
			
	<div class="row-fluid">
		<div class="span12">
			<div><?php echo $numFound ?></div>
			<div class="pagination">
				<ul>
					<?php echo $pagination ?>
				</ul>	
			</div>
		</div>	
	</div>	
<?php 
// var_dump($response_header['params']); exit;
  if ( isset($response_header['params']['fq'])):
    if (is_string ( $response_header ['params']['fq'])){
      $response_header['params']['fq'] = array($response_header['params']['fq']);
    }
    
 ?>   
      <h4>Filters: </h4>
      <ul> 
      <?php foreach($response_header['params']['fq'] as $filter):
        $filter_array = explode(':',$filter);
       ?>
        <li class="filter label label-info">
          <a class="filter-removal" data-filter='<?php echo $filter ?>' href='#'>&times; <?php echo '<em>'. str_replace('"',' ',$filter_array[1]) ?></em></a>
        </li>
      <?php endforeach;?>
      </ul>
  <?php endif; ?>
<?php endif; ?>

<?php 
if(isset($nosearchterm)): ?>
 		<p>
			<?php echo t('Please fill out one or more search terms') ?>
		</p>
<?php endif; ?>

<?php if(isset($searchform)): ?>
  <div class="form-search-container2">
		<form class="form-search" action="<?php echo $lang_prefix ?>search" id="advanced_search">
			<div class="input-append">
				<input name="query" type="text" class="input-xxlarge autocomplete-search advanced-search" placeholder="<?php echo t('search')?>" value="<?php echo htmlspecialchars($searchTerms) ?>">
				<button type="submit" class="btn"><i class="icon icon-search"></i></button>
            </div>
            <?php if (!isset($hideadvanced)): ?>
            <div class="advanced-search-options">
                <select name="orderBy" id="orderBy">
                    <option value="relevance"><?php echo t('Sort by relevance') ?></option>
                    <option value="date"><?php echo t('Sort by date') ?></option>
               </select>
            </div>
           <?php endif; ?>
		</form>
	</div>

<?php endif; ?>

<?php if (isset($facets_block)): $i=0; ?>
    <div class="facets_block span3">
    <?php foreach($facet_counts['facet_fields'] as $field_name => $values ): $i++; ?>
        <h4><?php echo $facet_labels[$field_name] ?></h4>
        <ul>
        <?php foreach ($values as $key => $value):  ?>
          <li>
            <?php if (!in_array($field_name.':'.'"'.$key.'"',$active_filters)):?>
            <a href='<?php echo $lang_prefix . 'search?' . $query_string .'&p=1' ?>&fq_<?php echo $i?>=<?php echo $field_name .':"' .$key ?>"'><?php echo $key ." ($value)" ?></a>
            <?php else:  ?>
             <span class="active_filter"><em><?php echo $key ." ($value)" ?></em></span> 
            <?php endif; ?>
          </li>
        <?php endforeach ?>
       </ul>
    <?php endforeach ?>
    </div>
<?php endif; ?>


<?php if (isset($searchresult)): 
         $result_url = $lang_prefix .'gig/?'.$internallink;

?>
   <div class="row-fluid search-result-row2">
      <div class="span12">
       <a href="<?php echo $result_url?>"><strong> <?php echo $title ?></strong></a><br>

        <a class="gigdate" href="<?php echo  $result_url ?>"><em><?php echo $date ?></em></a> &bull; 
         <a href="<?php echo $result_url?>"> 
              <span class="location"><strong><?php echo $location ?></strong></span></a> &bull; 
              <span class="city"><?php echo  $city ?></span><br>
                    <?php echo htmlentities(strip_tags($desc)) ?>
          </div>
      </div>
<?php endif; ?>
<?php if (isset($resultfooter)): ?>
	<div class="row-fluid">
		<div class="span12">
			<div class="pagination">
				<ul>
					<?php echo $pagination ?>
				</ul>	
			</div>
		</div>
	</div>
<?php endif; ?>
<?php if (isset($noresults)):
    if (strstr($lang_prefix,'nl')): ?>
		<h1>Helaas</h1>
		<p>Helaas... geen resultaten gevonden voor <em>"<?php echo htmlspecialchars($searchTerms) ?>"</em>.</p>
		<p>Probeer het a.u.b. opnieuw met een andere zoekopdracht.</p>	
  <?php else: ?>
		<h1>Sorry...</h1>
		<p>No results found for <em>"<?php echo htmlspecialchars($searchTerms) ?>"</em>.</p>
		<p>Please try another set of search terms.</p>	
  <?php endif; ?>	
<?php endif; ?>	





<?php 

if(isset($nosearchterm)): ?>
    
 		<p>
			<?php echo t('Please fill out one or more search terms') ?>
		</p>
   

<?php endif; ?>

<?php if(isset($searchTerms)): 

?>

  <div class="form-search-container2">
		<form class="form-search" action="/search" id="advanced_search">
			<div class="input-append">
				<input name="query" type="text" class="input-xxlarge search-query" placeholder="<?php echo t('search')?>" value="<?php echo htmlspecialchars($searchTerms) ?>">
				<button type="submit" class="btn"><i class="icon icon-search"></i></button>
            </div>
            <div class="advanced-search-options">
                <select name="orderBy" id="orderBy">
                    <option value="relevance"><?php echo t('Sort by relevance') ?></option>
                    <option value="date"><?php echo t('Sort by date') ?></option>
               </select>
            </div>
		</form>
	</div>

<?php endif; ?>

<?php if (isset($searchresult)): 
  $lang_prefix = Muziek_util::lang_url(); 

?>
 

      <div class="row-fluid search-result-row2">
          <div class="span2">
              <a class="gigdate" href="<?php echo $lang_prefix ?>gig/?<?php echo $internallink ?>"><?php echo $date ?></a><br>
              <span class="location"><?php echo $location ?></span><br>
              <span class="city"><?php echo  $city ?></span>

          </div>
          <div class="span10">
              <a href="<?php echo $lang_prefix ?>gig/?<?php echo $internallink ?>">
                  <?php echo $title ?>
              </a><br>
              <?php echo $desc ?>
          </div>
      </div>

<?php endif; ?>



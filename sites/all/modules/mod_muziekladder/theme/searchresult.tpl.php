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
				<input name="query" type="text" class="input-xxlarge search-query" placeholder="<?php echo t('search')?>" value="<?php echo htmlspecialchars($searchTerms) ?>">
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

<?php if (isset($searchresult)): 
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





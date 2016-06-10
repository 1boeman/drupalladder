<?php
/**
 * @file
 * Returns the HTML for a single Drupal page.
 *
 * Complete documentation for this file is available online.
 * @see https://drupal.org/node/1728148
 */
?>
<?php require ('header.inc.php') ?>
<div id="page" class="front_dammit">
 
  <div id="main">      
    <div class="column frontheader">
     <?php print $messages; ?>
    </div>
    <div class="front-search">
      <h1 class="page__title title" id="page-title"><?php print $title; ?></h1>

      <form  action="/search">
        <input placeholder="<?php echo t('Search') ?>" id="front-search-input" name="query" type="text" /> 
        <button type="submit" class="btn btn-large">&raquo;</button>
      </form>
    </div>
       <div class="regio_menu-container">
        <h2>Muziekagendas</h2>
        <?php echo $regio_menu ?>
      </div>
    
    <h3><?php echo t('Latest tips by visitors')?></h3>

    <div id="content" class="column" role="main">
      <div class="front-add-event-container">
        <a class="btn btn-inverse front-add-event" href="<?php echo url('muziekformulier')?>">+ <?php echo t('Add event') ?></a>
      </div>
      <?php print render($page['highlighted']); ?>
      <a id="main-content"></a>

      <?php print render($page['help']); ?>
      <?php if ($action_links): ?>
        <ul class="action-links"><?php print render($action_links); ?></ul>
      <?php endif; ?>
      <?php print render($page['content']); ?>
      <?php print $agenda ?>
     <?php if (isset($after_content)): ?>
    <div class="after_content">
        <?php print $after_content ?>
    </div>

    <?php endif; ?>

     <?php print $feed_icons; ?>
      
    </div>

 
    <div class="city_menu-container">
  
      <?php echo $city_menu ?>
    </div>
 
   
  </div>

  <?php print render($page['footer']); ?>

</div>

<?php print render($page['bottom']); ?>

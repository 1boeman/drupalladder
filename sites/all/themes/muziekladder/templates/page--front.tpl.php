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
        <input placeholder="Artiest, stad of locatie" id="front-search-input" name="query" type="text" /> 
        <button type="submit" class="btn btn-large">&raquo;</button>
      </form>
    </div>
   
    <div id="content" class="column" role="main">

      <?php print render($page['highlighted']); ?>
      <?php print $breadcrumb; ?>
      <a id="main-content"></a>

      <?php print render($tabs); ?>
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
      <?php
      // Render the sidebars to see if there's anything in them.
      $sidebar_first  = render($page['sidebar_first']);
      $sidebar_second = render($page['sidebar_second']);
   
      if ($sidebar_first || $sidebar_second): ?>
      <aside class="sidebars">
        <?php print $sidebar_first; ?>
        <?php print $sidebar_second; ?>
      </aside>
    <?php endif; ?>
  </div>

  <?php print render($page['footer']); ?>

</div>

<?php print render($page['bottom']); ?>

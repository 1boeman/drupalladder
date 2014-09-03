<?php
/**
 * @file
 * Returns the HTML for a single Drupal page.
 *
 * Complete documentation for this file is available online.
 * @see https://drupal.org/node/1728148
 */
?>

<div id="page" class="front_dammit">
  <?php require ('header.inc.php') ?>
  
  <div id="main">      
    <div class="column frontheader">
      <?php print render($title_prefix); ?>
      <?php if ($title): ?>
        <h1 class="page__title title" id="page-title"><?php print $title; ?></h1>
      <?php endif; ?>
      <?php print render($title_suffix); ?>
      <?php print $messages; ?>
      <?php if (isset($city_menu)): ?>
        <?php print $city_menu ?>
      <?php endif; ?>
    </div>
    <nav id="frontTabs">
        <ul>
            <li><span>Muziekladder nieuws</span></li>
            <li><span>Nieuws portaal </span></li>
        </ul>
    </nav>
   
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
    <?php if (isset($after_content)): ?>
    <div class="after_content">
        <?php print $after_content; ?>
    </div>
    <?php endif; ?>
  </div>

  <?php print render($page['footer']); ?>

</div>

<?php print render($page['bottom']); ?>

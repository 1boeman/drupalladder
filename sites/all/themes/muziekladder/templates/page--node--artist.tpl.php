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

<div id="page">
  <div id="main">
    <div id="content" class="column" role="main">
      <ul class="breadcrumb">
        <li>
          <a href="<?php echo $path; ?>muziekformulier"><span>Tips</span></a>
          <span class="divider"><i class="icon-chevron-right"></i></span>
        </li>
        <li>
          <a href="<?php echo $path; ?>archief"><span><?php echo t('Archive') ?></span></a>
          <span class="divider"><i class="icon-chevron-right"></i></span>
        </li>
        <li>
          <span class="active"><?php if ($title):
               print $title;
           endif; ?></span>
        </li>
      </ul>

      <div class="artist-container">
        <?php print render($page['highlighted']); ?>
        <?php print $breadcrumb; ?>
        <a id="main-content"></a>
        <?php print render($title_prefix); ?>
        <?php if ($title): ?>
          <h1 class="page__title title" id="page-title"><?php print $title; ?></h1>
        <?php endif; ?>
        <?php print render($title_suffix); ?>
        <?php print $messages; ?>
        <?php print render($tabs); ?>
        <?php print render($page['help']); ?>
        <?php if ($action_links): ?>
          <ul class="action-links"><?php print render($action_links); ?></ul>
        <?php endif; ?>

        <?php include('edit-links.php') ?>

        <?php print render($page['content']); ?>
          
        <?php print $feed_icons; ?>
      </div>
    </div>

    <?php
      // Render the sidebars to see if there's anything in them.
      $sidebar_first  = render($page['sidebar_first']);
      $sidebar_second = render($page['sidebar_second']);
    ?>

    <?php if ($sidebar_first || $sidebar_second): ?>
      <aside class="sidebars">
        <?php print $sidebar_first; ?>
        <?php print $sidebar_second; ?>
      </aside>
    <?php endif; ?>

  </div>

  <?php print render($page['footer']); ?>

</div>

<?php print render($page['bottom']); ?>

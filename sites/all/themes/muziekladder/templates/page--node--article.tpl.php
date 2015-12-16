<?php
/**
 * @file
 * Returns the HTML for a single Drupal page.
 *
 * Complete documentation for this file is available online.
 * @see https://drupal.org/node/1728148
 */

 $path = $base_path;
 $path .= get_lang().'/';

?>

<?php require ('header.inc.php') ?>

<div id="page">
  <div id="main">

    <div id="content" class="column" role="main">
      <div class="article-container">
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
        <?php
         if (isset($editable)): ?>
          <div class="editor-buttons tip-link-container">
            <a class="btn btn-inverse btn-small tip-edit-link" data-handler="EditTip" href="<?php echo $path.'muziekformulier/edit/'.$editable ?>">
              <span>
                <i class="icon icon-edit icon-white"></i>
                &#160;
                <?php echo t('Edit'); ?>
              </span>
            </a>

            <a class="btn btn-inverse btn-small tip-delete-link handleMe" data-handler="nodeDeleteTip" data-xml="<?php echo $path.'muziekformulier/delete/'.$editable ?>" >
              <span>
                <i class="icon icon-remove icon-white"></i>
                 &#160;
                 <?php echo t('Delete'); ?>
              </span>
            </a>
         </div>

        <?php endif; ?>

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

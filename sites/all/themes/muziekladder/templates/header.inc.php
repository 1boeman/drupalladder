  <header class="header" id="header" role="banner">
    <div id="navigation">
    <nav class="brand clearfix">
      <div class="muziekladder_logo"></div>
      <div class="form-search-container">
          <form class="form-search" action="/search">
            <div class="input-append">
                <input name="query" type="text" class="input-medium search-query" placeholder="zoeken">
                <button type="submit" class="btn"><i class="icon icon-search"></i></button>
            </div>
          </form>
      </div>
      <?php print render($page['navigation']); ?>
    </nav>
    </div> 

    <?php if ($secondary_menu): ?>
      <nav class="header__secondary-menu" id="secondary-menu" role="navigation">
        <?php print theme('links__system_secondary_menu', array(
          'links' => $secondary_menu,
          'attributes' => array(
            'class' => array('links', 'inline', 'clearfix'),
          ),
          'heading' => array(
            'text' => $secondary_menu_heading,
            'level' => 'h2',
            'class' => array('element-invisible'),
          ),
        )); ?>
      </nav>
    <?php endif; ?>

    <?php print render($page['header']); ?>

  </header>

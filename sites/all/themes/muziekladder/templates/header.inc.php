  <header class="header" id="header" role="banner">
    <div id="navigation">
    <nav class="brand clearfix">
      <div class="muziekladder_logo"><a href="<?php echo $lang_prefix ?>"><img src="/<?php echo path_to_theme(); ?>/img/logo.png" /></a></div>
      <div class="form-search-container">
          <form class="form-search" action="<?php echo $lang_prefix ?>search">
            <div class="input-append">
                <input name="query" type="text" class="input-medium search-query" placeholder="zoeken">
                <input type="hidden" name="orderBy" value="relevance" />
                <button type="submit" class="btn"><i class="icon icon-search"></i></button>
            </div>
          </form>
      </div>
      <div id="block-system-main-menu" class="block block-system block-menu first last odd" role="navigation">
            <ul class="menu">
              <li class="menu__item is-leaf first leaf menu-385"><a href="<?php echo $lang_prefix ?>muziek/" title="" class="menu__link menu-385">Agenda</a></li>
              <li class="menu__item is-leaf leaf menu-387"><a href="<?php echo $lang_prefix ?>uitgaan/" title="" class="menu__link menu-387"><?php echo t('Locations') ?></a></li>
              <li class="menu__item is-leaf last leaf menu-475"><a href="<?php echo $lang_prefix ?>muziekformulier" title="" class="menu__link menu-475">Tips</a></li>
            </ul>
      </div>

    </nav>
    </div> 

    <?php print render($page['header']); ?>

  </header>

  <header class="header" id="header" role="banner">
    <div id="navigation">
    <nav class="brand clearfix">
     <div class="muziekladder_logo"><a href="<?php echo $lang_prefix ?>"><img src="/<?php echo path_to_theme(); ?>/img/logo.png" /></a></div>
      <div class="muziekladder_logo_small"><a href="<?php echo $lang_prefix ?>"><img src="/<?php echo path_to_theme(); ?>/img/muziekladder.png" /></a>
     
      </div>


      <div class="user-container">
     <?php if ($user->uid > 0 ): ?>
        <a href="<?php echo $lang_prefix ?>user" title=""><?php echo truncate_utf8($user->name,20,TRUE,TRUE) ?></a>
      <?php endif; ?>
      </div>
 


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

              <?php if ($user->uid == 0 ): ?>
                <li class="header_login_link menu__item handleMe" data-handler="presentLogin"><a href="#<?php echo $lang_prefix ?>user/login" title="">Log in</a></li>

              <?php endif; ?> 

              <li class="menu__item is-leaf first leaf menu-385"><a href="<?php echo $lang_prefix ?>muziek/">Agenda</a></li>
              <li class="menu__item is-leaf leaf menu-387"><a href="<?php echo $lang_prefix ?>uitgaan/"><?php echo t('Locations') ?></a></li>
              <li class="menu__item is-leaf last leaf menu-475"><a href="<?php echo $lang_prefix ?>muziekformulier" title="">Tips</a></li>
            </ul>
      </div>

    </nav>
    </div> 

    <?php print render($page['header']); ?>

  </header>


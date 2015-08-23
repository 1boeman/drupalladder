<nav>
  <ul class="breadcrumb">
    <?php foreach ($items as $item) : ?>
      <li>
        <?php if (isset($item['link'])): ?>
          <a href="<?php echo $item['link']?>">
            <span><?php echo $item['text'] ?></span>
          </a>
          <span class="divider"><i class="icon-chevron-right"></i></span>
        <?php else: ?>
          <span class="active"><?php echo $item['text'] ?></span>
        <?php endif; ?>
      </li>
    <?php endforeach; ?>
  </ul>
</nav>

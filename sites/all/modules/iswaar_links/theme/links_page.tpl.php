<div class="row iswaar-links">
  <div class="span12">
    <h1><?php echo t($layout['title']) ?></h1>
  </div>
</div>

<div class="row iswaar-links iswaar-links-container">
<?php 

$d = count($layout['columns']);
$span_size = floor(12/$d);
foreach ($layout['columns'] as $column): ?>
  <div class="span<?php echo $span_size?>">
    <?php foreach ($column as $tag_id):  $i=0; ?>
    <div class="tag_entry">
      <?php foreach($data as $link):
              if($tag_id == $link['tag_id']): 
                $has_description = isset($link['description']) && strlen($link['description']); 
                      if ($i==0):  $i++ ?>
             <h2><?php echo $link['tag']?></h2>
                <?php endif; ?>
                  <ul class="iswaar-link-container">
                    <li>
                      <i class="icon-th-list"></i>
                      <a href="<?php echo $link['url']?>" class="desc-opener"><?php echo $link['title']?></a>
                    </li>
                    <li class="iswaar-link-description">
                      <a target="_blank" href="<?php echo $link['url']?>"> <i class="icon-arrow-right"></i> <?php echo $link['url'] ?></a><br>
                      <?php if($has_description): ?>
                       <?php echo $link['description'] ?><br>
                      <?php endif; ?>
                    </li>
                  </ul>

      <?php   endif;
            endforeach; ?>       
    </div>
    <?php endforeach; ?>

  </div>
<?php endforeach; ?>
</div>


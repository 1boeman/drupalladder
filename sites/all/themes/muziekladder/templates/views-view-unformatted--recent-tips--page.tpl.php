<?php

/**
 * @file
 * Default simple view template to display a list of rows.
 *
 * @ingroup views_templates
 */

$i = 0;
if (!empty($title)): ?>
  <h3><?php print $title; ?></h3>
<?php endif; ?>
  
<?php foreach ($rows as $id => $row):
  if($view->result[$i]->node_uid == $user->uid): ?>
    <a class="btn btn-inverse btn-small tip-edit-link handleMe" data-handler="EditTip" data-nid="<?php echo $view->result[$i]->nid ?>">
      <span>
        <i class="icon icon-edit icon-white"></i>
        &#160;
        <?php echo t('Edit'); ?>
      </span>
    </a>

    <a class="btn btn-inverse btn-small tip-delete-link handleMe" data-handler="DeleteTip" data-nid="<?php echo $view->result[$i]->nid ?>" >
      <span>
        <i class="icon icon-remove icon-white"></i>
         &#160;
         <?php echo t('Delete'); ?>
      </span>
    </a>

  <?php endif?>
  <div<?php if ($classes_array[$id]) { print ' class="' . $classes_array[$id] .'"';  } ?>>
    <?php print $row; ?>
  </div>
<?php
$i++;
endforeach; ?>

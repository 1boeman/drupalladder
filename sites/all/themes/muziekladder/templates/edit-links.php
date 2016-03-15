<?php if (isset($editable)): ?>
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


<?php
/**
 * @file
 * bootstrap-panel.tpl.php
 *
 * Markup for Bootstrap panels ([collapsible] fieldsets).
 */

?>
<?php if ($prefix): ?>
  <?php print $prefix; ?>
<?php endif; ?>
<div<?php print $attributes; ?>>
  <?php if ($title): ?>
    <?php if ($collapsible): ?>
      <a href="#<?php print $id; ?>" class="panel-heading" data-toggle="collapse" data-target="#<?php print $id; ?>">
        <span class="panel-title">
          <?php print $title; ?>
        </span>
      </a>
    <?php else: ?>
      <div class="panel-heading">
        <div class="panel-title">
          <?php print $title; ?>
        </div>
      </div>
    <?php endif; ?>
  <?php endif; ?>
  <?php if ($collapsible): ?>
    <div id="<?php print $id; ?>" class="panel-collapse collapse<?php print (!$collapsed ? ' in' : ''); ?>">
  <?php endif; ?>
  <div class="panel-body">
    <?php if ($description): ?>
      <?php print $description; ?>
    <?php endif; ?>
    <?php print $content; ?>
  </div>
  <?php if ($collapsible): ?>
    </div>
  <?php endif; ?>
</div>
<?php if ($suffix): ?>
  <?php print $suffix; ?>
<?php endif; ?>

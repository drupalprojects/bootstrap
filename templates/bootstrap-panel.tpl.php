<?php
/**
 * @file
 * boostrap-panel.tpl.php
 *
 * Markup for Bootstrap panels ([collapsible] fieldsets).
 * @todo Figure out how to deal with vertical_tab fieldsets.
 */

?>
<div class="panel panel-default">
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
    <?php print $content; ?>
  </div>
  <?php if ($collapsible): ?>
    </div>
  <?php endif; ?>
</div>

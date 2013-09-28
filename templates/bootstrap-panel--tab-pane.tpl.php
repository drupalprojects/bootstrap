<?php
/**
 * @file
 * bootstrap-panel--tab-pane.tpl.php
 *
 * Markup for Bootstrap panels (tab-panes/vertical tabs).
 */

?>
<?php if ($prefix): ?>
  <?php print $prefix; ?>
<?php endif; ?>
<div<?php print $attributes; ?>>
  <?php if ($description): ?>
  <p class="help-block">
    <?php print $description; ?>
  </p>
  <?php endif; ?>
  <?php print $content; ?>
</div>
<?php if ($suffix): ?>
  <?php print $suffix; ?>
<?php endif; ?>

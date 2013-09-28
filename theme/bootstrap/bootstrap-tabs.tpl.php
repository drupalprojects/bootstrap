<?php
/**
 * @file
 * bootstrap-tabs.tpl.php
 *
 * Markup for Bootstrap tabs.
 */
?>
<?php if ($prefix): ?>
  <?php print $prefix; ?>
<?php endif; ?>
<?php if ($tabs && $content): ?>
<div<?php print $attributes; ?>>
  <?php print $tabs; ?>
  <div class="tab-content">
    <?php print $content; ?>
  </div>
</div>
<?php endif; ?>
<?php if ($suffix): ?>
  <?php print $suffix; ?>
<?php endif; ?>

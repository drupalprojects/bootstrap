<?php
/**
 * @file
 * region--content.tpl.php
 *
 * Default theme implementation to display the "content" region.
 *
 * Available variables:
 * - $content: The content for this region, typically blocks.
 * - $attributes: String of attributes that contain things like classes and ids.
 * - $content_attributes: The attributes used to wrap the content. If empty,
 *   the content will not be wrapped.
 * - $region: The name of the region variable as defined in the theme's .info
 *   file.
 * - $page: The page variables from bootstrap_process_page().
 *
 * Helper variables:
 * - $is_admin: Flags true when the current user is an administrator.
 * - $is_front: Flags true when presented in the front page.
 * - $logged_in: Flags true when the current user is a logged-in member.
 *
 * @see bootstrap_preprocess_region().
 * @see bootstrap_process_page().
 *
 * @ingroup themeable
 */
?>
<?php if ($content): ?>
  <div<?php print $attributes; ?>>
    <?php if ($content_attributes): ?><div<?php print $content_attributes; ?>><?php endif; ?>
    <?php print render($page['page']['highlighted']); ?>
    <?php print render($page['breadcrumb']); ?>
    <a id="main-content"></a>
    <?php print render($page['title_prefix']); ?>

    <?php if ($page['title']): ?>
      <h1 class="page-header"><?php print $page['title']; ?></h1>
    <?php endif; ?>

    <?php print render($page['title_suffix']); ?>
    <?php print render($page['messages']); ?>
    <?php print render($page['tabs']); ?>
    <?php print render($page['page']['help']); ?>
    <?php print render($page['action_links']); ?>
    <?php print $content; ?>
    <?php if ($content_attributes): ?></div><?php endif; ?>
  </div>
<?php endif; ?>

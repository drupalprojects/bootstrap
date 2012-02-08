<div class="container">

  <header role="banner">
    <?php if ($logo): ?>
      <a class="brand" href="<?php print $front_page; ?>" title="<?php print t('Home'); ?>">
        <img src="<?php print $logo; ?>" alt="<?php print t('Home'); ?>" />
      </a>
    <?php endif; ?>
    <?php if ($site_name || $site_slogan): ?>
      <hgroup>
        <?php if ($site_name): ?>
          <h1 class="page-header">
            <a href="<?php print $front_page; ?>" title="<?php print t('Home'); ?>"><span><?php print $site_name; ?></span></a>
            <?php if ($site_slogan): ?>
              <small><?php print $site_slogan; ?></small>
            <?php endif; ?>
          </h1>
        <?php endif; ?>
      </hgroup>
    <?php endif; ?>

    <?php print render($page['header']); ?>
    <div id="navbar" class="navbar navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <!-- .btn-navbar is used as the toggle for collapsed navbar content -->
          <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>
        
          <div class="nav-collapse">
            <?php if ($primary_nav): ?>
              <?php print $primary_nav; ?>
            <?php endif; ?>
        
            <?php if ($search): ?>
              <?php if ($search): print render($search); endif; ?>
            <?php endif; ?>
            
            <?php if ($secondary_nav): ?>
              <?php print $secondary_nav; ?>
            <?php endif; ?>
          </div>         
        </div>
      </div>
    </div>
    <?php print render($page['header']); ?>   
  </header> <!-- /#header -->
	
	<div class="row">
	  
    <?php if ($page['sidebar_first']): ?>
      <aside class="span4" role="complementary">
        <?php print render($page['sidebar_first']); ?>
      </aside>  <!-- /#sidebar-first -->
    <?php endif; ?>  
	  
	  <section class="<?php print _twitter_bootstrap_content_span($columns); ?>">  
      <?php if ($page['highlighted']): ?>
        <div id="highlighted hero-unit"><?php print render($page['highlighted']); ?></div>
      <?php endif; ?>
      <?php if ($breadcrumb): print $breadcrumb; endif;?>
      <a id="main-content"></a>
      <?php print render($title_prefix); ?>
      <?php if ($title): ?>
        <h1 class="title" id="page-title"><?php print $title; ?></h1>
      <?php endif; ?>
      <?php print render($title_suffix); ?>
      <?php print $messages; ?>
      <?php if (!empty($tabs['#primary'])): ?>
        <?php print render($tabs); ?>
      <?php endif; ?>
      <?php if ($page['help']): ?> 
        <div class="well"><?php print render($page['help']); ?></div>
      <?php endif; ?>
      <?php if ($action_links): ?>
        <ul class="action-links"><?php print render($action_links); ?></ul>
      <?php endif; ?>
      <?php print render($page['content']); ?>
	  </section>

    <?php if ($page['sidebar_second']): ?>
      <aside class="span4" role="complementary">
        <?php print render($page['sidebar_second']); ?>
      </aside>  <!-- /#sidebar-second -->
    <?php endif; ?>

	</div>
	
	<footer class="footer">
	  <?php print render($page['footer']); ?>
	</footer>
	
  </div>

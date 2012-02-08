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
      <div id="navbar" class="navbar">
        <div class="navbar-inner">
          <div class="container">
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
          
            <?php print render($page['header']); ?>
          </div>
        </div>
      </div>    
    </header> <!-- /#header -->
	
	<div class="row-fluid">
	  
	  <?php print $messages; ?>
	  
	  <?php if ($page['highlight']): ?>
		<div class="highlight hero-unit">
		  <?php print render($page['highlight']); ?>
		</div>
	  <?php endif; ?>
	  
	  <?php if ($breadcrumb): ?>
		<?php print $breadcrumb; ?>
	  <?php endif; ?>
	   
	  <?php if ($page['sidebar_first']): ?>
		<?php print render($page['sidebar_first']); ?>
	  <?php endif; ?>	  
	  
	  <div class="<?php print _twitter_bootstrap_content_span($columns); ?>">
	  
		<?php if ($title): ?>
		<div class="page-header">
		  <h1><?php print $title; ?></h1>
		</div>
		<?php endif; ?>
		
		<?php if ($tabs): ?>
		  <ul class="primary-tabs nav nav-tabs"><?php print render($tabs); ?></ul>
		<?php endif; ?>
	     
		<?php if ($page['help']): ?> 
		  <div class="well">
			<?php print render($page['help']); ?>
		  </div>
		 <?php endif; ?>
		 
		<?php print render($page['content']); ?>
	  </div>
	  
	  <?php if ($page['sidebar_second']): ?>
		<?php print render($page['sidebar_second']); ?>
	  <?php endif; ?>

	</div>
	
	<footer class="footer">
	  <?php print render($page['footer']); ?>
	</footer>
	
  </div>

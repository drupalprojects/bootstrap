  <div class="navbar navbar-fixed-top">
	 <div class="navbar-inner">
	   <div class="container">
		    <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
			  <span class="icon-bar"></span>
			  <span class="icon-bar"></span>
			  <span class="icon-bar"></span>
			</a>
			<a class="brand" href="/">
			  <?php if ($logo): ?>
				<img src="<?php print $logo; ?>" alt="<?php print t('Home'); ?>" />
			  <?php endif; ?>
			  
			  <?php if ($site_name): ?>
				<?php print $site_name; ?>
			  <?php endif; ?>	
			</a>
		  
		  <div class="nav-collapse">
			<?php if ($primary_nav): ?>
			  <?php print $primary_nav; ?>
			<?php endif; ?>
			
			<?php print theme('twitter_bootstrap_navigation', array()); ?>
			
			<?php if ($main_menu): ?>
			  <?php print theme('links__main_menu', array('links' => $main_menu, 'attributes' => array('dropdown' => TRUE, 'id' => 'main-menu', 'class' => array('nav'))));; ?>
			<?php endif; ?>
		  
		  	<?php if ($search): ?>
			  <?php if ($search): print render($search); endif; ?>
			<?php endif; ?>
		  
			<?php if ($secondary_menu): ?>
			  <?php print theme('links__system_main_menu', array('links' => $secondary_menu, 'attributes' => array('id' => 'secondary-menu', 'class' => array('nav', 'pull-right')))); ?>
			<?php endif; ?>
		  </div>
		  
		  <?php print render($page['header']); ?>
	   </div>
	 </div>
  </div>
  
  <div class="container">
	
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
		
		<?php if ($tabs && $tabs['#primary']): ?>
		  <?php print render($tabs); ?>
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

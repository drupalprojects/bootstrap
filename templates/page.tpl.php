  <div class="topbar">
	 <div class="fill">
	   <div class="container">
		  <h3>
			<a class="brand" href="#">
			  <?php if ($site_name): ?>
				<?php print $site_name; ?>
			  <?php endif; ?>	
			  <?php if ($logo): ?>
			   <img src="<?php print $logo; ?> alt="<?php print t('Home'); ?>" />
			  <?php endif; ?> 
			</a>
		  </h3>
		  <?php if ($main_menu || $secondary_menu): ?>
			<?php print theme('links__system_main_menu', array('links' => $main_menu, 'attributes' => array('id' => 'main-menu', 'class' => array('nav')))); ?>
		  <?php endif; ?>
		  
		  <?php print render($page['header']); ?>
	   </div>
	 </div>
  </div>
  
  <?php if ($page['highlight']): ?>
  <div class="highlight">
	<div class="container">
	  <?php print render($page['highlight']); ?>
	</div>
  </div>
  <?php endif; ?>
	
  <div id="content" class="container">
	<?php print $messages; ?>
	
	<?php if ($breadcrumb): ?>
	  <?php print $breadcrumb; ?>
	<?php endif; ?>
     
	<?php if ($title): ?><h1><?php print $title; ?></h1><?php endif; ?>
	
	<?php if ($tabs && $tabs['#primary']): ?>
	  <nav><?php print render($tabs); ?></nav>
	<?php endif; ?>
	 
	<div class="row">
	  <?php if ($page['sidebar_first']): ?>
		<div class="sidebar-first <?php print _twitter_bootstrap_region_span('sidebar_first', $columns); ?>">
		  <?php print render($page['sidebar_first']); ?>
		</div>
	  <?php endif; ?>	  
	  
	  <div class="<?php print _twitter_bootstrap_region_span('content', $columns); ?>">
		<?php print render($page['content']); ?>
	  </div>
	  
	  <?php if ($page['sidebar_second']): ?>
		<div class="sidebar-second <?php print _twitter_bootstrap_region_span('sidebar_second', $columns); ?>">
		  <?php print render($page['sidebar_second']); ?>
		</div>
	  <?php endif; ?>
	</div>

  </div>
  
  <footer class="footer">
	<?php print render($page['footer']); ?>
  </footer>

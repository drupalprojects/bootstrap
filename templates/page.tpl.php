  <div class="topbar">
	 <div class="fill">
	   <div class="container">
		  <h3>
			<a class="brand" href="/">
			  <?php if ($logo): ?>
				<img src="<?php print $logo; ?>" alt="<?php print t('Home'); ?>" />
			  <?php endif; ?>
			  
			  <?php if ($site_name): ?>
				<?php print $site_name; ?>
			  <?php endif; ?>	
			</a>
		  </h3>
		  
		  <?php if ($main_menu || $secondary_menu): ?>
			<?php print theme('links__system_main_menu', array('links' => $main_menu, 'attributes' => array('id' => 'main-menu', 'class' => array('nav')))); ?>
		  <?php endif; ?>
		  
		  <?php if ($search): print render($search); endif; ?>
		  
		  <?php print render($page['header']); ?>
	   </div>
	 </div>
  </div>
  
  <?php if ($page['highlight']): ?>
	<div class="highlight">
	  <div class="container">
		<div class="row">
		  <?php print render($page['highlight']); ?>
		</div>
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
		<?php print render($page['sidebar_first']); ?>
	  <?php endif; ?>	  
	  
	  <div class="<?php print _twitter_bootstrap_content_span($columns); ?>">
		<?php print render($page['content']); ?>
	  </div>
	  
	  <?php if ($page['sidebar_second']): ?>
		<?php print render($page['sidebar_second']); ?>
	  <?php endif; ?>
	</div>

  </div>
  
  <footer class="footer">
	<?php print render($page['footer']); ?>
  </footer>

		<?php if($pagination) : // check to see if pagination is required on this page
			if(!$no_data) : // if there no recorded records ?>
				<div class="under-table">
					<p class="num-rows">
						<?php recordNumber($start_row, $limit_rows, $total_rows); ?>
					</p>
					<div class="pages">
						
						<?php if($page_no > 0) { /* Show if not first page */ ?>
							<a href="<?php printf("%25s?p=%d%s", $this_page, 0, $query_string_page); ?>" class="page" title="Go to the first page">&laquo; First</a>
				
							<a href="<?php printf("%25s?p=%d%s", $this_page, max(0, $page_no - 1), $query_string_page); ?>" class="page" title="Go to the previous page">&lsaquo; Previous</a>
							
							<?php if($page_no - 2 > 0) { ?>
								<a href="<?php printf("%25s?p=%d%s", $this_page, max(0, $page_no - 2), $query_string_page); ?>" class="page"><?php echo $page_no - 2; ?></a>
							<?php } ?>
						
							<?php if($page_no - 1 > 0) { ?>
								<a href="<?php printf("%25s?p=%d%s", $this_page, max(0, $page_no - 1), $query_string_page); ?>" class="page"><?php echo $page_no - 1; ?></a>
							<?php } ?>
						
							<span class="dots">...</span>
						<?php } ?>
						
						<a href="" class="page current" title="Page <?php echo $page_no; ?>"><?php echo $page_no; ?></a>
						
						<?php if($page_no < $total_pages) { /* Show if not last page */ ?>
						
							<?php if($page_no + 1 < $total_pages) { ?>
								<span class="dots">...</span>
								
								<a href="<?php printf("%25s?p=%d%s", $this_page, max(0, $page_no + 1), $query_string_page); ?>" class="page"><?php echo $page_no + 1; ?></a>
							<?php } ?>
							
							<?php if($page_no + 2 < $total_pages) { ?>
								<a href="<?php printf("%25s?p=%d%s", $this_page, max(0, $page_no + 2), $query_string_page); ?>" class="page"><?php echo $page_no + 2; ?></a>
							<?php }?>
							
							<a href="<?php printf("%25s?p=%d%s", $this_page, min($total_pages, $page_no + 1), $query_string_page); ?>" class="page" title="Go to the next page">Next &rsaquo;</a>
				
							<a href="<?php printf("%25s?p=%d%s", $this_page, $total_pages, $query_string_page); ?>" class="page" title="Go to the last page">Last &raquo;</a>
						<?php } ?>
					</div>
					<br class="clear" />
				</div>
			<?php endif; // if there is data
		endif; // end if pagination is on
		?>

		</div><!-- close #content -->
		
		<div id="content-lower">
			&nbsp;
			<a href="#t" title="Go to the top of the page">Top</a>		
		</div>
		
	</div> <!-- close #mc -->

	<div id="footer">
		<p>
			<span class="copy">&copy;<?php echo date("Y"); ?> <a href="http://eire32designs.com" target="_blank">Eire32</a> &amp; <a href="http://jonsdesigns.com" target="_blank">Jon</a> - All rights reserved</span>
			<?php if(loggedIn()) { ?>
			<span class="foot-nav links">
				<a href="<?php echo $path; ?>" title="Home Page">Home</a> -
				<a href="<?php echo $path; ?>sa.php" title="Site Administration">Site Admin</a> -
				<a href="<?php echo $path; ?>me.php" title="Edit your account">My Account</a> -
				<a href="<?php echo $path; ?>actions/logout.php" class="logout" title="Logout">Logout</a>
			</span>
			<?php } ?>
		</p>
	</div>
	
	<!--[if lt IE 7]>
			<script type="text/javascript" src="../js/unitpngfix.js"></script>
	<![endif]--> 
	
	<!-- load jQuery off google CDN -->
	<?php if($https) { ?>
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js"></script>
	<?php } else { ?>
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js"></script>
	<?php } ?>
	
	<?php if($page == 'me') { ?>
		<script src="<?php echo $path; ?>js/me.js" type="text/javascript" charset="utf-8"></script>
		<script src="<?php echo $path; ?>js/password_strength_plugin.js" type="text/javascript" charset="utf-8"></script>
	<?php } ?>
	
	<?php if($page == 'clientdetails') { ?>
		<script src="<?php echo $path; ?>js/cd.js" type="text/javascript" charset="utf-8"></script>
	<?php } ?>
	
	<!-- load main site js -->
	<script src="<?php echo $path; ?>js/site.js" type="text/javascript" charset="utf-8"></script>
	
</body>
</html>
<?php 	
	if($pagination && !$db->error) : // check to see if pagination is required on this page
	if(!$no_data) : // if there no recorded records ?>
		<div class="under-table">
			<p class="num-rows">
				<?php recordNumber($start_row, $limit_rows, $total_rows); ?>
			</p>
			<?php if($total_rows > $limit_rows) : /* If the number of rows returned is not more than the min per page then don't show this section */ ?>
				<div class="pages">
					
			<?php if($page_no > 0) { /* Show if not first page */ ?>
				<a href="<?php printf("%25s?p=%d%s", $this_page, 0, $query_string_page); ?>" class="page" title="Go to the first page">&laquo; First</a>
	
				<a href="<?php printf("%25s?p=%d%s", $this_page, max(0, $page_no - 1), $query_string_page); ?>" class="page" title="Go to the previous page">&lsaquo; Previous</a>
				
				<?php if($page_no - 1 > 0) { ?>
					<a href="<?php printf("%25s?p=%d%s", $this_page, max(0, $page_no - 2), $query_string_page); ?>" class="page"><?php echo $page_no - 1; ?></a>
				<?php } ?>
			
				<?php if($page_no > 0) { ?>
					<a href="<?php printf("%25s?p=%d%s", $this_page, max(0, $page_no - 1), $query_string_page); ?>" class="page"><?php echo $page_no; ?></a>
				<?php } ?>
			
			<?php } ?>
			
			<span class="page current"><?php echo $page_no + 1; ?></span>
			
			<?php if($page_no < $total_pages) { /* Show if not last page */ ?>
			
				<?php if($page_no + 2 < $total_pages) { ?>								
					<a href="<?php printf("%25s?p=%d%s", $this_page, max(0, $page_no + 1), $query_string_page); ?>" class="page"><?php echo $page_no + 2; ?></a>
				<?php } ?>
				
				<?php if($page_no + 3 < $total_pages) { ?>
					<a href="<?php printf("%25s?p=%d%s", $this_page, max(0, $page_no + 2), $query_string_page); ?>" class="page"><?php echo $page_no + 3; ?></a>
				<?php }?>
				
				<a href="<?php printf("%25s?p=%d%s", $this_page, min($total_pages, $page_no + 1), $query_string_page); ?>" class="page" title="Go to the next page">Next &rsaquo;</a>
	
				<a href="<?php printf("%25s?p=%d%s", $this_page, $total_pages, $query_string_page); ?>" class="page" title="Go to the last page">Last &raquo;</a>
				<?php } ?>
				
				</div>
			<?php endif; ?>
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
		<?php if($mem->loggedIn()) { ?>
		<span class="foot-nav links">
			<a href="<?php echo $path; ?>" title="Home Page">Home</a> -
			<a href="<?php echo $path; ?>sa.php" title="Site Administration">Site Admin</a> -
			<a href="<?php echo $path; ?>me.php" title="Edit your account">My Account</a> -
			<a href="<?php echo $path; ?>actions/logout.php" class="logout" title="Logout">Logout</a>
		</span>
		<?php } ?>
	</p>
</div>

</div><!-- close #page-wrap -->

<!--[if lt IE 7]>		
		<script type="text/javascript" src="<?php echo $path; ?>js/unitpngfix.js"></script>
<![endif]--> 

<!-- load jQuery off google CDN -->
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js"></script>

<!-- Load in some jquery plugins -->
<script src="<?php echo PATH; ?>js/jquery.plugins.js" type="text/javascript" charset="utf-8"></script>

<?php if($page == 'me') { ?>
	<script src="js/me.js" type="text/javascript" charset="utf-8"></script>
<?php } ?>

<?php if($page == 'clientdetails') : ?>
	
	<script src="js/jquery.colorbox-min.js" type="text/javascript" charset="utf-8"></script>
	<script src="js/cd.js" type="text/javascript" charset="utf-8"></script>
	<script type="text/javascript">
		$('#level-pw').hide();

		// check for show/hide PW required for level change 
		if ($('#level').val() >= <?php echo $config['cosmos']['pw_req_level_group']; ?>) {
			$("#level-pw").show();
		}
		$('#level').change(function(){
			if ($('#level').val() >= 64) {
				$("#level-pw").slideDown();
			} else {
				$("#level-pw").slideUp();
			}
		});
	</script>
<?php endif; ?>

<!-- load main site js -->
<script src="<?php echo $path; ?>js/site.js" type="text/javascript" charset="utf-8"></script>

<?php $plugins->getJS(); ?>

</body>
</html>
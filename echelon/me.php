<?php
$page = "me";
$page_title = "My Account";
$auth_name = 'login';
require 'inc.php';

require 'inc/header.php';
?>
	<fieldset>
		<legend>Edit My Account</legend>
		<form action="actions/edit-me.php" method="post" id="edit-me">
			<fieldset class="none">
				<legend>Account Info</legend>
				
				<label class="uname">Username:</label>
					<input type="text" name="uname" value="<?php echo $_SESSION['username']; ?>" disabled="disabled" />		
				<label for="display">Display Name:</label><?php tooltip('A name shown to all users, a name used to identify you'); ?>
					<input type="text" name="name" value="<?php echo $mem->name; ?>" id="display" tabindex="1" />
				<label for="email">Email:</label><?php tooltip('A valid email address where Echelon can contact you'); ?>
					<input type="text" name="email" value="<?php echo $mem->email; ?>" id="email" tabindex="2" />
			</fieldset>

			<fieldset class="none">
				<legend>Change your password</legend>
				
				<label for="change-pw">Change your password:</label>
					<input type="checkbox" name="change-pw" id="change-pw" tabindex="3" /><?php tooltip('Do you want to change your Echelon password'); ?><br />
					
				<label for="pass1" class="block">New Password:</label>
					<input type="password" name="pass1" id="pass1" value="" class="disable" disabled="disabled" tabindex="4" />
				<label for="pass2" class="block" style="width: 230px;">New Password Again:</label>
					<input type="password" name="pass2" id="pass2" value="" class="disable" disabled="disabled" tabindex="5" />
			</fieldset>
			
			<br class="clear" />
			
			<fieldset class="none" id="prove">
				<legend>Prove Identiy</legend>
					
				<label for="password">Your Current Password:</label>
					<input type="password" name="password" id="password" value="" tabindex="5" />
			</fieldset>
			
			<br class="clear" />
			
			<input id="edit-me-submit" type="submit" value="Edit Me" class="button">
		</form>
	</fieldset>
	
<?php require 'inc/footer.php'; ?>
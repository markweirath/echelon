<?php
$page = "settings";
$page_title = "Settings";
$auth_name = 'manage_settings';
$b3_conn = true;
require 'inc.php';

// get a list of main Echelon settings from the config table
$settings = $dbl->getSettings('cosmos');

$token_settings = genFormToken('settings');

require 'inc/header.php';
?>

<fieldset>
	<legend>Echelon Settings</legend>
	
	<form action="actions/settings.php" method="post" id="settings-f">
	
		<fieldset class="none">
			<legend>General Echelon Settings</legend>
			
			<label for="name">Site Name:</label><?php tooltip('The name of your site (eg your clanname)'); ?>
				<input type="text" name="name" value="<?php echo $settings['name']; ?>">

			<label for="email">Echelon Admin Email:</label><?php tooltip('Email for the admin of this site'); ?>
				<input type="text" name="email" value="<?php echo $settings['email']; ?>">
				
			<label for="admin_name">Name of Site Admin:</label><?php tooltip('Name of the admin for this site'); ?>
				<input type="text" name="admin_name" value="<?php echo $settings['admin_name']; ?>">

			<input type="text" name="limit_rows" value="<?php echo $settings['limit_rows']; ?>" class="int"><label for="limit_rows">Max rows in tables</label>
				<?php tooltip('Default number of rows that are shown in tables'); ?><br>

		</fieldset>
		
		<fieldset class="none email-msg">
			<legend>Email Messages</legend>
			
			<label for="email_header">Text to start all emails:</label><?php tooltip('All emails sent by Echelon will user this email header.'); ?><br />
				<textarea name="email_header"><?php echo tableClean($settings['email_header']); ?></textarea>
				<br />
				
			<label for="email_footer">Text to end all emails:</label><?php tooltip('This template will be appended to the end of all emails'); ?><br />
				<textarea name="email_footer"><?php echo tableClean($settings['email_footer']); ?></textarea>
				
			<p class="tip">There are some varibles that can be used in the email templates, <strong>%name%</strong> is replaced with the users name, and <strong>%ech_name%</strong> is replaced with the name of the website (eg. your clan name)</p>	
				
		</fieldset>
		
		<br class="clear" />
		
		<fieldset class="none">
			<legend>Time Settings</legend>
			
			<label for="time_format">Format of time:</label><?php tooltip('This time format will be used for almost all times displayed on the website'); ?>
				<input type="text" name="time_format" value="<?php echo tableClean($settings['time_format']); ?>">
				<p class="tip">Time format field is the PHP <a class="external" href="http://php.net/manual/en/function.date.php" title="PHP time format setup">time format</a>.</p>
			
			<label for="time_zone">Time Zone:</label><?php tooltip('Timezone of your game server or web server'); ?>
				<input type="text" name="time_zone" value="<?php echo tableClean($settings['time_zone']); ?>">
				<p class="tip">Timezone field uses PHP <a class="external" href="http://php.net/manual/en/timezones.php" title="PHP time zone lisiting">time zones</a>.</p>
			
		</fieldset>
		
		
		<fieldset class="none">
			<legend>Security Settings</legend>
			
			<input type="text" name="min_pw_len" value="<?php echo $settings['min_pw_len']; ?>" class="int"><label for="min_pw_len">Minimum password length for users</label><?php tooltip('Minimum length for Echelon user passwords'); ?><br>
			<input type="text" name="user_key_expire" value="<?php echo $settings['user_key_expire']; ?>" class="int"><label for="user_key_expire">Days a user reg. key is active</label><?php tooltip('Number of days a registration key will remain valid after the time it was created'); ?><br>
			
			<input type="checkbox" name="https"<?php if($settings['https'] == 1) echo ' checked="checked"'; ?>><label for="https">SSL connection required</label><br>
				<p class="tip">Forces HTTPS, only enable if you have an SSL cert, consult the <a href="http://echelon.bigbrotherbot.com/help/ssl" class="external help-docs">Help Docs</a></p>
			
			<input type="checkbox" name="allow_ie"<?php if($settings['allow_ie'] == 1) echo ' checked="checked"'; ?>><label for="allow_ie">Allow Internet Explorer</label><?php tooltip('If checked, this bans users from using Internet Explorer anywhere but the Public Ban List page'); ?>
			
			<?php if(!$no_games) : ?>
			
			<fieldset class="none">
				<legend>Require password for client level edits</legend>
				
				<input type="checkbox" name="pw_req_level"<?php if($settings['pw_req_level'] == 1) echo ' checked="checked"'; ?>><label for="pw_req_level">Require password</label>
				<select name="pw_req_level_group">
					<?php
						$b3_groups = $db->getB3Groups();
						foreach($b3_groups as $group) :
							$gid = $group['id'];
							$gname = $group['name'];
							if($settings['pw_req_level_group'] == $gid)
								echo '<option value="'.$gid.'" selected="selected">'.$gname.'</option>';
							else
								echo '<option value="'.$gid.'">'.$gname.'</option>';
						endforeach;
					?>
				</select>
				
			</fieldset>
			
			<?php endif; ?>
		
		</fieldset>

		<br class="clear" />
		
		<fieldset class="none">
			<legend>Verify Yourself</legend>
		
			<label for="verify-pw">Your current password:</label><?php tooltip('Please enter your current Echelon user password so that we know that it is really you editing settings'); ?>
				<input type="password" name="password" id="verify-pw" />
		
		</fieldset>
		
		<br class="clear" />
		
		<input type="hidden" name="token" value="<?php echo $token_settings; ?>" />
		<input type="submit" name="settings-sub" value="Save Echelon Settings" />
		
	</form>
		
</fieldset>
	
<?php require 'inc/footer.php'; ?>
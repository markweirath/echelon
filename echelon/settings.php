<?php
set_time_limit(10);
$page = "settings";
$page_title = "Settings";
$auth_name = 'manage_settings';
require 'inc.php';

// get a list of all settings from the config table
$settings = $dbl->getSettings();

require 'inc/header.php';
?>

<fieldset>
	<legend>Settings</legend>
	<form action="#" method="post" id="edit-settings">
	<?php
		$general = '';
		$num_games = $settings[1]['value'];
		$games = array();
		foreach($settings as $setting) :
			$id = $setting['id'];
			$type = $setting['type'];
			$name = $setting['name'];
			$title = $setting['title'];
			$value = $setting['value'];
			$cat = $setting['category'];
			
			
			if($cat == 'cosmos') {
				$result = settingText($name, $title, $value, $type);
				$general .= $result;
			} else {
				$counter = 1;
				while($counter <= $num_games) :
					if($cat == 'game'.$counter) {
						$result = settingText($name, $title, $value, $type);
						$games[$counter] .= $result;	
					}
					$counter++;
				endwhile;
			}
			
		endforeach;
	?>
		<fieldset class="none" id="cosmos-settings">
			<legend>General Echelon Settings</legend>
			<?php echo $general;?>
		</fieldset>
		
		<?php 
			$counter = 1;
			while($counter <= $num_games) :
				echo'<fieldset class="none"><legend>Game '.$counter.'</legend>';
				echo $games[$counter];
				echo '</fieldset>';
				$counter++;
			endwhile;
		?>
		<br class="clear" />
	
		<input type="submit" id="sub-edit-settings" value="Update Settings" />
	</form>
</fieldset>
	
<?php require 'inc/footer.php'; ?>
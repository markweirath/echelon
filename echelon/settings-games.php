<?php
$page = "settings-game";
$page_title = "Games Settings";
$auth_name = 'manage_settings';
require 'inc.php';

if($no_games && $_GET['t'] != 'add')
	send('settings-games.php?t=add');

if($_GET['t'] == 'add') : // if add game type page

	$is_add = true;
	$add_game_token = genFormToken('addgame');

else : // if edit current game settings

	$is_add = false;
	// We are using the game information that was pulled in setup.php
	$game_token = genFormToken('gamesettings');

	if($_GET['w'] == 'game')
		set_warning('You have changed game/DB since the last page!');
		
endif;

require 'inc/header.php';

if($is_add) : ?>

	<a href="settings-games.php" class="float-left">&laquo; Go Back</a>
	<br />

	<form action="actions/settings-game.php" method="post">

	<fieldset>
		<legend>Add a New Game</legend>
			
			<fieldset class="none wide">
				<legend>Names</legend>
				
				<label for="name">Full Name:</label>
					<input type="text" name="name" id="name"  />
				
				<label for="name-short">Short Name:</label>
					<input type="text" name="name-short" id="name-short"  /><br />
					
				<label for="game-type">Game:</label>
					<select name="game-type" id="game-type">
						<?php
							foreach($supported_games as $key => $value) :
								
								echo '<option value="'.$key.'">'.$value.'</option>';

							endforeach;
						?>
					</select>
			
			</fieldset>
			
			<fieldset class="none">
				<legend>B3 DB Information</legend>
			
				<label for="db-host">DB Hostname:</label>
					<input type="text" name="db-host" id="db-host"  />
				
				<label for="db-user">DB User:</label>
					<input type="text" name="db-user" id="db-user" />
								
					<label for="db-pw">DB Password:</label>
						<input type="password" name="db-pw" id="db-pw" />
			
				<label for="db-name">DB Name:</label>
					<input type="text" name="db-name" id="db-name"  />
				
				</fieldset><!-- end DB info -->
			
			<br class="clear" />

	</fieldset><!-- end general game settings -->

		<input type="hidden" name="cng-pw" value="on" />
		<input type="hidden" name="type" value="add" />
		<input type="hidden" name="token" value="<?php echo $add_game_token; ?>" />
		<input type="submit" name="game-settings-sub" value="Add Game" />

	</form>

<?php else: ?>
	
	<span class="float-left">
		<?php
		$this_cur_page = basename($_SERVER['SCRIPT_NAME']);
		$games_list = $dbl->getGamesList();
		$i = 0;
		$count = count($games_list);
		$count--; // minus 1
		while($i <= $count) :
			
			if($game == $games_list[$i]['id']) {
				$selected = 'game-cur';
				$warning_game = NULL;
			} else {
				$selected = NULL;
				$warning_game = '&amp;w=game';
			}
			
			echo '<a href="'.PATH . $this_cur_page .'?game='. $games_list[$i]['id'] . $warning_game .'" title="Switch to this game" class="'. $selected .'">'. $games_list[$i]['name_short'] .'</a>';
			
			if($count != $i)
				echo ' - ';
			
			$i++;
		endwhile;
		?>
	</span>

	
	<a href="settings-games.php?t=add" class="float-right" title="Add a Game (DB) to Echelon">Add Game &raquo;</a>

	<br />
	
	<form action="actions/settings-game.php" method="post">

	<fieldset>
		<legend>Game Settings <?php echo $game_name; ?></legend>
			
			<fieldset class="none wide">
				<legend>Names</legend>
				
				<label for="name">Full Name:</label>
					<input type="text" name="name" id="name" value="<?php echo $game_name; ?>" />
				
				<label for="name-short">Short Name:</label>
					<input type="text" name="name-short" id="name-short" value="<?php echo $game_name_short; ?>" />
			
			</fieldset>
			
			<fieldset class="none">
				<legend>B3 DB Information</legend>
			
				<label for="db-host">Hostname:</label>
					<input type="text" name="db-host" id="db-host" value="<?php echo $game_db_host; ?>" />
				
				<label for="db-user">User:</label>
					<input type="text" name="db-user" id="db-user" value="<?php echo $game_db_user; ?>" />

				<label for="cng-pw">Change DB Password?</label>
					<input type="checkbox" name="cng-pw" id="cng-pw" /><br />
								
				<div id="change-pw-box">
					<label for="db-pw">DB Password:</label>
						<input type="password" name="db-pw" id="db-pw" />
				</div>
			
				<label for="db-name">DB Name:</label>
					<input type="text" name="db-name" id="db-name" value="<?php echo $game_db_name; ?>" />
				
				</fieldset><!-- end DB info -->
				
			<fieldset class="none">
				<legend>Echelon Plugins</legend>
				
				<?php
					$plugins_enabled = $config['game']['plugins'];
				
					foreach(glob(getenv("DOCUMENT_ROOT").PATH.'lib/plugins/*') as $name) :
					
						$name = basename($name);
						
						if(!empty($plugins_enabled)) :
							if(in_array($name, $plugins_enabled))
								$check = 'checked="checked" ';
							else
								$check = '';
						
						else:
							## we need this now because it is not in the inc because of no active plugins
							require_once 'classes/plugins-class.php'; // require the plugins base class
						endif;
						
						$file = getenv("DOCUMENT_ROOT").PATH.'lib/plugins/'.$name.'/class.php'; // abolsute path - needed because this page is include in all levels of this site
						if(file_exists($file)) {
							include_once $file;
							$plugin = call_user_func(array($name, 'getInstance'), 'name');
							$title = $plugin->getTitle();
						} else
							$title = $name;
						
						echo '<input id="'. $name .'" type="checkbox" name="plugins[]" value="'. $name .'" '. $check .'/>
								<label for="'. $name .'">'. $title .'</label><br />';
					endforeach; 
				?>
				
			</fieldset>

			<fieldset class="none">
				<legend>Enable/Disable</legend>

				<input id="enable" type="checkbox" name="enable" value="enable" <?php if($game_active) : ?>checked="checked"<?php endif;?> />
				<label for="enable">Enable/Disable</label><br />

			</fieldset>

			<fieldset class="none">
				<legend>Verify Identity</legend>

				<label for="verify-pw">Your current password:</label>
					<input type="password" name="password" id="verify-pw" />

			</fieldset>
			
			<br class="clear" />

	</fieldset><!-- end general game settings -->
	
		<input type="hidden" name="type" value="edit" />
		<input type="hidden" name="token" value="<?php echo $game_token; ?>" />
		<input type="hidden" name="game" value="<?php echo $game; ?>" />
		<input type="submit" name="game-settings-sub" value="Save Settings" />

	</form>

	<br />

<?php endif;

require 'inc/footer.php'; 
?>
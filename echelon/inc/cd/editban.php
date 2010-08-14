<?php
$auth_name = 'edit_ban';
$b3_conn = true; // this page requries access to the B3 DB
require '../../inc.php';

$pen_id = cleanvar($_GET['banid']);

$query = "SELECT p.reason, p.time_add, p.duration, p.time_expire, c.id, c.name, c.pbid FROM penalties p LEFT JOIN clients c ON p.client_id = c.id WHERE p.id = ?";
$stmt = $db->mysql->prepare($query) or die('DB Error'. $db->mysql->error);
$stmt->bind_param('i', $pen_id);
$stmt->execute();
$stmt->store_result(); // store results (needed to count num_rows)
$num_rows = $stmt->num_rows; // finds the number fo rows retrieved from the database
if($num_rows > 0) {
	$stmt->bind_result($reason, $time_add, $duration, $time_expire, $cid, $name, $pbid);
	$stmt->fetch();
}
?>
<html>
<head>
</head>
<body>

	<fieldset id="edit-ban">
		<legend>Edit this Ban</legend>
		
		<?php if($num_rows > 0) : 
		
			$reason_read = cleanvar(removeColorCode($reason));
			$name = cleanvar($name);
			$time_add_read = date($tformat, $time_add);
			$time_expire_read = timeExpirePen($time_expire);
			
			if($duration != 0)
				$duration_read = time_duration($duration*60, 'yMwdhm');
			else
				$duration_read = 'Permanent';
			
			$token_eb = genFormToken('editban');
		?>
		
		<fieldset class="none eb-fs">
			<legend>Information about the ban</legend>
			<table class="cd-table">
				<tbody>
					<tr>
						<th>Client Name</th>
						<td><?php echo $name; ?></td>

						<th>Time Add</th>
						<td><?php echo $time_add_read; ?></td>
					</tr>
					<tr>
						<th>Duration</th>
						<td><?php echo $duration_read; ?></td>
						
						<th>Time Expires</th>
						<td><?php echo $time_expire_read; ?></td>
					</tr>
				</tbody>
			</table>
		</fieldset>
		
		<form action="actions/b3/editban.php" method="post">
		
			<fieldset class="none eb-fs">
				<legend>Type</legend>
				
				<label for="eb-pb">Permanent Ban?</label>
					<input type="checkbox" name="pb" id="eb-pb" onchange="editBanCheck()" <?php if($duration == 0) echo 'checked="checked" '; ?>/><br />
				
				<div id="eb-ban-duration" <?php if($duration == 0) echo 'style="display: none;"'; ?>>
					<label for="duration">Duration:</label>
						<input type="text" name="duration" id="eb-duration" value="<?php echo $duration; ?>" class="int dur" style="width: 45px !important" />
						
						<select name="time">
							<option value="m">Minutes</option>
							<option value="h">Hours</option>
							<option value="d">Days</option>
							<option value="w">Weeks</option>
							<option value="mn">Months</option>
							<option value="y">Years</option>
						</select>
				</div>
			</fieldset>
			
			<label for="eb-reason">Reason:</label>
				<input type="text" name="reason" id="eb-reason" value="<?php echo $reason_read; ?>" />
			
			<input type="hidden" name="cid" value="<?php echo $cid; ?>" />
			<input type="hidden" name="pbid" value="<?php echo $pbid; ?>" />
			<input type="hidden" name="banid" value="<?php echo $pen_id; ?>" />
			<input type="hidden" name="token" value="<?php echo $token_eb; ?>" />
			<input type="submit" name="eb-sub" value="Edit Ban" />
			
		</form>
		
		<?php else:
			echo '<p>That is not a valid ban id, no information was found on this ban id.</p>';
		endif;
		?>
		
	</fieldset>

</body>
</html>
<?php $stmt->close(); ?>
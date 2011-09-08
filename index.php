<?php

// Initilize App
require_once('init.php');

// Get entryId from GET parameter or Config
$entryId = (isset($_GET['entryId']) ? htmlspecialchars($_GET['entryId']) : $conf['entry_id']);

// html5 library location
$html5Url = 'http://' . $conf['host'] . '/p/' . $conf['partner_id'] ."/sp/". $conf['partner_id'] ."00/embedIframeJs/uiconf_id/". $conf['kdp_uiconf_id'] ."/partner_id/". $conf['partner_id'];

// Create Kdp Url
$kdpUrl = 'http://' . $conf['host'] . '/kwidget/wid/_' . $conf['partner_id'] . '/uiconf_id/' . $conf['kdp_uiconf_id'] . '/sus/ash/entry_id/' . $entryId;

// Create Clipper Url & Flashvars
$clipperUrl = 'http://' . $conf['host'] . '/kgeneric/ui_conf_id//' . $conf['clipper_uiconf_id'];

$clipperFlashvars = '&entry_id=' . $entryId . '&partner_id=' . $conf['partner_id'] . '&host=' . $conf['host'];
$clipperFlashvars .= '&ks=' . $ks . '&show_add_delete_buttons=false&state=clippingState&jsReadyFunc=clipperReady';
$clipperFlashvars .= '&max_allowed_rows=1&show_control_bar=true&show_message_box=false';

if(!$entryId)
	die("Missing entry id");

// Load entry
try
{
	$entry = $client->baseEntry->get($entryId, null);
}
catch(Exception $e)
{
	echo($e->getMessage());
}

if( $conf['overwrite_entry'] ){
	$save_message = $conf['trim_save_message'];
	$form_title = 'Use the trimming timeline below or enter exact in and out times';
} else {
	$save_message = $conf['clip_save_message'];
	$form_title = '<a href="#">Add New Clip</a>';
}

?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php echo $conf['title']; ?></title>
		<meta charset="utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE8" />
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
		<script src="js/jquery.time.stepper.js"></script>
		<script src="js/clipApp.js"></script>
		<script>
		clipApp.init( {
				"config": "<?php echo htmlspecialchars($_GET['config']);?>",
				"host": "<?php echo $conf['host'];?>",
				"partner_id": "<?php echo $conf['partner_id'];?>",
				"entry": <?php echo json_encode($entry);?>,
				"ks": "<?php echo $ks;?>",
				"kdp_uiconf_id": <?php echo $conf['kdp_uiconf_id']; ?>,
				"kclip_uiconf_id": <?php echo $conf['clipper_uiconf_id']; ?>,
				"redirect_save": <?php echo ($conf['redirect_save']) ? 'true' : 'false'; ?>,
				"redirect_url": "<?php echo $conf['redirect_url']; ?>",
				"overwrite_entry": <?php echo ($conf['overwrite_entry']) ? 'true' : 'false'; ?>
		});
		</script>
		<script src="<?php echo $html5Url; ?>"></script>
		<link rel="stylesheet" type="text/css" href="css/style.css" />
	</head>
<!--[if IE 7 ]><body class="ie ie7"><![endif]-->
<!--[if IE 8 ]><body class="ie ie8"><![endif]-->
<!--[if IE 9 ]><body class="ie ie9"><![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--><body><!--<![endif]-->
		<div id="wrapper">
			<object id="kdp3" name="kdp3" type="application/x-shockwave-flash" wmode="window" allowFullScreen="true" allowNetworking="all" allowScriptAccess="always" bgcolor="#000000" resource="<?php echo $kdpUrl; ?>" data="<?php echo $kdpUrl; ?>"><param name="allowFullScreen" value="true" /><param name="allowNetworking" value="all" /><param name="allowScriptAccess" value="always" /><param name="wmode" value="window" /><param name="bgcolor" value="#000000" /><param name="flashVars" value="&steamerType=rtmp" /><param name="movie" value="<?php echo $kdpUrl; ?>" /></object>
			<div id="form" class="form clearfix">
				<div id="newclip"><div class="disable"></div><img id="loader" src="images/loader.gif" alt="Saving..." /><?php echo $form_title; ?></div>
				<div id="embed" class="form clearfix">
					<p><?php echo $save_message; ?></p><br />
					<?php if( $conf['show_embed'] === true ) { ?>
					<div class="item clearfix">
						<label>Embed:</label>
						<input id="embedcode" class="text-field" type="text" value="" />
					</div><br />
					<?php } ?>
				</div>
				<div id="fields">
					<div class="disable"></div>
					<div class="item clearfix">
						<label>Start Time:</label>
						<input id="startTime" value="" />
					</div>
					<div class="item clearfix">
						<label>End Time:</label>
						<input id="endTime" value="" />
					</div>
					<?php if( ! $conf['overwrite_entry'] ): ?>
					<div class="item clearfix">
						<label>Title:</label>
						<input id="entry_title" class="text-field" type="text" value="<?php echo htmlspecialchars($entry->name); ?>" /><br /><br />
					</div>
					<div class="item clearfix">
						<label>Description:</label>
						<textarea id="entry_desc"><?php echo htmlspecialchars($entry->description); ?></textarea><br /><br />
					</div>
					<?php endif; ?>
				</div>
			</div>
			<object id="clipper" name="clipper" type="application/x-shockwave-flash" wmode="window" allowNetworking="all" allowScriptAccess="always" data="<?php echo $clipperUrl; ?>"><param name="allowNetworking" value="all" /><param name="allowScriptAccess" value="always" /><param name="wmode" value="window" /><param name="bgcolor" value="#f8f8f8" /><param name="flashVars" value="<?php echo $clipperFlashvars; ?>" /><param name="movie" value="<?php echo $clipperUrl; ?>" /></object>
			
			<div id="actions" class="clearfix">
				<div class="disable"></div>
				<div class="left clearfix">
					<a href="#" id="setStartTime">Set In</a>
					<a href="#" id="setEndTime">Set Out</a>
				</div>
				<div class="right clearfix">
					<a href="#" id="preview">Preview</a>
					<span class="seperator"> | </span>
					<a href="#" id="delete">Remove</a>
				</div>
			</div>
			<div id="save">
				<div class="disable"></div>
				<a href="#">Save</a>
			</div>
		</div>
	</body>
</html>
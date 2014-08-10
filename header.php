<?php
	session_start();
	include('./settings.php');

	if (in_array('comic', $templates)) {
		$templates[] = 'new_post_form';
	}

	if (in_array('nav', $templates)) {
		$templates[] = 'modal';
		$styles[] = 'modal';
		$scripts[] = 'Modal';

		$templates[] = 'about';
		$templates[] = 'style';
		$templates[] = 'login';
		$templates[] = 'legal';
	}
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title><?php echo setting('site_title'); ?></title>
	<!-- styles -->
	<!-- <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'> -->
	<link rel="stylesheet" href="./css/global.css">
	<?php
		foreach($styles as $style) {
			echo('<link rel="stylesheet" href="./css/' . $style . '.css">');
		}
	?>
	<!-- exports -->
	<script type="text/javascript">
		var Exports = {
			user_id: <?php echo isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0; ?>,
			username: "<?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'ERROR'; ?>",
			comic_id: <?php echo isset($_GET['comic']) ? (int) $_GET['comic'] : 0; ?>
		};
	</script>
	<!-- scripts -->
	<!-- <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script> -->
	<script src="./js/jquery.min.js"></script>
	<script src="./js/handlebars-v1.3.0.js"></script>
	<?php
		foreach($scripts as $script) {
			echo('<script src="./js/' . $script . '.js" type="text/javascript"></script>');
		}
	?>
	<!-- templates -->
	<script type="text/javascript">var templates = {}, partials = {};</script>
	<?php
		foreach($partials as $tpl) {
			echo('<script id="' . $tpl . '-partial" type="text/x-handlebars-template" style="display:none">');
			require_once('./templates/partials/' . $tpl . '.tpl');
			echo('</script>');
			echo('<script type="text/javascript">partials.' . $tpl . ' = Handlebars.registerPartial("' . $tpl . '", $("#' . $tpl . '-partial").html());</script>');
		}
		foreach($templates as $tpl) {
			echo('<script id="' . $tpl . '-template" type="text/x-handlebars-template">');
			require_once('./templates/' . $tpl . '.tpl');
			echo('</script>');
			echo('<script type="text/javascript">templates.' . $tpl . ' = Handlebars.compile($("#' . $tpl . '-template").html());</script>');
		}
	?>
</head>
<body>
	<div class="all">
		<div class="header">
			<h1><a href="."><?php setting('site_title'); ?></a></h1>
			<h2><?php setting('sub_title'); ?></h2>
			<div class="nav"></div>
		</div>

<?php
	if ( ! defined( 'ABSPATH' ) ) exit;
	global $wpdb; global $hook_suffix;
	//VALIDACIÓN DE ENTORNO --------------------------------------------------------------
	$sume_clientes_conf = $wpdb->prefix.'sume_clientes_conf';
	$sql = "SELECT api_key, sandbox FROM ".$sume_clientes_conf.";";
	$Configuracion = $wpdb->get_results($sql, ARRAY_A);
	$sumeclientes = ( $Configuracion[0]['sandbox'] == 1 )?'sume.space':'sumeclientes.net';
	$asset = ('127.0.0.1' == $_SERVER['REMOTE_ADDR'])?'http://localhost/sume_clientes/public/':"https://$sumeclientes/";
?>
<!DOCTYPE html>
<html lang="es">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="robots" content="noindex, nofollow">
		<title> P&aacute;gina web no disponible </title>
		<meta name="description" content="Mercadeo en línea basado en resultados">
		<meta name="keywords" content="Sume Clientes, Sume, Clientes">
		<link rel="shortcut icon" href="<?php echo $asset.'SysWeb/SumeClientes/icon32x32.ico'; ?>" type="image/x-icon">
		<link rel="icon" href="<?php echo $asset.'SysWeb/SumeClientes/icon32x32.ico'; ?>" type="image/x-icon">
		<link rel="icon" type="image/png" sizes="16x16" href="<?php echo $asset.'icon32x32.ico'; ?>">
		<link media="all" type="text/css" rel="stylesheet" href="<?php echo $asset.'WebPro/bootstrap/css/bootstrap.min.css'; ?>">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
		<link href="https://fonts.googleapis.com/css?family=Passion+One" rel="stylesheet"> 
		<link href="https://fonts.googleapis.com/css?family=Sintony" rel="stylesheet"> 
	</head>
	<body style="padding-top: 20px; background: #efefef;">
		<div class="jumbotron vertical-center">
			<div class="container">
				<div class="row">
					<div class="col-xs-12 col-sm-4 col-md-4"></div>
					<div class="col-xs-12 col-sm-4 col-md-4">
						<a href="https://www.sumeclientes.com"> <img src="<?php echo $asset.'SysWeb/SumeClientes/logo-sumeclientes.png'; ?>" class="img-responsive"> </a>
						<h2 class="text-center" style="font-family: 'Sintony', sans-serif; font-size: 40px;"> uuuups... </h2>
						<h1 class="text-center" style="font-family: 'Passion One', cursive; font-size: 66px; color: red;"> Error 404 </h1>
						<h3 class="text-center" style="font-family: 'Sintony', sans-serif; font-size: 26px;"> Esta p&aacute;gina ya no existe o no est&aacute; disponible por el momento. </h3>
						<img src="<?php echo $asset.'SysWeb/SumeClientes/VictorHugo.png'; ?>" class="img-responsive" style="padding-top: 65px;">
					</div>
					<div class="col-xs-12 col-sm-4 col-md-4"></div>
				</div>
			</div>
		</div>
		<script src="<?php echo $asset.'WebPro/js/jquery-2.0.2.min.js'; ?>"></script>
		<script src="<?php echo $asset.'WebPro/bootstrap/js/bootstrap.min.js'; ?>"></script>
		<script src="<?php echo $asset.'iframe-resizer/iframeResizer.contentWindow.min.js'; ?>"></script>
		<script type="text/javascript">
			if (window!= window.top) { // inside iframe
				document.body.style.backgroundColor = 'transparent';
				// or document.body.style.background = 'none';
			}
		</script>
	</body>
</html>
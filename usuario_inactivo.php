<?php
	if ( ! defined( 'ABSPATH' ) ) exit;
	global $hook_suffix;
?>
	<div class="wrap">
		<div class="col-md-12">
			<img class="img img-responsive center-block" style="height: 110px; padding-bottom: 10px;" src="<?php echo plugins_url( 'logo.png', __FILE__ ) ?>">
		</div>
		<div class="col-md-12">
			<div class="block-web">
				<div class="header">
					<h3 class="content-header">
						Integración con la plataforma <strong> Sume Clientes </strong>
						<span style="display: none;"> <?php echo $hook_suffix; ?> </span>
					</h3>
				</div>
				<div class="porlets-content">
					<div class="alert alert-danger" style="font-size: 25px;">
						Parece ser que tu cuenta en Sume Clientes se encuentra inactíva, para poder seguir utilizando el plugin debes reactivar tu cuenta y todo seguirá funcionando con normalidad, en este momento tus clientes verán nuestra página de error 404 en todas las páginas publicadas con nuestro plugin, o si borraste tu API Key dentro de la plataforma debes desinstalar e instalar la API Key nueva.
					</div>
				</div>
			</div>
		</div>
	</div>
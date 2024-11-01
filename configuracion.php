<?php
	if ( ! defined( 'ABSPATH' ) ) exit;
	global $wpdb; global $hook_suffix;
	$sume_clientes_conf = $wpdb->prefix.'sume_clientes_conf';
	$sql = "SELECT api_key, codigo_header, codigo_footer FROM ".$sume_clientes_conf.";";
	$Configuracion = $wpdb->get_results($sql, ARRAY_A);
	$conf = (count($Configuracion)==1)?true:false;
?>
<div class="wrap">
	<div class="col-md-12">
		<img class="img img-responsive center-block" style="height: 110px; padding-bottom: 10px;" src="<?php echo plugins_url('logo.png', __FILE__); ?>"> <?php /* <h2> Sume Clientes </h2> */ ?>
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
				<form class="form-horizontal row-border" method="post" id="frmConfiguracionSumeClientes">
					<div class="form-group">
						<label class="control-label" for="api_key"> API Key de Sume Clientes </label>
						<input class="form-control" id="api_key" name="api_key" type="text" <?php if($conf){ ?> value="<?php echo $Configuracion[0]['api_key']; ?>" <?php } ?> ></input>
						<span id="errapi_key" class="text-danger"></span>
					</div>
					<input type="hidden" name="action" value="sc_guardar_apikey">
					<input type="hidden" name="sandbox" value="0">
					<?php wp_nonce_field('sc_guardar_apikey'); ?>
				</form>
			</div>
			<div class="bottom">
				<button id="btnSubmit" class="btn btn-primary" type="button"> <?php if($conf){ ?> Actualizar <?php }else{ ?> Guardar <?php } ?> </button>
			</div>
		</div>
	</div>
	<?php if($conf){ ?>
	<div class="col-md-12">
		<div class="block-web">
			<div class="header">
				<h3 class="content-header">
					Códigos adicioanes a tu sitio web
					<span style="display: none;"> <?php echo $hook_suffix; ?> </span>
				</h3>
			</div>
			<div class="porlets-content">
				<form class="form-horizontal row-border" method="post" id="frmCodigoAdicional">
					<div class="form-group">
						<label class="control-label" for="codigo_header"> Fragmento de código antes del cierre de la etiqueta &lt;&#x2F;head&gt; </label>
						<textarea id="codigo_header" name="codigo_header" class="form-control" rows="10" style="resize: none;"><?php echo $Configuracion[0]['codigo_header']; ?></textarea>
						<span id="errcodigo_header" class="text-danger"></span>
					</div>
					<div class="form-group">
						<label class="control-label" for="codigo_footer"> Fragmento de código antes del cierre de la etiqueta &lt;&#x2F;body&gt; </label>
						<textarea id="codigo_footer" name="codigo_footer" class="form-control" rows="10" style="resize: none;"><?php echo $Configuracion[0]['codigo_footer']; ?></textarea>
						<span id="errcodigo_footer" class="text-danger"></span>
					</div>
					<input type="hidden" name="action" value="sc_guardar_codigoextra">
					<?php wp_nonce_field('sc_guardar_codigoextra'); ?>
				</form>
			</div>
			<div class="bottom">
				<button id="btnSubmit2" class="btn btn-primary" type="button"> Guardar </button>
			</div>
		</div>
	</div>
	<?php } ?>
</div>
<?php
	add_action('admin_footer', 'sc_guardar_apikey_javascript');
	function sc_guardar_apikey_javascript() {
?>
		<script type="text/javascript">
			jQuery(document).ready(function($) {
				jQuery("#btnSubmit").click(function(){
					jQuery('[id^="err"]').html("");
					var txt = jQuery("#btnSubmit").html();
					jQuery("#btnSubmit").prop("disabled", true).html('<i class="fa fa-spinner fa-spin"></i> Cargando');
					jQuery.post(
						ajaxurl,
						jQuery("#frmConfiguracionSumeClientes").serialize(),
						function(data){
							$("#btnSubmit").prop("disabled", false).html(txt);
							if(data.success){
								new PNotify({ title: "Felicidades", text: data.Mensaje, type: "success" });
								setTimeout(function(){ window.location.reload(); }, 1000);
							}
							else{
								jQuery.each(data.errMensajes, function(key, value){ if(value != ''){ jQuery("#err"+key).html(value); } else{ jQuery("#err"+key).html(""); } });
							}
						},
						"json"
					);
				});
				jQuery("#btnSubmit2").click(function(){
					jQuery('[id^="err"]').html("");
					var txt = jQuery("#btnSubmit2").html();
					jQuery("#btnSubmit2").prop("disabled", true).html('<i class="fa fa-spinner fa-spin"></i> Cargando');
					jQuery.post(
						ajaxurl,
						jQuery("#frmCodigoAdicional").serialize(),
						function(data){
							$("#btnSubmit2").prop("disabled", false).html(txt);
							if(data.success){
								new PNotify({ title: "Felicidades", text: data.Mensaje, type: "success" });
								setTimeout(function(){ window.location.reload(); }, 1000);
							}
							else{
								jQuery.each(data.errMensajes, function(key, value){ if(value != ''){ jQuery("#err"+key).html(value); } else{ jQuery("#err"+key).html(""); } });
							}
						},
						"json"
					);
				});
				jQuery("#frmConfiguracionSumeClientes").submit(function(e){
					e.preventDefault();
					var txt = jQuery("#btnSubmit").html();
					jQuery("#btnSubmit").prop("disabled", true).html('<i class="fa fa-spinner fa-spin"></i> Cargando');
					jQuery('[id^="err"]').html("");
					jQuery.post(
						ajaxurl,
						jQuery("#frmConfiguracionSumeClientes").serialize(),
						function(data){
							$("#btnSubmit").prop("disabled", false).html(txt);
							if(data.success){
								new PNotify({ title: "Felicidades", text: data.Mensaje, type: "success" });
								setTimeout(function(){ window.location.reload(); }, 1000);
							}
							else{
								jQuery.each(data.errMensajes, function(key, value){ if(value != ''){ jQuery("#err"+key).html(value); } else{ jQuery("#err"+key).html(""); } });
							}
						},
						"json"
					);
				});
				jQuery("#frmCodigoAdicional").submit(function(e){
					e.preventDefault();
					var txt = jQuery("#btnSubmit2").html();
					jQuery("#btnSubmit2").prop("disabled", true).html('<i class="fa fa-spinner fa-spin"></i> Cargando');
					jQuery('[id^="err"]').html("");
					jQuery.post(
						ajaxurl,
						jQuery("#frmCodigoAdicional").serialize(),
						function(data){
							$("#btnSubmit2").prop("disabled", false).html(txt);
							if(data.success){
								new PNotify({ title: "Felicidades", text: data.Mensaje, type: "success" });
								setTimeout(function(){ window.location.reload(); }, 1000);
							}
							else{
								jQuery.each(data.errMensajes, function(key, value){ if(value != ''){ jQuery("#err"+key).html(value); } else{ jQuery("#err"+key).html(""); } });
							}
						},
						"json"
					);
				});
			});
		</script>
<?php
	}
?>
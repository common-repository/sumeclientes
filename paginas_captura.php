<?php
	if ( ! defined( 'ABSPATH' ) ) exit;
	global $wpdb; global $hook_suffix;
	//VALIDACIÓN DE ENTORNO --------------------------------------------------------------
	$sume_clientes_conf = $wpdb->prefix.'sume_clientes_conf';
	$sql = "SELECT api_key, sandbox FROM ".$sume_clientes_conf.";";
	$Configuracion = $wpdb->get_results($sql, ARRAY_A);
	$sumeclientes = ( $Configuracion[0]['sandbox'] == 1 )?'sume.space':'sumeclientes.net';
	$conf = (count($Configuracion)==1)?true:false;
	//------------------------------------------------------------------------------------
	if($conf){
		//OBTENIENDO LAS PÁGINAS DE CAPTURAS CREADAS EN LA PLATAFORMA -----------------------------------------------------------------------------------------
		//ESCAPEANDO RUTA PARA COMUNICAR CON API-------------------------------------------------------------------------------------------------
		$ruta = ('127.0.0.1' == $_SERVER['REMOTE_ADDR'])?'http://localhost/sume_clientes/public/WP/Paginas/':"https://$sumeclientes/WP/Paginas/";
		$ruta = $ruta.$Configuracion[0]['api_key'];
		$ruta = esc_url($ruta);
		//---------------------------------------------------------------------------------------------------------------------------------------
		$curl = curl_init();
		curl_setopt_array($curl, array(CURLOPT_RETURNTRANSFER => 1, CURLOPT_URL => $ruta, CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT']));
		$resp = curl_exec($curl);
		curl_close($curl);
		$paginas_sume = json_decode($resp);
		//-----------------------------------------------------------------------------------------------------------------------------------------------------
		//SELECCIÓN DE PÁGINAS PUBLICADAS EN WORDPRESS -----------------------------------------
		$sume_clientes_paginas = $wpdb->prefix.'sume_clientes_paginas';
		$sql = "SELECT plantillasxclientes_id AS id, post_id FROM ".$sume_clientes_paginas.";";
		$PaginasPublicadas = $wpdb->get_results($sql, ARRAY_A);
		$pxcid = array(0); $postsid = array(0);
		foreach($PaginasPublicadas as $PP){ $pxcid[] = $PP['id']; $postsid[] = $PP['post_id']; }
		//--------------------------------------------------------------------------------------
		?>
		<div class="wrap">
			<div class="row">
				<div class="col-md-12">
					<img class="img img-responsive center-block" style="height: 110px; padding-bottom: 10px;" src="<?php echo plugins_url( 'logo.png', __FILE__ ) ?>"> <?php /* <h2> Sume Clientes </h2> */ ?>
				</div>
				<div class="col-md-12">
					<div class="block-web">
						<div class="header">
							<h3 class="content-header">
								Listado de <strong> páginas </strong> creadas y guardadas en la plataforma <strong> Sume Clientes </strong>
								<span style="display: none;"> <?php echo $hook_suffix; ?> </span>
							</h3>
						</div>
						<div class="porlets-content">
							<div class="table-responsive">
								<table class="table table-striped table-bordered table-hover" id="tblPaginas">
									<thead>
										<tr>
											<th class="col-md-4"> Nombre </th>
											<th class="col-md-5"> Ruta de acceso </th>
											<th class="col-md-3"> Acciones </th>
										</tr>
									</thead>
									<tbody>
										<?php
											if(count($paginas_sume) > 0){
												foreach($paginas_sume as $pagina_sume){
													$disabled = true;
													if(in_array($pagina_sume->id, $pxcid)){
														$disabled = false;
														$indice = array_search($pagina_sume->id, $pxcid);
														$post_id = $postsid[$indice];
														$sql = "SELECT guid FROM $wpdb->posts WHERE ID = $post_id;";
														$ruta = $wpdb->get_results($sql, ARRAY_A);
													}
										?>
										<tr>
											<td> <?php echo $pagina_sume->nombre; ?> </td>
											<td>
												<?php if($disabled){ ?>
												<i class="fa fa-warning"></i> Página aún no publicada
												<?php }else{ ?>
												<div class="input-group">
													<input type="text" class="form-control" readonly id="url<?php echo $pagina_sume->id; ?>" value="<?php echo $ruta[0]['guid']; ?>"></input>
													<div class="input-group-btn">
														<button id="btnCopiar<?php echo $pagina_sume->id; ?>" class="btn btn-default btnCopiar" data-clipboard-target="#url<?php echo $pagina_sume->id; ?>">
															<i class="fa fa-copy"></i>
														</button>
													</div>
												</div>
												<?php } ?>
											</td>
											<td>
												<button class="btn btn-primary tooltips" type="button" <?php echo (!$disabled)?'disabled':'onclick="Publicar('.$pagina_sume->id.')"'; ?> data-toggle="tooltip" data-original-title="Publicar">
													<i class="fa fa-globe"></i>
												</button>
												<a class="btn btn-warning tooltips" target="_blank" <?php echo ($disabled)?'href="#" disabled':'href="'.$ruta[0]['guid'].'"'; ?> data-toggle="tooltip" data-original-title="Ver">
													<i class="fa fa-eye"></i>
												</a>
												<button class="btn btn-success tooltips" type="button" <?php echo ($disabled)?'disabled':'onclick="Actualizar('.$pagina_sume->id.')"'; ?> data-toggle="tooltip" data-original-title="Actualizar">
													<i class="fa fa-refresh"></i>
												</button>
												<button class="btn btn-danger tooltips" type="button" <?php echo ($disabled)?'disabled':'onclick="Despublicar('.$pagina_sume->id.')"'; ?> data-toggle="tooltip" data-original-title="Despublicar">
													<i class="fa fa-trash-o"></i>
												</button>
											</td>
										</tr>
											<?php } ?>
										<?php }else{ ?>
										<tr class="danger">
											<td colspan="3" class="text-center"> No tiene públicada ningúna página en la plataforma de Sume Clientes </td>
										</tr>
										<?php } ?>
									</tbody>
								</table>
							</div>
						</div>
						<div class="bottom"></div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
	else{
		?>
		<div class="wrap">
			<div class="col-md-12">
				<div class="block-web">
					<div class="header">
						<h3 class="content-header">
							Listado de <strong> páginas </strong> creadas y guardadas en la plataforma <strong> Sume Clientes </strong>
							<span style="display: none;"> <?php echo $hook_suffix; ?> </span>
						</h3>
					</div>
					<div class="porlets-content">
						<div class="alert alert-danger" role="alert">
							Debe asignar su API Key de la plataforma Sume Clientes para iniciar la publicación de sus páginas, para ello dirígete al menú Sume Clientes seguido de Configuración.
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

?>


<?php
	add_action('admin_footer', 'sc_publicar_pagina_javascript');
	function sc_publicar_pagina_javascript() {
?>
		<script type="text/javascript">
			function Publicar(id){
				var id = id;
				jQuery.post(
					ajaxurl,
					{idP:id, action:"sc_publicar_pagina", _wpnonce:"<?php echo wp_create_nonce('sc_publicar_pagina'); ?>"},
					function(data){
						if(data.success){
							new PNotify({ title: "Felicidades", text: data.Mensaje, type: "success" });
							setTimeout(function(){ window.location.reload(); }, 1000);
						}
						else{
							if(data.Mensaje != ""){ new PNotify({ title: "Error", text: data.Mensaje, type: "error" }); }
						}
					},
					"json"
				);
			}
			function Actualizar(id){
				var id = id;
				jQuery.post(
					ajaxurl,
					{idP:id, action:"sc_actualizar_pagina", _wpnonce:"<?php echo wp_create_nonce('sc_actualizar_pagina'); ?>"},
					function(data){
						if(data.success){
							new PNotify({ title: "Felicidades", text: data.Mensaje, type: "success" });
						}
						else{
							if(data.Mensaje != ""){ new PNotify({ title: "Error", text: data.Mensaje, type: "error" }); }
						}
					},
					"json"
				);
			}
			function Despublicar(id){
				var id = id;
				jQuery.post(
					ajaxurl,
					{idP:id, action:"sc_despublicar_pagina", _wpnonce:"<?php echo wp_create_nonce('sc_despublicar_pagina'); ?>"},
					function(data){
						if(data.success){
							new PNotify({ title: "Felicidades", text: data.Mensaje, type: "success" });
							setTimeout(function(){ window.location.reload(); }, 1000);
						}
						else{
							if(data.Mensaje != ""){ new PNotify({ title: "Error", text: data.Mensaje, type: "error" }); }
						}
					},
					"json"
				);
			}
			jQuery(document).ready(function($) {
				jQuery(".tooltips").tooltip();
				jQuery("#tblPaginas").DataTable({
					responsive: true,
					"bSort": false,
					"language": {
						"url": "<?php echo plugins_url('js/Spanish.json', __FILE__ ); ?>"
					}
				});
				var clipboard = new Clipboard(".btnCopiar");
				clipboard.on("success", function(e) {
					jQuery("#"+e.trigger.id).tooltip({trigger: "manual", title: "Copiado!"});
					jQuery("#"+e.trigger.id).tooltip("show");
					jQuery("#"+e.trigger.id).html('<i class="fa fa-check"></i>');
					setTimeout(function(){ jQuery("#"+e.trigger.id).html('<i class="fa fa-copy"></i>'); jQuery("#"+e.trigger.id).tooltip("destroy"); }, 1000);
				});
				clipboard.on("error", function(e) {
					var msj = "";
					if(/iPhone|iPad/i.test(navigator.userAgent)) {
						msj = "El navegador web no soporta la función de copiado, debes hacerlo manualmente";
					}
					else if (/Mac/i.test(navigator.userAgent)) {
						msj = "Presiona ⌘-C para copiar";
					}
					else {
						msj = "Presiona Ctrl-C para copiar";
					}
					jQuery("#"+e.trigger.id).tooltip({trigger: "manual", title: msj});
					jQuery("#"+e.trigger.id).tooltip("show");
					setTimeout(function(){ jQuery("#"+e.trigger.id).tooltip("destroy"); }, 5000);
				});
			});
		</script>
<?php
	}
?>
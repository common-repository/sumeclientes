<?php
	/**
	* Plugin Name: Sume Clientes
	* Plugin URI: https://sumeclientes.com
	* Description: Este complemento agrega un elemento al sidebar de WordPress que permite a los clientes publicar las páginas de captura creadas en la plataforma Sume Clientes.
	* Version: 0.1.15
	* Author: Sume Clientes
	* Author URI: https://sumeclientes.com
	* License: GPL2
	*/
	if ( ! defined( 'ABSPATH' ) ) exit;
	//PROCESO DE INSTALACIÓN DEL COMPLEMENTO ---------------------------
	function sc_instalacion(){
		global $wpdb;
		$sume_clientes_conf = $wpdb->prefix.'sume_clientes_conf';
		if($wpdb->get_var("SHOW TABLES LIKE '$sume_clientes_conf'") != $sume_clientes_conf){
			$sql = "CREATE TABLE IF NOT EXISTS ".$sume_clientes_conf." (
			`ID` INT NOT NULL AUTO_INCREMENT COMMENT '',
			`api_key` VARCHAR(255) NOT NULL,
			`sandbox` TINYINT(1) NOT NULL DEFAULT 0,
			`codigo_header` TEXT NULL DEFAULT NULL,
			`codigo_footer` TEXT NULL DEFAULT NULL,
			PRIMARY KEY (`ID`)
			) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8 COLLATE = utf8_spanish_ci;";
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
		}
		$sume_clientes_paginas = $wpdb->prefix.'sume_clientes_paginas';
		if($wpdb->get_var("SHOW TABLES LIKE '$sume_clientes_paginas'") != $sume_clientes_paginas){
			$sql = "CREATE TABLE IF NOT EXISTS ".$sume_clientes_paginas." (
			`ID` INT NOT NULL AUTO_INCREMENT COMMENT '',
			`post_id` INT NOT NULL COMMENT '',
			`plantillasxclientes_id` INT NOT NULL COMMENT '',
			`nombre` VARCHAR(255) NOT NULL COMMENT '',
			`title` VARCHAR(255) NOT NULL CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci COMMENT '',
			`description` VARCHAR(255) NOT NULL CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci COMMENT '',
			`keywords` VARCHAR(255) NOT NULL COMMENT '',
			`slug` VARCHAR(100) NOT NULL COMMENT '',
			`archivo` VARCHAR(255) NOT NULL COMMENT '',
			`codigo_html` LONGTEXT NOT NULL CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci COMMENT '',
			PRIMARY KEY (`ID`)  COMMENT '') ENGINE = InnoDB DEFAULT CHARACTER SET = utf8 COLLATE = utf8_spanish_ci;";
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
		}
		else{
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			if($wpdb->get_var("SELECT CHARACTER_MAXIMUM_LENGTH FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$sume_clientes_paginas' AND COLUMN_NAME = 'title';") < 255){
				$sql = "ALTER TABLE `wordpress`.`wp_sume_clientes_paginas` CHANGE COLUMN `title` `title` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_spanish_ci' NOT NULL;";
				dbDelta($sql);
			}
			if($wpdb->get_var("SELECT CHARACTER_MAXIMUM_LENGTH FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$sume_clientes_paginas' AND COLUMN_NAME = 'description';") < 255){
				$sql = "ALTER TABLE `wordpress`.`wp_sume_clientes_paginas` CHANGE COLUMN `description` `description` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_spanish_ci' NOT NULL;";
				dbDelta($sql);
			}
			if($wpdb->get_var("SELECT CHARACTER_MAXIMUM_LENGTH FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$sume_clientes_paginas' AND COLUMN_NAME = 'keywords';") < 255){
				$sql = "ALTER TABLE `wordpress`.`wp_sume_clientes_paginas` CHANGE COLUMN `keywords` `keywords` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_spanish_ci' NOT NULL;";
				dbDelta($sql);
			}
			if($wpdb->get_var("SELECT CHARACTER_MAXIMUM_LENGTH FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$sume_clientes_paginas' AND COLUMN_NAME = 'slug';") < 100){
				$sql = "ALTER TABLE `wordpress`.`wp_sume_clientes_paginas` CHANGE COLUMN `slug` `slug` VARCHAR(100) CHARACTER SET 'utf8' COLLATE 'utf8_spanish_ci' NOT NULL;";
				dbDelta($sql);
			}
		}
		$sume_clientes_variaciones = $wpdb->prefix.'sume_clientes_variaciones';
		if($wpdb->get_var("SHOW TABLES LIKE '$sume_clientes_variaciones'") != $sume_clientes_variaciones){
			$sql = "CREATE TABLE IF NOT EXISTS ".$sume_clientes_variaciones." (
			`ID` INT NOT NULL AUTO_INCREMENT COMMENT '',
			`post_id` INT NOT NULL COMMENT '',
			`variaciones_id` INT NOT NULL COMMENT '',
			`plantillasxclientes1_id` INT NOT NULL COMMENT '',
			`plantillasxclientes2_id` INT NOT NULL COMMENT '',
			`url_sume_clientes` VARCHAR(100) NOT NULL COMMENT '',
			`url` VARCHAR(100) NOT NULL COMMENT '',
			`contador` INT NOT NULL COMMENT '',
			PRIMARY KEY (`ID`)  COMMENT '') ENGINE = InnoDB DEFAULT CHARACTER SET = utf8 COLLATE = utf8_spanish_ci;";
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
		}
	}
	register_activation_hook(__FILE__, 'sc_instalacion');
	//------------------------------------------------------------------
	//PROCESO DE DESINSTALACIÓN DEL COMPLEMENTO ------------------------
	function sc_desinstalacion(){
		global $wpdb;
		//PROCESO DE ELIMINACIÓN DE POSTS --------------------------------------
		$sume_clientes_paginas = $wpdb->prefix.'sume_clientes_paginas';
		$sume_clientes_variaciones = $wpdb->prefix.'sume_clientes_variaciones';
		$sql = "(SELECT post_id AS ID FROM $sume_clientes_paginas) UNION (SELECT post_id AS ID FROM $sume_clientes_variaciones);";
		$posts = $wpdb->get_results($sql, ARRAY_A);
		$postsids = array(0);
		foreach($posts as $post){ $postsids[] = $post['ID']; }
		$ids = substr(str_repeat("%d, ", count($postsids)), 0, (strlen($ids)-2));
		$wpdb->query($wpdb->prepare("DELETE FROM $wpdb->posts WHERE id IN($ids);", $postsids));
		//----------------------------------------------------------------------
		$sume_clientes_conf = $wpdb->prefix.'sume_clientes_conf';
		$wpdb->query("DROP TABLE IF EXISTS $sume_clientes_conf;");
		$sume_clientes_paginas = $wpdb->prefix.'sume_clientes_paginas';
		$wpdb->query("DROP TABLE IF EXISTS $sume_clientes_paginas;");
		$sume_clientes_variaciones = $wpdb->prefix.'sume_clientes_variaciones';
		$wpdb->query("DROP TABLE IF EXISTS $sume_clientes_variaciones;");
	}
	register_deactivation_hook(__FILE__, 'sc_desinstalacion');
	//------------------------------------------------------------------
	//PROCESO DE OCULTACIÓN DE PAGINAS ---------------------------------------------------------------------------------------------------------------------------------
	function sc_excluir_paginas_sume_clientes_publicadas($query){
		global $wpdb;
		$sume_clientes_paginas = $wpdb->prefix.'sume_clientes_paginas';
		$sume_clientes_variaciones = $wpdb->prefix.'sume_clientes_variaciones';
		$sql = "(SELECT post_id AS ID FROM $sume_clientes_paginas) UNION (SELECT post_id AS ID FROM $sume_clientes_variaciones);";
		$posts = $wpdb->get_results($sql, ARRAY_A);
		$postsids = array(0);
		foreach($posts as $post){ $postsids[] = $post['ID']; }
		if( is_admin() && !empty( $_GET['post_type'] ) && $_GET['post_type'] == 'page' && $query->query['post_type'] == 'page' && !current_user_can( 'be_overlord' ) ) {
			$query->set('post__not_in', $postsids);
		}
	}
	add_action('pre_get_posts', 'sc_excluir_paginas_sume_clientes_publicadas');
	//------------------------------------------------------------------------------------------------------------------------------------------------------------------
	//PROCESO DE CREACIÓN DEL MENÚ DE SUME CLIENTES --------------------------------------------------------------------------------------------------
	function sc_menu_sumeclientes(){
		add_menu_page('Sume Clientes', 'Sume Clientes', 'manage_options', 'sume_clientes', 'sc_paginas_captura', plugins_url( 'icon.png', __FILE__ ), 5);
		add_submenu_page('sume_clientes', 'Variaciones', 'Variaciones', 'manage_options', 'variaciones', 'sc_variaciones');
		add_submenu_page('sume_clientes', 'Configuracion', 'Configuracion', 'manage_options', 'configuracion', 'sc_pagina_configuracion');
	}
	add_action('admin_menu', 'sc_menu_sumeclientes');
	//------------------------------------------------------------------------------------------------------------------------------------------------
	//PROCESO DE IMPRESIÓN DE VISTAS DEL MENÚ
	function sc_paginas_captura(){
		if( sc_verificar_usuario() ){
			include 'paginas_captura.php';
		}
		else{
			include 'usuario_inactivo.php';
		}
	}
	function sc_pagina_configuracion(){
		if( sc_verificar_usuario() ){
			include 'configuracion.php';
		}
		else{
			include 'usuario_inactivo.php';
		}
	}
	function sc_variaciones(){
		if( sc_verificar_usuario() ){
			include 'variaciones.php';
		}
		else{
			include 'usuario_inactivo.php';
		}
	}
	//---------------------------------------
	//PROCESO AJAX DE CONFIGURACIÓN DE API -------------------------------------------------------------------------------------------------------
	if(is_admin()){
		add_action('wp_ajax_sc_guardar_apikey', 'sc_guardar_apikey_callback');
		add_action('wp_ajax_nopriv_sc_guardar_apikey', 'sc_guardar_apikey_callback');
		add_action('wp_ajax_sc_guardar_codigoextra', 'sc_guardar_codigoextra_callback');
		add_action('wp_ajax_nopriv_sc_guardar_codigoextra', 'sc_guardar_codigoextra_callback');
		add_action('wp_ajax_sc_publicar_pagina', 'sc_publicar_pagina_callback');
		add_action('wp_ajax_nopriv_sc_publicar_pagina', 'sc_publicar_pagina_callback');
		add_action('wp_ajax_sc_publicar_variacion', 'sc_publicar_variacion_callback');
		add_action('wp_ajax_nopriv_sc_publicar_variacion', 'sc_publicar_variacion_callback');
		add_action('wp_ajax_sc_actualizar_pagina', 'sc_actualizar_pagina_callback');
		add_action('wp_ajax_nopriv_sc_actualizar_pagina', 'sc_actualizar_pagina_callback');
		add_action('wp_ajax_sc_despublicar_pagina', 'sc_despublicar_pagina_callback');
		add_action('wp_ajax_nopriv_sc_despublicar_pagina', 'sc_despublicar_pagina_callback');
		add_action('wp_ajax_sc_despublicar_variacion', 'sc_despublicar_variacion_callback');
		add_action('wp_ajax_nopriv_sc_despublicar_variacion', 'sc_despublicar_variacion_callback');
	}
	function sc_guardar_apikey_callback(){
		if( check_admin_referer('sc_guardar_apikey') && check_ajax_referer('sc_guardar_apikey') ){
			//DESINFECTANDO VARIABLES DE POST ----------------
			$api_key = sanitize_text_field($_POST['api_key']);
			$sandbox = sanitize_text_field($_POST['sandbox']);
			//------------------------------------------------
			if($api_key != ''){
				//VALIDACIÓN DE EXISTENCIA DE API KEY EN BD ---------------------------------------------------------------------------------------------------------------------------------------
				//ESCAPEANDO RUTA PARA COMUNICAR CON API-----------------------------------------------------------------------
				$urlext = ($sandbox == 1)?'https://sume.space/WP/Verificar/':'https://sumeclientes.net/WP/Verificar/';
				$ruta = ('127.0.0.1' == $_SERVER['REMOTE_ADDR'])?'http://localhost/sume_clientes/public/WP/Verificar/':$urlext;
				$ruta = $ruta.$api_key.'/'.$_SERVER['SERVER_NAME'];
				$ruta = esc_url($ruta);
				//-------------------------------------------------------------------------------------------------------------
				$curl = curl_init();
				curl_setopt_array($curl, array(CURLOPT_RETURNTRANSFER => 1, CURLOPT_URL => $ruta, CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT']));
				$resp = curl_exec($curl);
				curl_close($curl);
				//---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
				$validar = json_decode($resp);
				if($validar->success){
					global $wpdb;
					$sume_clientes_conf = $wpdb->prefix.'sume_clientes_conf';
					$sql = "SELECT ID, sandbox FROM ".$sume_clientes_conf.";";
					$Configuracion = $wpdb->get_results($sql, ARRAY_A);
					if(count($Configuracion)==1){
						if($sandbox == 1 || (int)$Configuracion[0]['sandbox'] == 1){
							$wpdb->update($sume_clientes_conf, array('api_key' => $api_key, 'sandbox' => $sandbox), array('ID' => (int)$Configuracion[0]['ID']), array('%s', '%d'), array('%d'));
						}
						else{
							$wpdb->update($sume_clientes_conf, array('api_key' => $api_key), array('ID' => (int)$Configuracion[0]['ID']), array('%s'), array('%d'));
						}
						$response = array('success' => true, 'Mensaje' => 'API Key actualizada exitosamente');
					}
					else if(count($Configuracion)==0){
						if($sandbox == 1){
							$wpdb->insert($sume_clientes_conf, array('api_key' => $api_key, 'sandbox' => $sandbox), array('%s'));
						}
						else{
							$wpdb->insert($sume_clientes_conf, array('api_key' => $api_key), array('%s'));
						}
						$response = array('success' => true, 'Mensaje' => 'API Key almacenada exitosamente');
					}
				}
				else{
					$errMensajes['api_key'] = 'La API Key ingresada no existe o ya está en uso, por favor verifiquela';
					$response = array('success' => false, 'errMensajes' => $errMensajes);
				}
			}
			else{
				$errMensajes['api_key'] = 'El campo es obligatorio';
				$response = array('success' => false, 'errMensajes' => $errMensajes);
			}
			echo json_encode($response);
			wp_die();
		}
		else{
			$response = array('success' => false, 'Mensaje' => 'No tienes permisos para ejecutar está función');
			echo json_encode($response);
		}
	}
	function sc_guardar_codigoextra_callback(){
		if( check_admin_referer('sc_guardar_codigoextra') && check_ajax_referer('sc_guardar_codigoextra') ){
			global $wpdb;
			$sume_clientes_conf = $wpdb->prefix.'sume_clientes_conf';
			$sql = "SELECT ID FROM ".$sume_clientes_conf.";";
			$Configuracion = $wpdb->get_results($sql, ARRAY_A);
			//DESINFECTANDO VARIABLES ---------------------------------------------------------
			$codigo_header = esc_html(balanceTags(stripslashes_deep($_POST['codigo_header'])));
			$codigo_footer = esc_html(balanceTags(stripslashes_deep($_POST['codigo_footer'])));
			//---------------------------------------------------------------------------------
			if(count($Configuracion)==1){
				$wpdb->update($sume_clientes_conf, array('codigo_header' => $codigo_header, 'codigo_footer' => $codigo_footer), array('ID' => (int)$Configuracion[0]['ID']), array('%s', '%s'), array('%d'));
				$response = array('success' => true, 'Mensaje' => 'Códigos extra almacenados exitosamente');
			}
			else if(count($Configuracion)==0){
				$wpdb->insert($sume_clientes_conf, array('codigo_header' => $codigo_header, 'codigo_footer' => $codigo_footer), array('%s'));
				$response = array('success' => true, 'Mensaje' => 'Códigos extra almacenados exitosamente');
			}
			echo json_encode($response);
			wp_die();
		}
		else{
			$response = array('success' => false, 'Mensaje' => 'No tienes permisos para ejecutar está función');
			echo json_encode($response);
		}
	}
	function sc_publicar_pagina_callback(){
		if( check_admin_referer('sc_publicar_pagina') && check_ajax_referer('sc_publicar_pagina') ){
			//OBTENIENDO LAS PÁGINAS DE CAPTURAS CREADAS EN LA PLATAFORMA -------------------------------------------------------------------------------------
			global $wpdb;
			//VALIDACIÓN DE ENTORNO --------------------------------------------------------
			$sume_clientes_conf = $wpdb->prefix.'sume_clientes_conf';
			$sql = "SELECT sandbox FROM ".$sume_clientes_conf.";";
			$sandbox = $wpdb->get_results($sql, ARRAY_A);
			$sumeclientes = ( $sandbox[0]['sandbox'] == 1 )?'sume.space':'sumeclientes.net';
			//------------------------------------------------------------------------------
			//ESCAPEANDO RUTA PARA COMUNICAR CON API-----------------------------------------------------------------------------------------------
			$ruta = ('127.0.0.1' == $_SERVER['REMOTE_ADDR'])?'http://localhost/sume_clientes/public/WP/Pagina/':"https://$sumeclientes/WP/Pagina/";
			$ruta = $ruta.$_POST['idP'];
			$ruta = esc_url($ruta);
			//-------------------------------------------------------------------------------------------------------------------------------------
			$curl = curl_init();
			curl_setopt_array($curl, array(CURLOPT_RETURNTRANSFER => 1, CURLOPT_URL => $ruta, CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT']));
			$resp = curl_exec($curl);
			curl_close($curl);
			$pagina = json_decode($resp);
			//DESINFECTANDO VARIABLES -------------------------------
			$idP = sanitize_text_field($pagina->id);
			$nombre = sanitize_text_field($pagina->nombre);
			$title = sanitize_text_field($pagina->title);
			$description = sanitize_text_field($pagina->description);
			$keywords = sanitize_text_field($pagina->keywords);
			$slug = sanitize_text_field($pagina->slug);
			$archivo = sanitize_text_field($pagina->final_html);
			//-------------------------------------------------------
			//-------------------------------------------------------------------------------------------------------------------------------------------------
			$sql = $wpdb->prepare("SELECT COUNT(id) AS cantidad FROM $wpdb->posts WHERE post_name = %s OR guid LIKE %s;", array($slug, '%'.$slug.'%'));
			$count = $wpdb->get_results($sql, ARRAY_A);
			if($count['cantidad'] == 0){
				//CREACIÓN DE POST PARA PUBLICACIÓN DE PÁGINA DE CAPTURA -----------------------------------------------------------------------------------------------------------------------------
				$user_id = get_current_user_id();
				$sc_post = array('post_title' => wp_strip_all_tags($slug), 'post_content' => 'Página de Sume Clientes', 'post_status' => 'publish', 'post_author' => $user_id, 'post_type' => 'page');
				$postid = wp_insert_post($sc_post);
				//------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
				//OBTENIENDO LA PÁGINA DE CAPTURAS CREADAS EN LA PLATAFORMA -----------------------------------------------------------------------------------------
				//ESCAPEANDO RUTA PARA COMUNICAR CON API-----------------------------------------------------------------------------------------------
				$ruta = ('127.0.0.1' == $_SERVER['REMOTE_ADDR'])?'http://localhost/sume_clientes/public/WP/Pagina/':"https://$sumeclientes/WP/Pagina/";
				$ruta = $ruta.$pagina->id.'/HTML';
				$ruta = esc_url($ruta);
				//-------------------------------------------------------------------------------------------------------------------------------------
				$curl = curl_init();
				curl_setopt_array($curl, array(CURLOPT_RETURNTRANSFER => 1, CURLOPT_URL => $ruta, CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT']));
				$resp = curl_exec($curl);
				curl_close($curl);
				$HTML = json_decode($resp);
				//DESINFECTANDO VARIABLES -------------------------------------------------
				$codigo_html = esc_html(balanceTags(stripslashes_deep($HTML->PaginaHTML)));
				//-------------------------------------------------------------------------
				//-----------------------------------------------------------------------------------------------------------------------------------------------------
				$sume_clientes_paginas = $wpdb->prefix.'sume_clientes_paginas';
				//CREACIÓN DE POST PARA PUBLICACIÓN DE PÁGINA DE CAPTURA -------------------------------------------------------------------------------------------------------------------------------------
				$wpdb->insert($sume_clientes_paginas, array('post_id' => (int)$postid, 'plantillasxclientes_id' => (int)$idP, 'nombre' => $nombre, 'title' => $title, 'description' => $description, 'keywords' => $keywords, 'slug' => $slug, 'archivo' => $archivo, 'codigo_html' => $codigo_html),
					array('%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s')
				);
				//CREACIÓN DE POST PARA PUBLICACIÓN DE PÁGINA DE CAPTURA -------------------------------------------------------------------------------------------------------------------------------------
				if( $wpdb->insert_id != false ){
					$response = array('success' => true, 'Mensaje' => 'Página publicada exitosamente');
				}
				else{
					$wpdb->delete($wpdb->posts, array('ID' => (int)$postid), array('%d'));
					$response = array('success' => false, 'Mensaje' => 'Ha ocurrido un error al intentar publicar la página');
				}
			}
			else{
				$response = array('success' => false, 'Mensaje' => 'Ya existe una página con ese slug, por favor asigne uno nuevo');
			}
			echo json_encode($response);
			wp_die();
		}
		else{
			$response = array('success' => false, 'Mensaje' => 'No tienes permisos para ejecutar está función');
			echo json_encode($response);
		}
	}
	function sc_publicar_variacion_callback(){
		if( check_admin_referer('sc_publicar_variacion') && check_ajax_referer('sc_publicar_variacion') ){
			//OBTENIENDO LAS PÁGINAS DE CAPTURAS CREADAS EN LA PLATAFORMA -----------------------------------------------------------------------------------------
			global $wpdb;
			//VALIDACIÓN DE ENTORNO --------------------------------------------------------
			$sume_clientes_conf = $wpdb->prefix.'sume_clientes_conf';
			$sql = "SELECT sandbox FROM ".$sume_clientes_conf.";";
			$sandbox = $wpdb->get_results($sql, ARRAY_A);
			$sumeclientes = ( $sandbox[0]['sandbox'] == 1 )?'sume.space':'sumeclientes.net';
			//------------------------------------------------------------------------------
			//DESINFECTANDO VARIABLE------------------
			$idV = sanitize_text_field($_POST['idV']);
			//----------------------------------------
			//ESCAPEANDO RUTA PARA COMUNICAR CON API-----------------------------------------------------------------------------------------------------
			$ruta = ('127.0.0.1' == $_SERVER['REMOTE_ADDR'])?'http://localhost/sume_clientes/public/WP/Variacion/':"https://$sumeclientes/WP/Variacion/";
			$ruta = $ruta.$idV;
			$ruta = esc_url($ruta);
			//-------------------------------------------------------------------------------------------------------------------------------------------
			$curl = curl_init();
			curl_setopt_array($curl, array(CURLOPT_RETURNTRANSFER => 1, CURLOPT_URL => $ruta, CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT']));
			$resp = curl_exec($curl);
			curl_close($curl);
			$variacion = json_decode($resp);
			//DESINFECTANDO VARIABLES --------------------------------------------------------
			$VariacionId = sanitize_text_field($variacion->id);
			$VariacionURL = sanitize_text_field($variacion->url);
			$VariacionNombre = sanitize_text_field($variacion->nombre);
			$VariacionPlantillaId1 = sanitize_text_field($variacion->plantillasxclientes1_id);
			$VariacionPlantillaId2 = sanitize_text_field($variacion->plantillasxclientes2_id);
			//--------------------------------------------------------------------------------
			//-----------------------------------------------------------------------------------------------------------------------------------------------------
			$sume_clientes_variaciones = $wpdb->prefix.'sume_clientes_variaciones';
			$sql = $wpdb->prepare("SELECT COUNT(id) AS cantidad FROM ".$sume_clientes_variaciones." WHERE post_name = %s OR guid LIKE %s;", array($VariacionURL, '%'.$VariacionURL.'%'));
			$count = $wpdb->get_results($sql, ARRAY_A);
			if($count['cantidad'] == 0){
				//CREACIÓN DE POST PARA PUBLICACIÓN DE PÁGINA DE CAPTURA -------------------------------------------------------------------------------------------------------------------------------------
				$user_id = get_current_user_id();
				$sc_post = array('post_title' => wp_strip_all_tags(($VariacionNombre!='')?$VariacionNombre:$VariacionURL), 'post_content' => 'Variación de Sume Clientes', 'post_status' => 'publish', 'post_author' => $user_id, 'post_type' => 'page');
				$postid = wp_insert_post($sc_post);
				$sume_clientes_variaciones = $wpdb->prefix.'sume_clientes_variaciones';
				$wpdb->insert($sume_clientes_variaciones, array('post_id' => (int)$postid, 'variaciones_id' => (int)$VariacionId, 'plantillasxclientes1_id' => (int)$VariacionPlantillaId1, 'plantillasxclientes2_id' => (int)$VariacionPlantillaId2, 'url_sume_clientes' => $VariacionURL, 'url' => $VariacionURL, 'contador' => 0),
					array('%d', '%d', '%d', '%d', '%s', '%s', '%d')
				);
				//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
				$response = array('success' => true, 'Mensaje' => 'Variación publicada exitosamente');
			}
			else{
				$response = array('success' => false, 'Mensaje' => 'Ya existe una variación con ese slug, por favor asigne uno nuevo');
			}
			echo json_encode($response);
			wp_die();
		}
		else{
			$response = array('success' => false, 'Mensaje' => 'No tienes permisos para ejecutar está función');
			echo json_encode($response);
		}
	}
	function sc_actualizar_pagina_callback(){
		if( check_admin_referer('sc_actualizar_pagina') && check_ajax_referer('sc_actualizar_pagina') ){
			//OBTENIENDO LAS PÁGINAS DE CAPTURAS CREADAS EN LA PLATAFORMA -----------------------------------------------------------------------------------------
			global $wpdb;
			//VALIDACIÓN DE ENTORNO --------------------------------------------------------
			$sume_clientes_conf = $wpdb->prefix.'sume_clientes_conf';
			$sql = "SELECT sandbox FROM ".$sume_clientes_conf.";";
			$sandbox = $wpdb->get_results($sql, ARRAY_A);
			$sumeclientes = ( $sandbox[0]['sandbox'] == 1 )?'sume.space':'sumeclientes.net';
			//------------------------------------------------------------------------------
			//ESCAPEANDO RUTA PARA COMUNICAR CON API-----------------------------------------------------------------------------------------------
			$ruta = ('127.0.0.1' == $_SERVER['REMOTE_ADDR'])?'http://localhost/sume_clientes/public/WP/Pagina/':"https://$sumeclientes/WP/Pagina/";
			$ruta = $ruta.$_POST['idP'].'/HTML';
			$ruta = esc_url($ruta);
			//-------------------------------------------------------------------------------------------------------------------------------------
			$curl = curl_init();
			curl_setopt_array($curl, array(CURLOPT_RETURNTRANSFER => 1, CURLOPT_URL => $ruta, CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT']));
			$resp = curl_exec($curl);
			curl_close($curl);
			$HTML = json_decode($resp);
			//DESINFECTANDO RESULTADO -------------------------------------------------
			$codigo_html = esc_html(balanceTags(stripslashes_deep($HTML->PaginaHTML)));
			//-------------------------------------------------------------------------
			//-----------------------------------------------------------------------------------------------------------------------------------------------------
			//DESINFECTANDO VARIABLES DE POST---------
			$idP = sanitize_text_field($_POST['idP']);
			//----------------------------------------
			//ACTUALIZANDO CÓDIGO DE PÁGINA DE CAPTURA -------------------------------------------------------------------------------------------------
			$sume_clientes_paginas = $wpdb->prefix.'sume_clientes_paginas';
			$sql = $wpdb->prepare("SELECT ID FROM $sume_clientes_paginas WHERE plantillasxclientes_id = %d;", array((int)$idP));
			$pagina = $wpdb->get_results($sql, ARRAY_A);
			$wpdb->update($sume_clientes_paginas, array('codigo_html' => $codigo_html), array('ID' => (int)$pagina[0]['ID']), array('%s'), array('%d'));
			//------------------------------------------------------------------------------------------------------------------------------------------
			$response = array('success' => true, 'Mensaje' => 'Página actualizada exitosamente');
			echo json_encode($response);
			wp_die();
		}
		else{
			$response = array('success' => false, 'Mensaje' => 'No tienes permisos para ejecutar está función');
			echo json_encode($response);
		}
	}
	function sc_despublicar_pagina_callback(){
		if( check_admin_referer('sc_despublicar_pagina') && check_ajax_referer('sc_despublicar_pagina') ){
			global $wpdb;
			//DESINFECTANDO VARIABLES DE POST --------
			$idP = sanitize_text_field($_POST['idP']);
			//----------------------------------------
			$sume_clientes_paginas = $wpdb->prefix.'sume_clientes_paginas';
			$sql = $wpdb->prepare("SELECT COUNT(ID) AS cantidad FROM $sume_clientes_paginas WHERE plantillasxclientes1_id = %d OR plantillasxclientes2_id = %d;", array($idP, $idP));
			$count = $wpdb->get_results($sql, ARRAY_A);
			if($count[0]['cantidad'] == 0){
				$sql = $wpdb->prepare("SELECT ID, post_id FROM $sume_clientes_paginas WHERE plantillasxclientes_id = %d;", array($idP));
				$post = $wpdb->get_results($sql, ARRAY_A);
				$wpdb->delete($sume_clientes_paginas, array('ID' => $post[0]['ID']), array('%d'));
				$wpdb->delete($wpdb->posts, array('ID' => $post[0]['post_id']), array('%d'));
				$response = array('success' => true, 'Mensaje' => 'Página despublicada exitosamente');
			}
			else{
				$response = array('success' => false, 'Mensaje' => 'No puede despublicar la página pues está siendo utilizada en una variación, debe despubliar la variación primero');
			}
			echo json_encode($response);
			wp_die();
		}
		else{
			$response = array('success' => false, 'Mensaje' => 'No tienes permisos para ejecutar está función');
			echo json_encode($response);
		}
	}
	function sc_despublicar_variacion_callback(){
		if( check_admin_referer('sc_despublicar_variacion') && check_ajax_referer('sc_despublicar_variacion') ){
			global $wpdb;
			//DESINFECTANDO VARIABLES DE POST --------
			$idV = sanitize_text_field($_POST['idV']);
			//----------------------------------------
			$sume_clientes_variaciones = $wpdb->prefix.'sume_clientes_variaciones';
			$sql = $wpdb->prepare("SELECT ID, post_id FROM $sume_clientes_variaciones WHERE variaciones_id = %d;", array($idV));
			$post = $wpdb->get_results($sql, ARRAY_A);
			$wpdb->delete($sume_clientes_variaciones, array('ID' => $post[0]['ID']), array('%d'));
			$wpdb->delete($wpdb->posts, array('ID' => $post[0]['post_id']), array('%d'));
			$response = array('success' => true, 'Mensaje' => 'Variación despublicada exitosamente');
			echo json_encode($response);
			wp_die();
		}
		else{
			$response = array('success' => false, 'Mensaje' => 'No tienes permisos para ejecutar está función');
			echo json_encode($response);
		}
	}
	//--------------------------------------------------------------------------------------------------------------------------------------------
	//REDIRECCIONAMIENTO A PLANTILLA
	add_action('template_redirect', 'sc_redireccionamiento');
	function sc_redireccionamiento(){
		global $wpdb;
		//VALIDACIÓN DE ENTORNO --------------------------------------------------------
		$sume_clientes_conf = $wpdb->prefix.'sume_clientes_conf';
		$sql = "SELECT sandbox FROM ".$sume_clientes_conf.";";
		$sandbox = $wpdb->get_results($sql, ARRAY_A);
		$sumeclientes = ( $sandbox[0]['sandbox'] == 1 )?'sume.space':'sumeclientes.net';
		//------------------------------------------------------------------------------
		$post = get_post();
		$postid = $post->ID;
		$sume_clientes_paginas = $wpdb->prefix.'sume_clientes_paginas';
		$sume_clientes_variaciones = $wpdb->prefix.'sume_clientes_variaciones';
		$sql = $wpdb->prepare("(SELECT id, 'P' AS tipo, plantillasxclientes_id AS id2V FROM ".$sume_clientes_paginas." WHERE post_id = %d) UNION (SELECT id, 'V' AS tipo, variaciones_id AS id2V FROM ".$sume_clientes_variaciones." WHERE post_id = %d);", array((int)$postid, (int)$postid));
		$pagina = $wpdb->get_results($sql, ARRAY_A);
		$redireccionar = (count($pagina)==1)?true:false;
		$curl = curl_init();
		//GENERACIÓN DE RUTA PARA CONTADOR DE VISITAS (PÁGINAS Y VARIACIONES) -------------------------------------------------------------------------------
		$ruta = ('127.0.0.1' == $_SERVER['REMOTE_ADDR'])?'http://localhost/sume_clientes/public/WP/ContarVisitas/':"https://$sumeclientes/WP/ContarVisitas/";
		$referer = ($_SERVER['HTTP_REFERER']=='')?'':'/'.parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);
		// FIN DE GENERACIÓN DE RUTA PARA CONTADOR DE VISITAS -----------------------------------------------------------------------------------------------
		if($redireccionar){
			//VERIFICO QUE EL USUARIO SE ENCUENTRE ACTIVO --------------
			if( !sc_verificar_usuario() ){ include('404.php'); exit(); }
			//----------------------------------------------------------
			//VERIFICO EL ESTADO DE LA PÁGINA A MOSTRAR ------------------------------
			if( !sc_verificar_estado_pagina($pagina[0]['id2V'], $pagina[0]['tipo']) ){
				include('404.php'); exit();
			}
			//------------------------------------------------------------------------
			//VERIFICO QUE EL CODIGO DE LA PAGINA SE ENCUENTRE ACTUALIZADO
			if( $pagina[0]['tipo'] == 'P' ){
				sc_verificar_codigo_pagina( $pagina[0]['id2V'] );
			}
			//------------------------------------------------------------
			/* OBTENCIÓN DE CODIGOS EXTRA CREADOS POR EL USUARIO */
			$sume_clientes_conf = $wpdb->prefix.'sume_clientes_conf';
			$sql = "SELECT codigo_header, codigo_footer FROM ".$sume_clientes_conf.";";
			$Configuracion = $wpdb->get_results($sql, ARRAY_A);
			//CONEXIÓN CON CHARSET DE CARACTERES ESPECIALES Y EMOJIS ---------
			$wpdbI = new wpdb(DB_USER, DB_PASSWORD, DB_NAME, DB_HOST);
			$wpdbI->set_charset($wpdbI->dbh, 'utf8mb4', 'utf8mb4_unicode_ci');
			//----------------------------------------------------------------
			/* FIN OBTENCIÓN DE CODIGOS EXTRA CREADOS POR EL USUARIO */
			if($pagina[0]['tipo'] == 'P'){
				$sql = $wpdbI->prepare("SELECT plantillasxclientes_id, codigo_html FROM $sume_clientes_paginas WHERE post_id = %d;", array($postid));
				$plantilla = $wpdbI->get_results($sql, ARRAY_A);
				$ruta = $ruta.$_SERVER['REMOTE_ADDR'].'/'.$plantilla[0]['plantillasxclientes_id'].'/0'.$referer;
				//ESCAPEANDO RUTA------
				$ruta = esc_url($ruta);
				//---------------------
				$codigo_html = htmlspecialchars_decode($plantilla[0]['codigo_html'], ENT_QUOTES);
				/* ADICIÓN DE CÓDIGO EXTRA */
				if( (string)$Configuracion[0]['codigo_header'] != '' ){
					$codigo_html = str_replace('<style type="text/css" id="headerWP"></style>', htmlspecialchars_decode((string)$Configuracion[0]['codigo_header'], ENT_QUOTES), $codigo_html);
				}
				if( (string)$Configuracion[0]['codigo_footer'] != '' ){
					$codigo_html = str_replace('<script type="text/javascript" id="footerWP"></script>', htmlspecialchars_decode((string)$Configuracion[0]['codigo_footer'], ENT_QUOTES), $codigo_html);
				}
				/* FIN DE ADICIÓN DE CÓDIGO EXTRA */
				/* ADICIÓN DE RUTA PARA CONTEO DE VISITAS DEL LADO DEL CLIENTE */
				$codigo_html = str_replace('var rutavisitas = "";', 'var rutavisitas = "'.$ruta.'";', $codigo_html);
				$codigo_html = str_replace('<input type="hidden" id="rutavisitas" value="">', '<input type="hidden" id="rutavisitas" value="'.$ruta.'">', $codigo_html);
				/* FIN DE ADICIÓN DE RUTA PARA CONTEO DE VISITAS DEL LADO DEL CLIENTE */
				//CONVERSIÓN DE SLUGS DINÁMICOS --------------------------------------------------------------------------------------------------------------
				//LOCALIZACIÓN ----------------------------------------------------------------
				$freeGeoIP = sc_freegeoip();
				$codigo_html = str_replace('{ pais }', $freeGeoIP['country_name'], $codigo_html);
				$codigo_html = str_replace('{ ciudad }', $freeGeoIP['city'], $codigo_html);
				//-----------------------------------------------------------------------------
				//TIEMPO -------------------------------------------------------------------------------------------------------------------------------------
				$fechaHora = new DateTime($freeGeoIP['time_zone']);
				$diasES = array('Domingo', 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sábado');
				$mesesES = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
				$dia = $fechaHora->format('d');
				$mes = $mesesES[$fechaHora->format('n')-1];
				$anho = $fechaHora->format('Y');
				$codigo_html = str_replace('{ dia }', $dia, $codigo_html);
				$codigo_html = str_replace('{ mes }', $mes, $codigo_html);
				$codigo_html = str_replace(array('{ año }', '{ a&ntilde;o }', '{ a&#241;o }'), $anho, $codigo_html);
				//--------------------------------------------------------------------------------------------------------------------------------------------
				//-----------------------------------------------------------------------------------------------------------------------------------------------------
				echo $codigo_html;
			}
			else if($pagina[0]['tipo'] == 'V'){
				$sql = $wpdb->prepare("SELECT ID, variaciones_id FROM $sume_clientes_variaciones WHERE post_id = %d;", array($postid));
				$variacion = $wpdb->get_results($sql, ARRAY_A);
				//ESCAPEANDO RUTA PARA COMUNICAR CON API------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
				$rutacvv = ('127.0.0.1' == $_SERVER['REMOTE_ADDR'])?'http://localhost/sume_clientes/public/WP/Contador/Variacion/'.$variacion[0]['variaciones_id']:"https://$sumeclientes/WP/Contador/Variacion/".$variacion[0]['variaciones_id'];
				$rutacvv = esc_url($rutacvv);
				//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
				curl_setopt_array($curl, array(CURLOPT_RETURNTRANSFER => 1, CURLOPT_URL => $rutacvv, CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT']));
				$resp = curl_exec($curl); $response = json_decode($resp);
				/* FIN DE CONTADOR DE IMPRESIÓN DE VARIACIÓN DE LA PLATAFORMA */
				if($response->success == 'true'){
					//DESINFECTANDO VARIABLES --------------------------------------
					$plantillasxclientes_id = sanitize_text_field($response->idPxC);
					//--------------------------------------------------------------
					//VERIFICO QUE EL CODIGO DE LA PAGINA SE ENCUENTRE ACTUALIZADO
					sc_verificar_codigo_pagina( $plantillasxclientes_id );
					//------------------------------------------------------------
					//MODIFICACIÓN DE RUTA DE CONTADOR DE VISITAS PARA VARIACIONES ------------------------------------------------
					$ruta = $ruta.$_SERVER['REMOTE_ADDR'].'/'.$plantillasxclientes_id.'/'.$variacion[0]['variaciones_id'].$referer;
					//-------------------------------------------------------------------------------------------------------------
					$sql = $wpdbI->prepare("SELECT codigo_html FROM $sume_clientes_paginas WHERE plantillasxclientes_id = %d;", array((int)$plantillasxclientes_id));
					$plantilla = $wpdbI->get_results($sql, ARRAY_A);
					$idP = $plantillasxclientes_id;
					$idV = $variacion[0]['variaciones_id'];
					$codigo_html = htmlspecialchars_decode($plantilla[0]['codigo_html'], ENT_QUOTES);
					/* MODIFICACIÓN DE PARAMETROS PARA FUNCIONAMIENTO DE CONTÉO DE CONVERSIONES EN VARIACIONES */
					$codigo_html = str_replace('var idV = 0;', "var idV = $idV;", $codigo_html);
					$codigo_html = str_replace("url: routeForm+\"/$idP\",", "url: routeForm+\"/$idP/$idV\",", $codigo_html);
					$codigo_html = str_replace("ruta+\"/$idP\",", "ruta+\"/$idP/$idV\",", $codigo_html);
					/* MODIFICACIÓN DE VARIABLE DE VARIACION */
					$hdn = '<input type="hidden" id="hdnV" value="'.$idV.'">';
					$codigo_html = str_replace('<input type="hidden" id="hdnV" value="0">', $hdn, $codigo_html);
					$codigo_html = str_replace('<input type=hidden id=hdnV value=0>', $hdn, $codigo_html);
					/* FIN DE MODIFICACIÓN DE VARIABLE DE VARIACION */
					/* FIN DE MODIFICACIÓN DE PARAMETROS PARA FUNCIONAMIENTO DE CONTÉO DE CONVERSIONES EN VARIACIONES */
					/* ADICIÓN DE RUTA PARA CONTEO DE VISITAS DEL LADO DEL CLIENTE */
					$codigo_html = str_replace('var rutavisitas = "";', 'var rutavisitas = "'.$ruta.'";', $codigo_html);
					$codigo_html = str_replace('<input type="hidden" id="rutavisitas" value="">', '<input type="hidden" id="rutavisitas" value="'.$ruta.'">', $codigo_html);
					/* FIN DE ADICIÓN DE RUTA PARA CONTEO DE VISITAS DEL LADO DEL CLIENTE */
					/* ADICIÓN DE CÓDIGO EXTRA */
					if( (string)$Configuracion[0]['codigo_header'] != '' ){
						$codigo_html = str_replace('<style type="text/css" id="headerWP"></style>', htmlspecialchars_decode((string)$Configuracion[0]['codigo_header'], ENT_QUOTES), $codigo_html);
					}
					if( (string)$Configuracion[0]['codigo_footer'] != '' ){
						$codigo_html = str_replace('<script type="text/javascript" id="footerWP"></script>', htmlspecialchars_decode((string)$Configuracion[0]['codigo_footer'], ENT_QUOTES), $codigo_html);
					}
					/* FIN DE ADICIÓN DE CÓDIGO EXTRA */
					//CONVERSIÓN DE SLUGS DINÁMICOS --------------------------------------------------------------------------------------------------------------
					//LOCALIZACIÓN ----------------------------------------------------------------
					$freeGeoIP = sc_freegeoip();
					$codigo_html = str_replace('{ pais }', $freeGeoIP['country_name'], $codigo_html);
					$codigo_html = str_replace('{ ciudad }', $freeGeoIP['city'], $codigo_html);
					//-----------------------------------------------------------------------------
					//TIEMPO -------------------------------------------------------------------------------------------------------------------------------------
					$fechaHora = new DateTime($freeGeoIP['time_zone']);
					$diasES = array('Domingo', 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sábado');
					$mesesES = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
					$dia = $fechaHora->format('d');
					$mes = $mesesES[$fechaHora->format('n')-1];
					$anho = $fechaHora->format('Y');
					$codigo_html = str_replace('{ dia }', $dia, $codigo_html);
					$codigo_html = str_replace('{ mes }', $mes, $codigo_html);
					$codigo_html = str_replace(array('{ año }', '{ a&ntilde;o }', '{ a&#241;o }'), $anho, $codigo_html);
					//--------------------------------------------------------------------------------------------------------------------------------------------
					//-----------------------------------------------------------------------------------------------------------------------------------------------------
					echo $codigo_html;
				}
				else{
					echo 'Error 404';
				}
			}
			curl_close($curl);
			exit();
		}
	}
	//---------------------------------------------------
	//AGREGANDO LAS HOJAS DE ESTILO --------------------
	add_action('admin_enqueue_scripts', 'sc_scripts_estilos_wp_admin');
	function sc_scripts_estilos_wp_admin($hook) {
		if($hook == 'toplevel_page_sume_clientes' || $hook == 'sume-clientes_page_variaciones' || $hook == 'sume-clientes_page_configuracion'){
			//HOJAS DE ESTÍLO
			wp_enqueue_style('bootstrap_css', plugins_url('css/bootstrap.min.css', __FILE__));
			wp_enqueue_style('font_awesome_css', plugins_url('css/font-awesome.min.css', __FILE__));
			wp_enqueue_style('style_css', plugins_url('css/style.css', __FILE__));
			wp_enqueue_style('style_responsive_css', plugins_url('css/style-responsive.css', __FILE__));
			wp_enqueue_style('pnotify_css', plugins_url('css/pnotify.custom.min.css', __FILE__));
			wp_enqueue_style('bootstrap_dialog_css', plugins_url('css/bootstrap-dialog.min.css', __FILE__));
			wp_enqueue_style('dataTables_bootstrap_css', plugins_url('css/dataTables.bootstrap.css', __FILE__));
			//SCRIPTS
			wp_enqueue_script('bootstrap_js', plugins_url('js/bootstrap.min.js', __FILE__));
			wp_enqueue_script('pnotify_js', plugins_url('js/pnotify.custom.min.js', __FILE__));
			wp_enqueue_script('bootstrap_dialog_js', plugins_url('js/bootstrap-dialog.min.js', __FILE__));
			wp_enqueue_script('clipboard_js', plugins_url('js/clipboard.min.js', __FILE__));
			wp_enqueue_script('jquery_dataTables_js', plugins_url('js/jquery.dataTables.min.js', __FILE__));
			wp_enqueue_script('dataTables_bootstrap_js', plugins_url('js/dataTables.bootstrap.min.js', __FILE__));
			//return;
		}
	}
	//---------------------------------------------------
	//CÓDIGOS EXTRAS --------------------------------------------------------------------------------------------------------------
	add_action('wp_enqueue_scripts', 'sc_scripts_sume_clientes' );
	function sc_scripts_sume_clientes(){
		wp_enqueue_script('iframe-resizer', plugins_url('js/iframe-resizer/iframeResizer.min.js', __FILE__ ), array(), 3.5, false);
	}
	add_action('wp_head', 'sc_wp_head_sume_clientes');
	function sc_wp_head_sume_clientes(){
		global $wpdb;
		$sume_clientes_conf = $wpdb->prefix.'sume_clientes_conf';
		$sql = "SELECT codigo_header FROM ".$sume_clientes_conf.";";
		$Configuracion = $wpdb->get_results($sql, ARRAY_A);
		echo htmlspecialchars_decode((string)$Configuracion[0]['codigo_header'], ENT_QUOTES);
	}
	add_action('wp_footer', 'sc_wp_footer_sume_clientes');
	function sc_wp_footer_sume_clientes(){
		global $wpdb;
		$sume_clientes_conf = $wpdb->prefix.'sume_clientes_conf';
		$sql = "SELECT codigo_footer FROM ".$sume_clientes_conf.";";
		$Configuracion = $wpdb->get_results($sql, ARRAY_A);
		echo htmlspecialchars_decode((string)$Configuracion[0]['codigo_footer'], ENT_QUOTES);
	}
	//-----------------------------------------------------------------------------------------------------------------------------
	//UTILIDADES EXTRA ------------------------------------------------------
	function sc_freegeoip(){
		//$url = 'https://freegeoip.net/json/';
		$url = 'https://json.geoiplookup.io/';
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_URL, $url);
		$result = curl_exec($ch);
		curl_close($ch);
		$response = json_decode($result, true);
		//DESINFECTANDO VARIABLES -------------------------------------------
		$sanitized_response = array(
			'ip' => sanitize_text_field($response['ip']),
			'country_code' => sanitize_text_field($response['country_code']),
			'country_name' => sanitize_text_field($response['country_name']),
			//'region_code' => sanitize_text_field($response['region_code']),
			//'region_name' => sanitize_text_field($response['region_name']),
			'city' => sanitize_text_field($response['city']),
			//'zip_code' => sanitize_text_field($response['zip_code']),
			'zip_code' => sanitize_text_field($response['postal_code']),
			//'time_zone' => sanitize_text_field($response['time_zone']),
			'time_zone' => sanitize_text_field($response['timezone_name']),
			'latitude' => sanitize_text_field($response['latitude']),
			'longitude' => sanitize_text_field($response['longitude']),
			//'metro_code' => sanitize_text_field($response['metro_code'])
		);
		//------------------------------------------------------------------
		return $sanitized_response;
	}
	function sc_verificar_usuario(){
		global $wpdb;
		$sume_clientes_conf = $wpdb->prefix.'sume_clientes_conf';
		$sql = "SELECT COUNT(ID) AS contador FROM ".$sume_clientes_conf.";";
		$contador = $wpdb->get_results($sql, ARRAY_A);
		if( $contador[0]['contador'] == 1 ){
			$sql = "SELECT api_key FROM ".$sume_clientes_conf.";";
			$api_key = $wpdb->get_results($sql, ARRAY_A);
			if( $api_key != '' ){
				//VALIDACIÓN DE ENTORNO --------------------------------------------------------------
				$sql = "SELECT sandbox, api_key FROM ".$sume_clientes_conf.";";
				$configuracion = $wpdb->get_results($sql, ARRAY_A);
				$sumeclientes = ( $configuracion[0]['sandbox'] == 1 )?'sume.space':'sumeclientes.net';
				//------------------------------------------------------------------------------------
				//ESCAPEANDO RUTA PARA COMUNICAR CON API-----------------------------------------------------------------------------------------------------------------
				$ruta = ('127.0.0.1' == $_SERVER['REMOTE_ADDR'])?'http://localhost/sume_clientes/public/WP/Validar/Usuario/':"https://$sumeclientes/WP/Validar/Usuario/";
				$ruta = $ruta.$configuracion[0]['api_key'];
				$ruta = esc_url($ruta);
				//-------------------------------------------------------------------------------------------------------------------------------------------------------
				$curl = curl_init();
				curl_setopt_array($curl, array(CURLOPT_RETURNTRANSFER => 1, CURLOPT_URL => $ruta, CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT']));
				$resp = curl_exec($curl);
				curl_close($curl);
				$validacion = json_decode($resp);
				return $validacion->valido;
			}
			else{
				return true;
			}
		}
		else{
			return true;
		}
	}
	function sc_verificar_estado_pagina($id, $tipo){
		global $wpdb;
		//VALIDACIÓN DE ENTORNO --------------------------------------------------------------
		$sume_clientes_conf = $wpdb->prefix.'sume_clientes_conf';
		$sql = "SELECT sandbox FROM ".$sume_clientes_conf.";";
		$configuracion = $wpdb->get_results($sql, ARRAY_A);
		$sumeclientes = ( $configuracion[0]['sandbox'] == 1 )?'sume.space':'sumeclientes.net';
		//------------------------------------------------------------------------------------
		//ESCAPEANDO RUTA PARA COMUNICAR CON API---------------------------------------------------------------------------------------------------------------
		$ruta = ('127.0.0.1' == $_SERVER['REMOTE_ADDR'])?'http://localhost/sume_clientes/public/WP/Validar/Pagina/':"https://$sumeclientes/WP/Validar/Pagina/";
		$ruta = $ruta.$id.'/'.$tipo;
		$ruta = esc_url($ruta);
		//-----------------------------------------------------------------------------------------------------------------------------------------------------
		$curl = curl_init();
		curl_setopt_array($curl, array(CURLOPT_RETURNTRANSFER => 1, CURLOPT_URL => $ruta, CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT']));
		$resp = curl_exec($curl);
		curl_close($curl);
		$validacion = json_decode($resp);
		return $validacion->valido;
	}
	function sc_verificar_codigo_pagina($id){
		//OBTENIENDO LAS PÁGINAS DE CAPTURAS CREADAS EN LA PLATAFORMA ----------------------------------------------------------------------------------
		global $wpdb;
		//VALIDACIÓN DE ENTORNO --------------------------------------------------------
		$sume_clientes_conf = $wpdb->prefix.'sume_clientes_conf';
		$sql = "SELECT sandbox FROM ".$sume_clientes_conf.";";
		$sandbox = $wpdb->get_results($sql, ARRAY_A);
		$sumeclientes = ( $sandbox[0]['sandbox'] == 1 )?'sume.space':'sumeclientes.net';
		//------------------------------------------------------------------------------
		//ESCAPEANDO RUTA PARA COMUNICAR CON API-----------------------------------------------------------------------------------------------
		$ruta = ('127.0.0.1' == $_SERVER['REMOTE_ADDR'])?'http://localhost/sume_clientes/public/WP/Pagina/':"https://$sumeclientes/WP/Pagina/";
		$ruta = $ruta.$id.'/HTML';
		$ruta = esc_url($ruta);
		//-------------------------------------------------------------------------------------------------------------------------------------
		$curl = curl_init();
		curl_setopt_array($curl, array(CURLOPT_RETURNTRANSFER => 1, CURLOPT_URL => $ruta, CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT']));
		$resp = curl_exec($curl);
		curl_close($curl);
		$HTML = json_decode($resp);
		//DESINFECTANDO RESULTADO -------------------------------------------------
		$codigo_html = esc_html(balanceTags(stripslashes_deep($HTML->PaginaHTML)));
		//-------------------------------------------------------------------------
		//----------------------------------------------------------------------------------------------------------------------------------------------
		//DESINFECTANDO VARIABLES DE POST
		$idP = sanitize_text_field($id);
		//-------------------------------
		//VERIFICO SI EL CAMPO DE CODIGO_HTML TIENE SOPORTE DE CARACTERES ESPECIALES ------------------------------------------------------------------------------------------
		try{
			$sume_clientes_paginas = $wpdb->prefix.'sume_clientes_paginas';
			$sql = "SHOW FULL COLUMNS FROM $sume_clientes_paginas LIKE 'codigo_html';";
			$tblPaginas = $wpdb->get_results($sql, ARRAY_A);
			if( $tblPaginas[0]['Collation'] == 'utf8_spanish_ci' ){
				$sql = "ALTER TABLE $sume_clientes_paginas CHANGE COLUMN `codigo_html` `codigo_html` LONGTEXT CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_unicode_ci' NOT NULL ;";
				$wpdb->query($sql);
			}
		}
		catch(Exception $e){
			error_log($e);
		}
		//---------------------------------------------------------------------------------------------------------------------------------------------------------------------
		//ACTUALIZANDO CÓDIGO DE PÁGINA DE CAPTURA -----------------------------------------------------------------------------------------------------
		$sume_clientes_paginas = $wpdb->prefix.'sume_clientes_paginas';
		$wpdbI = new wpdb(DB_USER, DB_PASSWORD, DB_NAME, DB_HOST);
		$wpdbI->set_charset($wpdbI->dbh, 'utf8mb4', 'utf8mb4_unicode_ci');
		$sql = $wpdbI->prepare("SELECT ID, codigo_html FROM $sume_clientes_paginas WHERE plantillasxclientes_id = %d;", array((int)$idP));
		$pagina = $wpdbI->get_results($sql, ARRAY_A);
		if($pagina[0]['codigo_html'] != $codigo_html){
			$wpdbI->update($sume_clientes_paginas, array('codigo_html' => $codigo_html), array('ID' => (int)$pagina[0]['ID']), array('%s'), array('%d'));
		}
		//----------------------------------------------------------------------------------------------------------------------------------------------
	}
	//-----------------------------------------------------------------------
?>
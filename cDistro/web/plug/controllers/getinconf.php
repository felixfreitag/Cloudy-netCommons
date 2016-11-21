<?php
//getinconf.php

$getinconf_file="/etc/getinconf-client.conf";

function index_get(){

	global $getinconf_file;
    $disabled = '';

    $page = "";
	$buttons = "";
	$submitButtons = "";

	$variables = load_conffile($getinconf_file);
	$page .= hlc(t("getinconf_title"));
	$page .= hl(t("getinconf_subtitle"),4);
    $page .= par(t("getinconf_description"));

    if (isUp($variables['NETWORK_NAME']))
        $disabled = "disabled";

    $page .= txt(t('getinconf_settings'));
	$page .= createForm(array('class'=>'form-horizontal'));
	$page .= addInput('GTC_SERVER_URL',t('getinconf_form_server_url'),$variables,array('type'=>'url', 'required'=>''),$disabled,t('getinconf_form_server_url_help'));
	$page .= addInput('NETWORK_NAME',t('getinconf_form_microcloud_network'),$variables,array('type'=>'text', 'required'=>''),$disabled,t('getinconf_form_microcloud_network_help'));
	$page .= addInput('NETWORK_KEY',t('getinconf_form_network_password'),$variables,array('type'=>'password', 'required'=>''),$disabled,t('getinconf_form_network_password_help'));
	$page .= addInput('INTERNAL_DEV',t('getinconf_form_community_network_device'),$variables,array('type'=>'text', 'required'=>''),$disabled,t('getinconf_form_community_network_device_help'));
	if (!isUp($variables['NETWORK_NAME']))
	   $submitButtons .= addSubmit(array('label'=>t('getinconf_button_save')));

    $page .= txt(t('getinconf_tinc_status'));
	if (isUp($variables['NETWORK_NAME'])){
		$page .= "<div class='alert alert-success text-center'>".t('getinconf_tinc_status_running')."</div>\n";
		$buttons .= addButton(array('label'=>t('getinconf_button_stop'),'class'=>'btn btn-danger','href'=>'getinconf/stop'));
		$buttons .= addButton(array('label'=>t('getinconf_button_interface'), 'href' => 'getinconf/interfaceStatus/'.$variables['NETWORK_NAME']));
	} else {
		$page .= "<div class='alert alert-error text-center'>".t('getinconf_tinc_status_stopped')."</div>\n";
		$buttons .= addButton(array('label'=>t('getinconf_button_start'),'class'=>'btn btn-success', 'href'=>'getinconf/start'));
		$buttons .= addButton(array('label'=>t('getinconf_button_uninstall'),'class'=>'btn btn-danger', 'href'=>'getinconf/uninstall'));
	}

    $page .= $buttons . $submitButtons;
	return(array('type' => 'render','page' => $page));
}

function index_post(){
	global $getinconf_file;
	global $staticFile;

	$pre = "#!/bin/sh\n\n# Automatically generate file with cGuifi\n";
	$post = "# POST=665\n# GETINCONF_IGNORE=1\n";

	//Check info!!!
	$datesToSave = array();
	foreach ($_POST as $key => $value) {
		//check($key,$value);
		$datesToSave[$key] = $value;
	}
	write_conffile($getinconf_file,$datesToSave,$pre,$post);

	setFlash(t('getinconf_alert_saved'),"success");
	setFlash(t('getinconf_alert_saved'),"success");
	return(array('type'=> 'redirect', 'url' => $staticFile.'/'.'getinconf'));
}

function start(){
	global $staticFile;

	execute_bg_shell('getinconf-client install');
	$page = "";

	$page .= hlc(t("getinconf_title"));
	$page .= hl(t("getinconf_subtitle"),4);


	$page .= "<div class='alert alert-warning'>".t('getinconf_alert_starting');
    $page .= "</div>";
	$page .= txt(t('getinconf_click_button_back'));
	$page .= addButton(array('label'=>t('getinconf_button_back'),'class'=>'btn btn-default', 'href'=>$staticFile.'/getinconf'));
	return(array('type'=>'render', 'page'=> $page));
	exit();
}

function stop(){
	global $staticFile;

	$r = execute_program('getinconf-client uninstall');
	// this getinconf-client must do it.
	execute_program_detached('rm -r /var/run/getinconf-client.md5.*');

	if ($r['return'] == 0)
		setFlash(t('getinconf_alert_stopping'), "warning");

	return(array('type'=> 'redirect', 'url' => $staticFile.'/'.'getinconf'));
}

function interfaceStatus(){
	global $Parameters,$staticFile;

    $page = "";
	$page .= hlc(t("getinconf_title"));
	$page .= hl(t("getinconf_subtitle_interface_status"),4);

	if (isset($Parameters) && isset($Parameters[0])){
	    $page .= txt(t('getinconf_interface_command_output_pre')."<strong>ip addr show dev ".$Parameters[0]."</strong>".t('getinconf_interface_command_output_post'));
		$r = execute_program_shell('ip addr show dev '.$Parameters[0]);

		$page .= "<div class='alert alert-warning'>";
		$page .= "<pre>";
		$page .= $r['output'];
		$page .= "</pre></div>";
		$page .= addButton(array('label'=>t('getinconf_button_back'),'class'=>'btn btn-default', 'href'=>$staticFile.'/getinconf'));
		return(array('type'=>'render', 'page'=> $page));
	}

    return(array('type'=> 'redirect', 'url' => $staticFile.'/'.'getinconf'));
}

function isUp($dev){
	$r = execute_program('ip addr show dev '.$dev);
	return ($r['return']==0);
}

function nothing(){
	$page = "";
	$page .= "<div class='alert alert-warning'>";
	$page .= t("Nothing to do.");
    $page .= "</div>";
    return(array('type'=>'render', 'page'=> $page));
}

function uninstall(){
	global $staticFile;
    execute_program_detached('getinconf-client uninstall');

   	if ($r['return'] == 0)
    	setFlash(t('getinconf_alert_uninstall'), "info");

    return(array('type'=> 'redirect', 'url' => $staticFile.'/'.'getinconf'));
}

?>
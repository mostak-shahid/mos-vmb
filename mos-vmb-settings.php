<?php
function mos_vmb_settings_init() {
	register_setting( 'mos_vmb', 'mos_vmb_options' );
	//add_settings_section('mos_vmb_section_top_nav', '', 'mos_vmb_section_top_nav_cb', 'mos_vmb');
	add_settings_section('mos_vmb_section_dash_start', '', 'mos_vmb_section_dash_start_cb', 'mos_vmb');
	add_settings_field( 'field_vat', __( 'Enable Marginal Vat', 'mos_vmb' ), 'mos_vmb_field_vat_cb', 'mos_vmb', 'mos_vmb_section_dash_start', [ 'label_for' => 'mos_vmb_enable', 'class' => 'mos_vmb_row', 'mos_vmb_custom_data' => 'custom', ] );
	add_settings_section('mos_vmb_section_dash_end', '', 'mos_vmb_section_end_cb', 'mos_vmb');

	/*
	add_settings_section('mos_vmb_section_scripts_start', '', 'mos_vmb_section_scripts_start_cb', 'mos_vmb');
	add_settings_field( 'field_jquery', __( 'JQuery', 'mos_vmb' ), 'mos_vmb_field_jquery_cb', 'mos_vmb', 'mos_vmb_section_scripts_start', [ 'label_for' => 'jquery', 'class' => 'mos_vmb_row', 'mos_vmb_custom_data' => 'custom', ] );
	add_settings_field( 'field_bootstrap', __( 'Bootstrap', 'mos_vmb' ), 'mos_vmb_field_bootstrap_cb', 'mos_vmb', 'mos_vmb_section_scripts_start', [ 'label_for' => 'bootstrap', 'class' => 'mos_vmb_row', 'mos_vmb_custom_data' => 'custom', ] );
	add_settings_field( 'field_css', __( 'Custom Css', 'mos_vmb' ), 'mos_vmb_field_css_cb', 'mos_vmb', 'mos_vmb_section_scripts_start', [ 'label_for' => 'mos_vmb_css' ] );
	add_settings_field( 'field_js', __( 'Custom Js', 'mos_vmb' ), 'mos_vmb_field_js_cb', 'mos_vmb', 'mos_vmb_section_scripts_start', [ 'label_for' => 'mos_vmb_js' ] );
	add_settings_section('mos_vmb_section_scripts_end', '', 'mos_vmb_section_end_cb', 'mos_vmb');
	*/

}
add_action( 'admin_init', 'mos_vmb_settings_init' );

function get_mos_vmb_active_tab () {
	$output = array(
		'option_prefix' => admin_url() . "/options-general.php?page=mos_vmb_settings&tab=",
		//'option_prefix' => "?post_type=p_file&page=mos_vmb_settings&tab=",
	);
	if (isset($_GET['tab'])) $active_tab = $_GET['tab'];
	elseif (isset($_COOKIE['plugin_active_tab'])) $active_tab = $_COOKIE['plugin_active_tab'];
	else $active_tab = 'dashboard';
	$output['active_tab'] = $active_tab;
	return $output;
}
function mos_vmb_section_top_nav_cb( $args ) {
	$data = get_mos_vmb_active_tab ();
	?>
    <ul class="nav nav-tabs">
        <li class="tab-nav <?php if($data['active_tab'] == 'dashboard') echo 'active';?>"><a data-id="dashboard" href="<?php echo $data['option_prefix'];?>dashboard">Dashboard</a></li>
        <li class="tab-nav <?php if($data['active_tab'] == 'scripts') echo 'active';?>"><a data-id="scripts" href="<?php echo $data['option_prefix'];?>scripts">Advanced CSS, JS</a></li>
    </ul>
	<?php
}
function mos_vmb_section_dash_start_cb( $args ) {
	$data = get_mos_vmb_active_tab ();
  global $mos_vmb_options;
	?>
	<div id="mos-vmb-dashboard" class="tab-con <?php if($data['active_tab'] == 'dashboard') echo 'active';?>">
		<?php //var_dump($mos_vmb_options) ?>

	<?php
}
function mos_vmb_section_scripts_start_cb( $args ) {
	$data = get_mos_vmb_active_tab ();
	?>
	<div id="mos-vmb-scripts" class="tab-con <?php if($data['active_tab'] == 'scripts') echo 'active';?>">
	<?php
}
function mos_vmb_field_vat_cb( $args ) {
	global $mos_vmb_options;
	?>
	<label for="<?php echo esc_attr( $args['label_for'] ); ?>"><input name="mos_vmb_options[<?php echo esc_attr( $args['label_for'] ); ?>]" type="checkbox" id="<?php echo esc_attr( $args['label_for'] ); ?>" value="1" <?php echo isset( $mos_vmb_options[ $args['label_for'] ] ) ? ( checked( $mos_vmb_options[ $args['label_for'] ], 1, false ) ) : ( '' ); ?>><?php esc_html_e( 'Yes I like to enable Marginal Vat.', 'mos_vmb' ); ?></label>
	<?php
}
function mos_vmb_field_jquery_cb( $args ) {
	global $mos_vmb_options;
	?>
	<label for="<?php echo esc_attr( $args['label_for'] ); ?>"><input name="mos_vmb_options[<?php echo esc_attr( $args['label_for'] ); ?>]" type="checkbox" id="<?php echo esc_attr( $args['label_for'] ); ?>" value="1" <?php echo isset( $mos_vmb_options[ $args['label_for'] ] ) ? ( checked( $mos_vmb_options[ $args['label_for'] ], 1, false ) ) : ( '' ); ?>><?php esc_html_e( 'Yes I like to add JQuery from Plugin.', 'mos_vmb' ); ?></label>
	<?php
}
function mos_vmb_field_bootstrap_cb( $args ) {
	global $mos_vmb_options;
	?>
	<label for="<?php echo esc_attr( $args['label_for'] ); ?>"><input name="mos_vmb_options[<?php echo esc_attr( $args['label_for'] ); ?>]" type="checkbox" id="<?php echo esc_attr( $args['label_for'] ); ?>" value="1" <?php echo isset( $mos_vmb_options[ $args['label_for'] ] ) ? ( checked( $mos_vmb_options[ $args['label_for'] ], 1, false ) ) : ( '' ); ?>><?php esc_html_e( 'Yes I like to add JQuery from Plugin.', 'mos_vmb' ); ?></label>
	<?php
}
function mos_vmb_field_css_cb( $args ) {
	global $mos_vmb_options;
	?>
	<textarea name="mos_vmb_options[<?php echo esc_attr( $args['label_for'] ); ?>]" id="<?php echo esc_attr( $args['label_for'] ); ?>" rows="10" class="regular-text"><?php echo isset( $mos_vmb_options[ $args['label_for'] ] ) ? esc_html_e($mos_vmb_options[$args['label_for']]) : '';?></textarea>
	<script>
    var editor = CodeMirror.fromTextArea(document.getElementById("mos_vmb_css"), {
      lineNumbers: true,
      mode: "text/css",
      extraKeys: {"Ctrl-Space": "autocomplete"}
    });
	</script>
	<?php
}
function mos_vmb_field_js_cb( $args ) {
	global $mos_vmb_options;
	?>
	<textarea name="mos_vmb_options[<?php echo esc_attr( $args['label_for'] ); ?>]" id="<?php echo esc_attr( $args['label_for'] ); ?>" rows="10" class="regular-text"><?php echo isset( $mos_vmb_options[ $args['label_for'] ] ) ? esc_html_e($mos_vmb_options[$args['label_for']]) : '';?></textarea>
	<script>
    var editor = CodeMirror.fromTextArea(document.getElementById("mos_vmb_js"), {
      lineNumbers: true,
      mode: "text/css",
      extraKeys: {"Ctrl-Space": "autocomplete"}
    });
	</script>
	<?php
}
function mos_vmb_section_end_cb( $args ) {
	$data = get_mos_vmb_active_tab ();
	?>
	</div>
	<?php
}


function mos_vmb_options_page() {
	//add_menu_page( 'WPOrg', 'WPOrg Options', 'manage_options', 'mos_vmb', 'mos_vmb_options_page_html' );
	add_submenu_page( 'options-general.php', 'Settings', 'Settings', 'manage_options', 'mos_vmb_settings', 'mos_vmb_admin_page' );
}
add_action( 'admin_menu', 'mos_vmb_options_page' );

function mos_vmb_admin_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	if ( isset( $_GET['settings-updated'] ) ) {
		add_settings_error( 'mos_vmb_messages', 'mos_vmb_message', __( 'Settings Saved', 'mos_vmb' ), 'updated' );
	}
	settings_errors( 'mos_vmb_messages' );
	?>
	<div class="wrap mos-vmb-wrapper">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<form action="options.php" method="post">
		<?php
		settings_fields( 'mos_vmb' );
		do_settings_sections( 'mos_vmb' );
		submit_button( 'Save Settings' );
		?>
		</form>
	</div>
	<?php
}
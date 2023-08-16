<?php
// Simple Registration

add_action( 'admin_menu', 'simple_registration_func' );
 
function simple_registration_func(){
 
	add_submenu_page(
		'options-general.php',
		__( 'Simple Registration Settings', 'woocommerce-simple-registration' ),
		'Simple Registration', 
		'manage_options', 
		'simple_registration',
		'simple_registration_page_callback',
		7
	);
}
 
function simple_registration_page_callback(){
	?>
	<form method="post" action="options.php">
		
		<?php
			settings_fields( 'simple_registration' );
			do_settings_sections( 'simple_registration' );
			submit_button();
		?>

	</form>
	<?php
}

add_action( 'admin_init', 'registration_settings_init' );

function registration_settings_init() {

    add_settings_section(
        'simple_page_setting_section',
        __( 'Simple Registration Settings', 'woocommerce-simple-registration' ),
        '',
        'simple_registration'
    );

	add_settings_field(
		'select_role_registration',
		__( 'Select Display Roles', 'woocommerce-simple-registration' ),
		'registration_roles_func',
		'simple_registration',
		'simple_page_setting_section'
	);

	register_setting( 'simple_registration', 'select_role_registration' );
}

function registration_roles_func() {
	?>
	<select	id="select_role_registration" class="settings-role-select regular-text select_role_registration" name="select_role_registration[]" multiple="multiple" placeholder="Choose role">
		<?php
			global $wp_roles;
			$selected_page = get_option( 'select_role_registration', array() );
			if ( ! is_array( $selected_page ) ) {
				$selected_page = (array) $selected_page;
			}
			foreach ( $wp_roles->roles as $key => $value ) {
				echo '<option value="' . esc_attr( $value['name'] ) . '" ' . selected( in_array( $value['name'] , $selected_page, true ) ? $value['name']   : null, $value['name']  , false ) . '>' . esc_html( $value['name'] ) . '</option>';
			}
		?>
	</select>
    <?php
}

<?php
// Simple Registration

add_action( 'admin_menu', 'simple_registration_func' );
 
function simple_registration_func(){
    // Add a top-level menu with a counter
    $role_requests_menu_title =   __( 'Role Requests', 'woocommerce-simple-registration' );
    $role_requests_counter = get_role_requests_item_count(); // Replace this with your logic to get the count
    $role_requests_menu_title_with_counter = $role_requests_menu_title . " <span class='update-plugins count-$role_requests_counter'><span class='plugin-count'>$role_requests_counter</span></span>";
	
	add_menu_page(
        __( 'Simple Registration Settings', 'woocommerce-simple-registration' ), // Page title
        __( 'Simple Registration', 'woocommerce-simple-registration' ),                                                 // Menu title
        'manage_options',                                                      // Capability
        'simple_registration',                                                 // Menu slug
        'simple_registration_page_callback',                                   // Callback function
        'dashicons-admin-users',                                               // Icon (you can replace with any dashicon)
        7                                                                      // Position in the menu
    );
	
	add_submenu_page(
        'simple_registration',          // Parent slug (slug of the top-level menu)
        __( 'Role Requests', 'woocommerce-simple-registration' ),                // Page title
        $role_requests_menu_title_with_counter,           
        'manage_options',               // Capability
        'role-requests',                // Menu slug
        'display_role_requests_page'    // Callback function
    );
	
}
function get_role_requests_item_count() {
    // Example logic to get the count
    $role_requests_item_count = count(get_option('role_requests', []));
    return $role_requests_item_count;
	
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
// Function to save role input into options table
function save_role_request($user_id, $input_role) {
    $requests = get_option('role_requests', []); // Fetch existing requests
    if (!is_array($requests)) {
        $requests = [];
    }
	
	$user = get_userdata($user_id);
	$current_role = isset($user->roles[0]) ? $user->roles[0] : '';	
	$userEmail = isset($user->data->user_email) ? $user->data->user_email : '';	
	$user_login  = isset($user->data->user_login) ? $user->data->user_login : '';	
	
    $new_request = [
        'id_user' => $user_id,
        'login_user' => $user_login,
        'email_id_user' => $userEmail,
        'current_role' => $current_role,
        'input_role' => $input_role,
        'status' => -1
    ];
	$new_request = sanitize_array_keys($new_request);
    $requests[] = $new_request;
    update_option('role_requests', $requests); // Save updated requests
}

function sanitize_array_keys($array) {
    $sanitized_array = [];
    foreach ($array as $key => $value) {
        // Remove special characters from the key
        $sanitized_key = preg_replace('/[^a-zA-Z0-9_]/', '', $key);
        $sanitized_array[$sanitized_key] = $value;
    }
    return $sanitized_array;
}
// Function to display the admin page
function display_role_requests_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    // Handle admin actions
    if (isset($_POST['action']) && isset($_POST['request_index'])) {
        $requests = get_option('role_requests', []);
        $index = intval($_POST['request_index']);

        if ($_POST['action'] === 'approve') {
            $requests[$index]['status'] = 1;
			$userID  = $requests[$index]['id_user'];
			$requestedRole  = $requests[$index]['input_role'];
			$user_id = wp_update_user( array( 'ID' => $userID, 'role' => $requestedRole ) );
            unset($requests[$index]);			
        } elseif ($_POST['action'] === 'reject') {
            $requests[$index]['status'] = 0;
			unset($requests[$index]);	
        }
		
        update_option('role_requests', $requests);
    }

    $requests = get_option('role_requests', []);

    echo '<div class="wrap">';
    echo '<h1>'.__( 'Role Requests', 'woocommerce-simple-registration' ).'</h1>';

    echo '<table class="wp-list-table widefat fixed striped">';
    echo '<thead>
			<tr>
				<th>'.__( 'User ID', 'woocommerce-simple-registration' ).'</th>
				<th>'.__( 'User Name', 'woocommerce-simple-registration' ).'</th>
				<th>'.__( 'User Email', 'woocommerce-simple-registration' ).'</th>
				<th>'.__( 'Current Role', 'woocommerce-simple-registration' ).'</th>
				<th>'.__( 'Requested Role', 'woocommerce-simple-registration' ).'</th>
				<th>'.__( 'Status', 'woocommerce-simple-registration' ).'</th>
				<th>'.__( 'Actions', 'woocommerce-simple-registration' ).'</th>
			</tr>
		</thead>';
    echo '<tbody>';

    if (empty($requests)) {
		
		$request = __( 'No role requests found.', 'woocommerce-simple-registration' );
		echo '<tr><td colspan="6">'. esc_attr( $request ).'</td></tr>';
		
    }else{
		$r = 1;
		
		foreach ($requests as $index => $request) {
			
			$status_text = __( 'Pending For Approval', 'woocommerce-simple-registration' );
			
			if ($request['status'] === 1) {
				$status_text = __( 'Approved', 'woocommerce-simple-registration' );
			} elseif ($request['status'] === 0) {
				$status_text = __( 'Rejected', 'woocommerce-simple-registration' );
			}

			echo '<tr>';
			echo '<td>' . esc_html($r) . '</td>';
			echo '<td>' . esc_html($request["login_user"]) . '</td>';
			echo '<td>' . esc_html($request["email_id_user"]) . '</td>';
			echo '<td>' . esc_html(ucfirst($request["current_role"])) . '</td>';
			echo '<td>' . esc_html(ucfirst($request["input_role"])) . '</td>';
			echo '<td>' . esc_html($status_text) . '</td>';
			echo '<td>';
			if ($request['status'] === -1) {
				echo '<form method="POST" style="display:inline-block;">';
				echo '<input type="hidden" name="request_index" value="' . esc_attr($index) . '">';
				echo '<button name="action" value="approve" class="button button-primary">'.__( 'Approve', 'woocommerce-simple-registration' ).'</button> ';
				echo '<button name="action" value="reject" class="button button-secondary">'.__( 'Reject', 'woocommerce-simple-registration' ).'</button>';
				echo '</form>';
			}
			echo '</td>';
			echo '</tr>';
			$r++;
		}
	}

    echo '</tbody>';
    echo '</table>';
    echo '</div>';
}
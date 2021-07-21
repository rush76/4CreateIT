<?php
/**
 * @package EGR CONSUMER MANAGMENT SYSTEM
 * @version 1.7.2
 */
/*
Plugin Name: EGR CONSUMER MANAGMENT SYSTEM
Plugin Name: EGR Consumer Registration Form
Plugin URI: http://energogazrezerv.com.ua
Description: Сustom registration form with fields defined in plugin code
Version: 1.0
Author: Ruslan Shylov
Author URI: rus.shilov@gmail.com
*/

// set def DateTimeZone
date_default_timezone_set('Europe/Kiev');



function wph_admin_footer_text () {
  echo 'СКС v 1.1.a. Розробник: <a href="skype:rushlv?chat">Руслан Шилов</a> <br><br>';
  $kk = 'Консультант: <a href=skype:nastena_popil?chat">Анастасія Попіль</a>';
}
add_filter('admin_footer_text', 'wph_admin_footer_text');


// prepare:  removing default widgets //

// disable autoupdates
function remove_core_updates(){
global $wp_version;return(object) array('last_checked'=> time(),'version_checked'=> $wp_version,);
}
add_filter('pre_site_transient_update_core','remove_core_updates');
add_filter('pre_site_transient_update_plugins','remove_core_updates');
add_filter('pre_site_transient_update_themes','remove_core_updates');

function remove_dashboard_widgets() {
    global $wp_meta_boxes;
    $UD = get_userdata(get_current_user_id());
    if ($UD->roles[0] == "consumer" || $UD->roles[0] == "cms_manager") {

    unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press']);
    unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_incoming_links']);
    unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_activity']);
    unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now']);
    unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins']);
    unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_drafts']);
    unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_comments']);
    unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);
    unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary']);
  }
}
add_action('wp_dashboard_setup', 'remove_dashboard_widgets' );

//dump data
function DD ($var) { echo "<pre>"; var_dump($var); echo "</pre>";}

// allow upload PDF
function my_myme_types($mime_types){
    $mime_types['pdf'] = 'application/pdf';
    return $mime_types;
}
add_filter('upload_mimes', 'my_myme_types', 1, 1);


//add css
function admin_style() {
  wp_enqueue_style('admin-styles', plugin_dir_url(__FILE__).'egr_consumer.css');
}
// add js

function admin_js() {
  wp_enqueue_script('my_custom_script', plugin_dir_url(__FILE__) . 'egr_consumer.js');
}

add_action('admin_enqueue_scripts', 'admin_style');
add_action('admin_enqueue_scripts', 'admin_js');


//1// load translate for plugin
add_action( 'plugins_loaded', 'rs_load_plugin_textdomain' );

function rs_load_plugin_textdomain() {
 load_plugin_textdomain( 'egr-consumer', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}


//2// add custom fields to basic regform
add_action( 'register_form', 'EGR_consumer_register_form' );

function EGR_consumer_register_form() {
  ?>
  <p>
      <label for="firm_name"><?php _e('Firm Name', 'egr-consumer') ?><br />
          <input type="text" name="firm_name" id="firm_name" class="input tmpsave" autocomplete="on" value="<?php echo esc_attr( wp_unslash( $firm_name ) ); ?>" size="25" /></label>
  </p>
  <p>
      <label for="EDRPOU"><?php _e('EDRPOU', 'egr-consumer') ?><br />
          <input type="text" name="EDRPOU" id="EDRPOU" class="input tmpsave" value="<?php echo esc_attr( wp_unslash( $EDRPOU ) ); ?>" size="25" /></label>
  </p>
  <p>
      <label for="Official_Address"><?php _e('Official Address', 'egr-consumer') ?><br />
          <input type="text" name="Official_Address" id="Official_Address" class="input tmpsave" value="<?php echo esc_attr( wp_unslash( $Official_Address ) ); ?>" size="25" /></label>
  </p>
  <p>
      <label for="Postal_Address"><?php _e('Postal Address', 'egr-consumer') ?><br />
          <input type="text" name="Postal_Address" id="Postal_Address" class="input tmpsave" value="<?php echo esc_attr( wp_unslash( $Postal_Address ) ); ?>" size="25" /></label>
  </p>

  <h3><b><?php _e('Contact Person', 'egr-consumer') ?></b></h3><br />

  <p>
      <label for="First Name"><?php _e('First Name', 'egr-consumer') ?><br />
          <input type="text" name="First_Name" id="First_Name" class="input tmpsave" value="<?php echo esc_attr( wp_unslash( $First_Name ) ); ?>" size="25" /></label>
  </p>
  <p>
      <label for="Last_Name"><?php _e('Last Name', 'egr-consumer') ?><br />
          <input type="text" name="Last_Name" id="Last_Name" class="input tmpsave" value="<?php echo esc_attr( wp_unslash( $Last_Name ) ); ?>" size="25" /></label>
  </p>
  <p>
      <label for="Phone Number"><?php _e('Phone Number', 'egr-consumer') ?><br />
          <input type="text" name="Phone_Number" id="Phone_Number" class="input tmpsave" value="<?php echo esc_attr( wp_unslash( $Phone_Number ) ); ?>" size="25" /></label>
  </p>
  <p class="cd_link">
    <a href="#" id="clearData"><?php _e('clear fields', 'egr-consumer') ?></a><br>
  </p>
<?php
}

//3// Check required fields
add_filter( 'registration_errors', 'rs_registration_errors', 10, 9 );

function rs_registration_errors( $errors, $sanitized_user_login, $user_email ) {

    if ( empty( $_POST['firm_name'] ) || !empty( $_POST['firm_name'] ) && trim( $_POST['firm_name'] ) == '' ) {
        $errors->add( 'firm_name_error', __( 'You must write a firm name', 'egr-consumer' ) );
    }

    if ( empty( $_POST['EDRPOU'] ) || !empty( $_POST['EDRPOU'] ) && trim( $_POST['EDRPOU'] ) == '' ) {
        $errors->add( 'EDRPOU_error', __( 'You must write a EDRPOU', 'egr-consumer' ) );
    }

    if ( empty( $_POST['Official_Address'] ) || !empty( $_POST['Official_Address'] ) && trim( $_POST['Official_Address'] ) == '' ) {
        $errors->add( 'Official_Address', __( 'You must write a Official Address', 'egr-consumer' ) );
    }
    if ( empty( $_POST['Postal_Address'] ) || !empty( $_POST['Postal_Address'] ) && trim( $_POST['Postal_Address'] ) == '' ) {
        $errors->add( 'Postal_Address', __( 'You must write a Postal Address', 'egr-consumer' ) );
    }
    if ( empty( $_POST['First_Name'] ) || !empty( $_POST['First_Name'] ) && trim( $_POST['First_Name'] ) == '' ) {
        $errors->add( 'First_Name', __( 'You must write a First Name', 'egr-consumer' ) );
    }
    if ( empty( $_POST['Last_Name'] ) || !empty( $_POST['Last_Name'] ) && trim( $_POST['Last_Name'] ) == '' ) {
        $errors->add( 'Last_Name', __( 'You must write a Last Name', 'egr-consumer' ) );
    }
    if ( empty( $_POST['Phone_Number'] ) || !empty( $_POST['Phone_Number'] ) && trim( $_POST['Phone_Number'] ) == '' ) {
        $errors->add( 'Phone_Number', __( 'You must write a Phone Number', 'egr-consumer' ) );
    }
    return $errors;
}

//4// register new user and store his custom data
add_action( 'user_register', 'rs_user_register' );
function rs_user_register ( $user_id ) {
  if (!empty($_POST['firm_name']))        { update_user_meta( $user_id, 'firm_name', trim($_POST['firm_name'])); }
  if (!empty($_POST['EDRPOU']))           { update_user_meta( $user_id, 'EDRPOU', trim($_POST['EDRPOU'])); }
  if (!empty($_POST['Official_Address'])) { update_user_meta( $user_id, 'Official_Address', trim($_POST['Official_Address'])); }
  if (!empty($_POST['Postal_Address']))   { update_user_meta( $user_id, 'Postal_Address', trim($_POST['Postal_Address'])); }
  if (!empty($_POST['First_Name']))       { update_user_meta( $user_id, 'First_Name', trim($_POST['First_Name'])); }
  if (!empty($_POST['Last_Name']))        { update_user_meta( $user_id, 'Last_Name', trim($_POST['Last_Name']));  }
  if (!empty($_POST['Phone_Number']))     { update_user_meta( $user_id, 'Phone_Number', trim( $_POST['Phone_Number']));  }

}

//5//  show consumer profile fields when edit

add_action( 'edit_user_profile', 'custom_user_profile_fields', 10, 1 );
add_action( 'show_user_profile', 'custom_user_profile_fields', 10, 1 );
function custom_user_profile_fields( $profileuser ) {
?>
	<table class="form-table">
    <tr>
			<td colspan="2">
				<h3><?php esc_html_e( 'Consumer additional data', 'egr-consumer' ); ?></h3>
			</td>
		</tr>
    <tr>
			<th><label for="user_location"><?php _e( 'Firm Name','egr-consumer'); ?></label></th>
			<td>
        <input type="text" name="firm_name" id="firm_name" value="<?php echo esc_attr( get_the_author_meta( 'firm_name', $profileuser->ID ) ); ?>" class="regular-text" />
				<!-- span class="description"><!- ?php esc_html_e( 'Your location.', 'text-domain' ); ?></span -->
			</td>
		</tr>
    <tr>
			<th><label for="user_location"><?php _e( 'EDRPOU','egr-consumer'); ?></label></th>
			<td>
				<input type="text" name="EDRPOU" id="EDRPOU" value="<?php echo esc_attr( get_the_author_meta( 'EDRPOU', $profileuser->ID ) ); ?>" class="regular-text" />
			</td>
		</tr>
    <tr>
			<th><label for="user_location"><?php _e( 'Official Address','egr-consumer'); ?></label></th>
			<td>
				<input type="text" name="Official_Address" id="Official_Address" value="<?php echo esc_attr( get_the_author_meta( 'Official_Address', $profileuser->ID ) ); ?>" class="regular-text" />
			</td>
		</tr>
    <tr>
			<th><label for="user_location"><?php _e( 'Postal Address','egr-consumer'); ?></label></th>
			<td>
				<input type="text" name="Postal_Address" id="Postal_Address" value="<?php echo esc_attr( get_the_author_meta( 'Postal_Address', $profileuser->ID ) ); ?>" class="regular-text" />
			</td>
		</tr>
    <tr>
			<th><label for="user_location"><?php _e( 'Phone Number','egr-consumer'); ?></label></th>
			<td>
				<input type="text" name="Phone_Number" id="Phone_Number" value="<?php echo esc_attr( get_the_author_meta( 'Phone_Number', $profileuser->ID ) ); ?>" class="regular-text" />
			</td>
		</tr>
	</table>
<?php
}


//6//  update consumer custom profile fields
add_action('profile_update', 'rs_update_consumer_meta', 10, 1);
function rs_update_consumer_meta($user_id){
  var_dump($profileuser);

  if (!empty($_POST['EDRPOU']))           { update_user_meta( $user_id, 'EDRPOU', trim($_POST['EDRPOU'])); }
  if (!empty($_POST['firm_name']))        { update_user_meta( $user_id, 'firm_name', trim($_POST['firm_name'])); }
  if (!empty($_POST['Official_Address'])) { update_user_meta( $user_id, 'Official_Address', trim($_POST['Official_Address'])); }
  if (!empty($_POST['Postal_Address']))   { update_user_meta( $user_id, 'Postal_Address', trim($_POST['Postal_Address'])); }
  if (!empty($_POST['First_Name']))       { update_user_meta( $user_id, 'First_Name', trim($_POST['First_Name'])); }
  if (!empty($_POST['Last_Name']))        { update_user_meta( $user_id, 'Last_Name', trim($_POST['Last_Name']));  }
  if (!empty($_POST['Phone_Number']))     { update_user_meta( $user_id, 'Phone_Number', trim( $_POST['Phone_Number']));  }

}

//7// remove basic menu
// add_action('admin_menu', 'remove_menus');
 function remove_menus(){

  $UD = get_userdata(get_current_user_id());
  if ($UD->roles[0] == "consumer" || $UD->roles[0] == "cms_manager") {
   	global $menu;
   	$restricted = array(
   		__('Dashboard'),
   		__('Posts'),
   		__('Media'),
   		__('Links'),
   		__('Pages'),
   		__('Appearance'),
   		__('Tools'),
   		__('Users'),
   		__('Settings'),
   		__('Comments'),
   		__('Plugins')
   	);
   	end ($menu);
   	while (prev($menu)){
   		$value = explode(' ', $menu[key($menu)][0]);
   		if( in_array( ($value[0] != NULL ? $value[0] : "") , $restricted ) ){ unset($menu[key($menu)]); }
   	}
  }

 }


//8// make new menu for consumers

add_action('admin_menu', function(){
  if (current_user_can('gas_dashboard')){
    add_menu_page( 'ГАЗОПОСТАЧАННЯ'     , 'ГАЗ'       , 'gas_dashboard', 'gas'   , 'add_gas_dashboard', '', 2);
  }

  if (current_user_can('ee_dashboard')){
    add_menu_page( 'ЕЛЕКТРИЧНА ЕНЕРГІЯ' , 'ЕЛ.ЕНЕРГІЯ', 'ee_dashboard', 'ele', 'add_ee_dashboard', '', 3);

  }
  if (current_user_can('manage_contracts')) {
    add_menu_page('КОНТРАКТИ','КОНТРАКТИ', 'manage_contracts', 'contracts', 'add_contracts_dashboard', '', 4);
  }

} );



function add_contracts_dashboard() {
  require('contracts_dashboard.php');
}

function add_gas_dashboard(){
  require('gas_dashboard.php');
}

function add_ee_dashboard(){
   require('ele_dashboard.php');
}



function get_user_role($uid) {
  $user_meta = get_usermeta($uid);
  return array_keys($user_meta[11]);
}


// Регистрация виджета консоли

  add_action( 'wp_dashboard_setup', 'add_dashboard_widgets' );



function add_dashboard_widgets() {
  if (current_user_can('gas_dashboard')){
    wp_add_dashboard_widget( 'LGC', 'Активні Контракти: ГАЗОПОСТАЧАННЯ', 'show_gas_contracts' ); // LGC - last gas contracts
    wp_add_dashboard_widget( 'LEC', 'Активні Контракти: ЕЛЕКТРОЕНЕРГІЯ', 'show_ele_contracts' );// LEC - last el.energy contracts
				wp_add_dashboard_widget( 'CPE', 'Поточні ціни споживання', 'show_curprices' );  // CPE - current customers price for electric energy
				wp_add_dashboard_widget( 'FACT', 'Споживання за попередні періоди:', 'show_facts' );  // CPE - current customers price for electric energy
				wp_add_dashboard_widget( 'CHGINFO', 'УВАГА!', 'show_important_info' );  // CPE - current customers price for electric energy


   }
}

function show_important_info(){
		echo '<p style="color:#931; font-size:1.5em"><b>Заява на перехід до іншого постачальника має бути подана НЕ ПІЗНІШЕ ніж за 21 день до закінчення розрахункового періоду<b></p>';
}

function  show_facts() {
	global $wpdb;

		// get active contract by user_register
		 $q = 'SELECT internal_id, contract_type, consumer_contract_number FROM consumers_contracts WHERE consumer_id='.get_current_user_id();
		 $contracts = $wpdb->get_results($q);

			foreach ($contracts as $contract) {
//					DD($contract);

					echo '<strong>№ контракту:&nbsp;'.$contract->consumer_contract_number." | Тип: ".__($contract->contract_type,'egr-consumer')."</strong><br>";

					$q =  'SELECT * FROM consumer_contract_facts WHERE contract_id='.$contract->internal_id.' ORDER BY fact_submission_date DESC';
					$res = $wpdb->get_results($q);
					foreach ($res as $facts) {
						echo $facts->fact_capacity;
						if ($contract->contract_type == 'gaz') { echo ' куб.м'."<br>";	} else {echo ' кВт'."<br>"; }
					}
			}


} // 	$facts_ele = $wpdb->get_results('SELECT * FROM consumer_contract_facts WHERE user-')



// show current customers price for e/e
function show_curprices () {
	$cpe = get_user_meta(get_current_user_id(), 'curprice_ele');
	$cpg = get_user_meta(get_current_user_id(), 'curprice_gaz');

echo "<h3>Поточна ціна на Е/Е: <b>".$cpe[0]."</b>&nbsp;грн/кВт</h3><br>";
echo "<h3>Поточна ціна на ГАЗ: <b>".$cpg[0]."</b>&nbsp;грн/ 1000 м.куб.</h3><br>";
}


// Выводит контент
function show_gas_contracts( $post, $callback_args ) {
  global $wpdb;
  $icons_path = plugin_dir_url(__FILE__)."icons/";
  $cur_role = get_user_role(get_current_user_id());
  if (in_array('consumer', $cur_role)) {
    if ($cur_role[0] == 'consumer') {
      $where = 'WHERE contract_type="gas" AND contract_end_date > CURDATE() AND consumer_id='.get_current_user_id();
    } else {
      $where ='WHERE contract_type="gas" AND contract_end_date > CURDATE()';
    }
  }

  $query = 'SELECT * FROM consumers_contracts '.$where.' ORDER BY contract_end_date DESC';

  $contracts = $wpdb->get_results($query);

  $rows ='<div class="dbrd_clm dbrd_ctr_num dbrd_row_hdr">Номер контракту</div><div class="dbrd_clm dbrd_ctr_date dbrd_row_hdr">Дата початку</div><div class="dbrd_clm dbrd_ctr_date dbrd_row_hdr"> Дата закінчення</div><br><div class="dbrd_clm dbrd_row_hdr">План</div><div class="dbrd_clm dbrd_row_hdr">Кориг.</div><div class="dbrd_clm dbrd_row_hdr">Факт</div><div class="dbrd_clm dbrd_row_hdr">Рах.</div><br>';
  foreach ($contracts as $contract) {

    $rows .= '<a id="dbrd_a" href="?page=editcontract_form&internal_id='.$contract->internal_id.'" title="редагувати контракт"><div class="dbrd_clm dbrd_ctr_num">'.$contract->consumer_contract_number.'</div>';
    $rows .= '<div class="dbrd_clm dbrd_ctr_date">'.date("d.m.Y", strtotime($contract->contract_start_date)).'</div>';
    $rows .= '<div class="dbrd_clm dbrd_ctr_date">'.date("d.m.Y", strtotime($contract->contract_end_date)).'</div></a>&nbsp;';
    $rows .= '<div class="dbrd_clm "><a href="?page=gas&action=addplan_form" title="подати планове споживання"><img src="'.$icons_path.'correction_plan.png"></a></div>&nbsp;';
    $rows .= '<div class="dbrd_clm "><a href="?page=gas&action=addcorr_form" title="подати коригування"><img src="'.$icons_path.'correction_plan.png"></a></div>';
    $rows .= '<div class="dbrd_clm "><a href="?page=gas&action=addfact_form" title="подати фактичне споживання"><img src="'.$icons_path.'upload_fact.png"></a></div>';
    $rows .= '<div class="dbrd_clm "><a href="?page=gas&action=getinvoice_form" title="створити запит на рахунок"><img src="'.$icons_path.'correction_plan.png"></a></div><br>';

  }
  echo $rows;
}




function show_ele_contracts( $post, $callback_args ) {
  global $wpdb;
  $icons_path = plugin_dir_url(__FILE__)."icons/";
  $cur_role = get_user_role(get_current_user_id());
  if (in_array('consumer', $cur_role)) {
    if ($cur_role[0] == 'consumer') {
      $where = 'WHERE contract_type="ele" AND contract_end_date > CURDATE() AND consumer_id='.get_current_user_id();
    } else {
      $where ='WHERE contract_type="ele" AND contract_end_date > CURDATE()';
    }
  }

  $query = 'SELECT * FROM consumers_contracts '.$where.' ORDER BY contract_end_date DESC';

  $contracts = $wpdb->get_results($query);

  $rows ='<div class="dbrd_clm dbrd_ctr_num dbrd_row_hdr">Номер контракту</div><div class="dbrd_clm dbrd_ctr_date dbrd_row_hdr">Дата початку</div><div class="dbrd_clm dbrd_ctr_date dbrd_row_hdr"> Дата закінчення</div><br><div class="dbrd_clm dbrd_row_hdr">План</div><div class="dbrd_clm dbrd_row_hdr">Кориг.</div><div class="dbrd_clm dbrd_row_hdr">Факт</div><div class="dbrd_clm dbrd_row_hdr">Рах.</div><br>';
  foreach ($contracts as $contract) {

    $rows .= '<a id="dbrd_a" href="?page=editcontract_form&internal_id='.$contract->internal_id.'" title="редагувати контракт"><div class="dbrd_clm dbrd_ctr_num">'.$contract->consumer_contract_number.'</div>';
    $rows .= '<div class="dbrd_clm dbrd_ctr_date">'.date("d.m.Y", strtotime($contract->contract_start_date)).'</div>';
    $rows .= '<div class="dbrd_clm dbrd_ctr_date">'.date("d.m.Y", strtotime($contract->contract_end_date)).'</div></a>&nbsp;';
    $rows .= '<div class="dbrd_clm "><a href="?page=gas&action=addplan_form" title="подати планове споживання"><img src="'.$icons_path.'correction_plan.png"></a></div>&nbsp;';
    $rows .= '<div class="dbrd_clm "><a href="?page=gas&action=addcorr_form" title="подати коригування"><img src="'.$icons_path.'correction_plan.png"></a></div>';
    $rows .= '<div class="dbrd_clm "><a href="?page=gas&action=addfact_form" title="подати фактичне споживання"><img src="'.$icons_path.'upload_fact.png"></a></div>';
    $rows .= '<div class="dbrd_clm "><a href="?page=gas&action=getinvoice_form" title="створити запит на рахунок"><img src="'.$icons_path.'correction_plan.png"></a></div><br>';

  }
  echo $rows;
}

<?php
/**
 * @package EGR Consumer Registration Form
 * @version 1.1.0
 */
/*
  Plugin Name: EGR Consumer Registration Form
  Plugin URI: http://energogazrezerv.com.ua
  Description: Сustom registration form with fields defined in plugin code
  Version: 1.0
  Author: Ruslan Shylov
  Author URI: rus.shilov@gmail.com
 */
?>
<script type='text/javascript' src='http://code.jquery.com/jquery-latest.min.js'></script>
<script type='text/javascript' src='<?php echo plugin_dir_url( __FILE__) ?>/egr_csmr_rf_scripts.js'></script>
<script type='text/javascript' src='<?php echo plugin_dir_url( __FILE__) ?>/egr_jq_cookie_plugin.js'></script>
<link rel='stylesheet' href=<?php echo plugin_dir_url( __FILE__) ?>'/egr_consumer.css' type='text/css' media='all' />

<?php
 add_action( 'plugins_loaded', 'rs_load_plugin_textdomain' );

 function rs_load_plugin_textdomain() {
 	load_plugin_textdomain( 'egr-consumer-regform', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
 }

function EGR_consumer_register_form() {

  //  $first_name = ( ! empty( $_POST['first_name'] ) ) ? trim( $_POST['first_name'] ) : '';

    ?>
    <p>
        <label for="firm_name"><?php _e('Firm Name', 'egr-consumer-regform') ?><br />
            <input type="text" name="firm_name" id="firm_name" class="input tmpsave" autocomplete="on" value="<?php echo esc_attr( wp_unslash( $firm_name ) ); ?>" size="25" /></label>
    </p>
    <p>
        <label for="EDRPOU"><?php _e('EDRPOU', 'egr-consumer-regform') ?><br />
            <input type="text" name="EDRPOU" id="EDRPOU" class="input tmpsave" value="<?php echo esc_attr( wp_unslash( $EDRPOU ) ); ?>" size="25" /></label>
    </p>
    <p>
        <label for="Official_Address"><?php _e('Official Address', 'egr-consumer-regform') ?><br />
            <input type="text" name="Official_Address" id="Official_Address" class="input tmpsave" value="<?php echo esc_attr( wp_unslash( $Official_Address ) ); ?>" size="25" /></label>
    </p>
    <p>
        <label for="Postal_Address"><?php _e('Postal Address', 'egr-consumer-regform') ?><br />
            <input type="text" name="Postal_Address" id="Postal_Address" class="input tmpsave" value="<?php echo esc_attr( wp_unslash( $Postal_Address ) ); ?>" size="25" /></label>
    </p>

    <h3><b><?php _e('Contact Person', 'egr-consumer-regform') ?></b></h3><br />

    <p>
        <label for="First Name"><?php _e('First Name', 'egr-consumer-regform') ?><br />
            <input type="text" name="First_Name" id="First_Name" class="input tmpsave" value="<?php echo esc_attr( wp_unslash( $First_Name ) ); ?>" size="25" /></label>
    </p>
    <p>
        <label for="Last_Name"><?php _e('Last Name', 'egr-consumer-regform') ?><br />
            <input type="text" name="Last_Name" id="Last_Name" class="input tmpsave" value="<?php echo esc_attr( wp_unslash( $Last_Name ) ); ?>" size="25" /></label>
    </p>
    <p>
        <label for="Phone Number"><?php _e('Phone Number', 'egr-consumer-regform') ?><br />
            <input type="text" name="Phone_Number" id="Phone_Number" class="input tmpsave" value="<?php echo esc_attr( wp_unslash( $Phone_Number ) ); ?>" size="25" /></label>
    </p>
    <p class="cd_link">
      <a href="#" id="clearData"><?php _e('clear fields', 'egr-consumer-regform') ?></a><br>
    </p>

    <?php
}

//2. Добавляем проверку, если этот элемент обязателен
add_filter( 'registration_errors', 'rs_registration_errors', 10, 9 );

function rs_registration_errors( $errors, $sanitized_user_login, $user_email ) {

    if ( empty( $_POST['firm_name'] ) || !empty( $_POST['firm_name'] ) && trim( $_POST['firm_name'] ) == '' ) {
        $errors->add( 'firm_name_error', __( 'You must write a firm name', 'egr-consumer-regform' ) );
    }

    if ( empty( $_POST['EDRPOU'] ) || !empty( $_POST['EDRPOU'] ) && trim( $_POST['EDRPOU'] ) == '' ) {
        $errors->add( 'EDRPOU_error', __( 'You must write a EDRPOU', 'egr-consumer-regform' ) );
    }

    if ( empty( $_POST['Official_Address'] ) || !empty( $_POST['Official_Address'] ) && trim( $_POST['Official_Address'] ) == '' ) {
        $errors->add( 'Official_Address', __( 'You must write a Official Address', 'egr-consumer-regform' ) );
    }
    if ( empty( $_POST['Postal_Address'] ) || !empty( $_POST['Postal_Address'] ) && trim( $_POST['Postal_Address'] ) == '' ) {
        $errors->add( 'Postal_Address', __( 'You must write a Postal Address', 'egr-consumer-regform' ) );
    }
    if ( empty( $_POST['First_Name'] ) || !empty( $_POST['First_Name'] ) && trim( $_POST['First_Name'] ) == '' ) {
        $errors->add( 'First_Name', __( 'You must write a First Name', 'egr-consumer-regform' ) );
    }
    if ( empty( $_POST['Last_Name'] ) || !empty( $_POST['Last_Name'] ) && trim( $_POST['Last_Name'] ) == '' ) {
        $errors->add( 'Last_Name', __( 'You must write a Last Name', 'egr-consumer-regform' ) );
    }
    if ( empty( $_POST['Phone_Number'] ) || !empty( $_POST['Phone_Number'] ) && trim( $_POST['Phone_Number'] ) == '' ) {
        $errors->add( 'Phone_Number', __( 'You must write a Phone Number', 'egr-consumer-regform' ) );
    }
    return $errors;
}

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

?>

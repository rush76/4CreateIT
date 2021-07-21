<?php
/**
 * @package Quizz Manager
 * @version 1.1.1
 */
/*
Plugin Name: Quizz Manager
Plugin URI: http://iryna-shylova.biz.ua/wp-content/quiz_manager/about/
Description: Quizz manager
Author: Ruslan Shylov
Version: 1.0
Author URI: rus.shilov@gmail.com
*/


require ('classes.php');
$quizz_ui = new Quizz_UI; 
//add css
function admin_style() {
  wp_enqueue_style('admin-styles', plugin_dir_url(__FILE__).'quizz_manager.css');
}
// add js

function admin_js() {
  wp_enqueue_script('my_custom_script', plugin_dir_url(__FILE__) . 'quizz_manager.js');
}

add_action('admin_enqueue_scripts', 'admin_style');
add_action('admin_enqueue_scripts', 'admin_js');


add_action('admin_menu', function(){
  
 if (current_user_can('quizz_manager') || current_user_can('administrator')) {
    add_menu_page('Quizzes', 'Quizzes', 'read','quizzes','quizz_dashboard','', 2);
    add_submenu_page('quizzes',__('New Quizz','quizz_manager'),__('New Quizz','quizz_manager'),'read','quizz_new','quizz_new');
  }
});

function quizz_dashboard() {
  
 //  $tmpl = file_get_contents( plugin_dir_url(__FILE__)."db.html");
  global $wpdb;
  // get exist quizzes
  $res = $wpdb->get_results('SELECT * FROM IRQ_quizzes ORDER BY quizz_addeddate DESC');
 
  if (!empty($res)) {
echo DDi($res);
  } else {
    echo 'no one quizz found';
  }
  
}

function quizz_new(){
global $quizz_ui;
  $quizz_ui->new_quizz_form(); 

}

?>

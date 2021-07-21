<div class="wrap">

  <center><h1><?php echo get_admin_page_title() ?></h1></center>

  <?php



global $wpdb;
  switch ($_GET['action']) {
    default:

  // settings_errors() не срабатывает автоматом на страницах отличных от опций
  if( get_current_screen()->parent_base !== 'options-general' )
    settings_errors('название_опции');
    $icons_path = plugin_dir_url(__FILE__)."icons/";
  ?>

  <div class="csmr_menu_item"><a href="?page=gas&action=newcontract_form" title="Укласти договір"><img src="<?php echo $icons_path ?>new_contract.png"><br />Укласти договір</a></div>
  <div class="csmr_menu_item"><a href="?page=gas&action=addplan_form"><img src="<?php echo $icons_path ?>upload_plan.png"><br />Подати план</a></div>
  <div class="csmr_menu_item"><a href="?page=gas&action=addcorr_form"><img src="<?php echo $icons_path ?>correction_plan.png"><br>Подати коригування</a></div>
  <div class="csmr_menu_item"><a href="?page=gas&action=addfact_form"><img src="<?php echo $icons_path ?>upload_fact.png"><br>Подати факт</a></div>
  <div class="csmr_menu_item"><a href="?page=gas&action=getinvoice_form"><img src="<?php echo $icons_path ?>make_invoice.png"><br>Сформувати рахунок</a></div>

</div>
<?php
break;
case "newcontract_form":
  ?>
    <div class="wrap">
        <center><h2>УКЛАДАННЯ ДОГОВОРУ</h2></center>
       Для того щоб укласти новий договір потрібно:
       <ul class="form_todo_list">
         <li>Завантажити шаблон договору (<a href="http://energogazrezerv.com.ua/wp-content/uploads/2020/04/Proekt-dohovoru-promyslovym-spozhyvacham.doc">для промислових підприємств</a>, <a href="http://energogazrezerv.com.ua/wp-content/uploads/2020/04/Proekt-dohovoru-biudzhetnym-spozhyvacham.doc">для бюджетних установ</a>)</li>
         <li>Відсканувати заповнений договір у форматі PDF</li>
         <li>Завантажити скан у форму нижче</li>
         <li>Заповнити відповідні поля у формі нижче</li>
         <li>Натиснути "Відправити"</li>
       </ul>
       Найближчим часом з вами зв'яжеться менеджер для обговорення деталей та активує договір в системі.
       Після активації ви отримаете електроного листа зі сповіщенням. Після цього в вас буде можливість подавати плани/коригування тощо.
      <hr>
       <form id="add_contract_form" name="add_contract_form" action="?page=gas&action=newcontract_processing" method="post" enctype="multipart/form-data">
         <input type="hidden" id="contract_type" value="gas">
         <label for="consumer_contract_number">Введіть номер контракту у вашій системі документобігу :
           <input type="text" id="consumer_contract_number" name="consumer_contract_number" value="" placeholder="Номер контракту" required></label><br>
         <label> Термін дії контракту: </label><br>

         <label for="contract_start_date">Початок: <input type="date" id="contract_start_date" name="contract_start_date" value="today" required></label>
         <label for="contract_end_date"> Закінчення: <input type="date" id="contract_end_date" name="contract_end_date" value="today" required></label><br>

         <label for="contract_start_date">Оберіть тип комерційної пропозиції:</label><br>
         <div class="cp_types">
         <?php
           global $wpdb;
           //get commercial prorositions options
           // show cp type
           $cp_set = $wpdb->get_results("SELECT * FROM consumer_cp_options WHERE cpo_grp_id=1", OBJECT);
           foreach ($cp_set as $cp_type) {
             echo '<div class="cp_type"><input type="radio" id="cp_type" name="cp_type" value="'.$cp_type->cpo_caption.'" required >'.__( $cp_type->cpo_caption,'egr-consumer').'</input></div>';
           }
        ?>
        </div>
        <hr>
        <div class="cp_types">
        <?php
        // show payment type
          $cp_set = $wpdb->get_results("SELECT * FROM consumer_cp_options WHERE cpo_grp_id=2", OBJECT);
          foreach ($cp_set as $cp_type) {
            echo '<div class="cp_type"><input type="radio" id="cp_payment_type" name="cp_payment_type" value="'.$cp_type->cpo_caption.'" required >'.__( $cp_type->cpo_caption,'egr-consumer').'</input></div>';
          }
        ?>
      </div>
      <hr>
      <div class="cp_types">
      <?php
      // show payment type
        $cp_set = $wpdb->get_results("SELECT * FROM consumer_cp_options WHERE cpo_grp_id=3", OBJECT);
        foreach ($cp_set as $cp_type) {
          echo '<div class="cp_type"><input type="radio" id="cp_delivery_type" name="cp_delivery_type" value="'.$cp_type->cpo_caption.'" required >'.__( $cp_type->cpo_caption,'egr-consumer').'</input></div>';
        }
      ?>
         <label for="contract_file"> Оберіть файл:

          <input type="file" id="contract_file"  name="contract_file" multiple="false" required ></label><br>
         <button  type="submit" id="submit" class="form_submit"><?php _e( 'Send','egr-consumer'); ?></button>
       </form>
    </div>

  <?php
break;
case "newcontract_processing":
  $wpdb->show_errors();
  $consumer_id = get_current_user_id();
  $error_msg ='';
  // checking exists contract number
  $query ="SELECT * FROM consumers_contracts WHERE consumer_id=".$consumer_id." AND consumer_contract_number='".$_POST['consumer_contract_number']."'";
  $db_contr_num = $wpdb->get_row($query, OBJECT );
  $uploaded_file= $_FILES['contract_file'];
  // check if exist contract num in DB
   if ( $db_contr_num != NULL) {
        $error_msg .='<div class="error"><h4> номер контракту <h2>'.$db_contr_num->consumer_contract_number.'</h2> вже існує у системі.<br>Поверніться <a href="javascript: window.history.back()">назад</a> та введіть інший номер. Або зв\'яжіться з менеджером за телефонами:</h4></div>';
   }

  //check file type of scanned contract file
  if ($uploaded_file['type'] != 'application/pdf') {
       $error_msg .='<div class="error">Тип відсканованого файлу має бути PDF. <br> Будь ласка поверніться <a href="javascript: window.history.back()">назад</a> та оберіть правильний тип файу</div>';
  }

// BEGIN ADDING INFO FROM FORM TO DB
// if no errors make some actions to add new contract, else show errors
if (empty($error_msg)) {
$PCN_prefix = array('gas' => 'Г/', 'ele'=>'E/');

//action 1: get max number of provider contract counter
  // ccn  - current contract number
  // ncn - next contract number
  // PCCN - Provider's Current Contract Number (get from DB)
  // PNCN - Provider's Next Contract Number
$PCCN = $wpdb->get_var('SELECT provider_contract_number FROM consumers_contracts WHERE contract_type="gas" ORDER BY internal_id DESC LIMIT 1');
  if (!isset($PCCN)) {
    $PCN = $PCN_prefix[$_GET['page']]."500-".date("y");
    DD($PCN);

} else {
  // set provider_contract_number +1
  preg_match('/[0-9]+/', $PCCN, $PCN);
  $PCN = $PCN_prefix[$_GET['page']].strval(intval($PCN[0])+1)."-".date('y');
}


  // action 2: add row to main table 'consumers_contracts'  !Done
  $contract_fname = $consumer_id."#".$_POST['consumer_contract_number'];

  $ins_res = $wpdb->insert('consumers_contracts', array(
    'consumer_id' => $consumer_id,
    'consumer_contract_number' => $_POST['consumer_contract_number'],
    'provider_contract_number' => $PCN,
    'contract_type'            => 'gas',
    'contract_start_date'=> $_POST['contract_start_date'],
    'contract_end_date'=> date("Y-m-d 23:59:00", strtotime($_POST['contract_end_date']))
  ));

  //action 3: add row into table consumer_commercial_propositions !Done
  $last_contract_internal_id = $wpdb->get_var('SELECT * FROM consumers_contracts WHERE contract_type="gas" ORDER BY internal_id DESC LIMIT 1');
  $ins_res = $wpdb->insert('consumer_commercial_propositions', array(
     'cp_contract_internal_id'  => $last_contract_internal_id,
     'cp_type'                  => $_POST['cp_type'],
     'cp_payment_type'          => $_POST['cp_payment_type'],
     'cp_delivery_type'         => $_POST['cp_delivery_type']
  ));

  if ($ins_res == 1) { // if row added into database, next step is upload scan of contract file. !Done
    $file_ts = new DateTime();
    $newname = get_current_user_id()."_".$file_ts->getTimestamp().".pdf";
    $upload_res = wp_upload_bits($newname, null, file_get_contents( $_FILES["contract_file"]["tmp_name"]) );
    // action 5: UPDATE consumers_contracts table, add link to scan file.
    $query_res = $wpdb->query('UPDATE consumers_contracts SET contract_file="'.$upload_res['file'].'", contract_url="'.$upload_res['url'].'" WHERE internal_id='.$last_contract_internal_id);
  }

  if (!$upload_res['error']) { // if no errors after uploading scan file,
  //  DD($upload_res);
    // action 4: prepare and send email to managers
    $firm_name = get_usermeta($consumer_id,'firm_name');
    $msg = file_get_contents(plugin_dir_url(__FILE__)."mail_tmpl_contract.html");
    $msg = str_replace("{CONTRACT_NUMBER}", $PCN, $msg);
    $msg = str_replace("{FIRM_NAME}", $firm_name, $msg);
    $msg = str_replace("{LINK_2_FILE}",$upload_res['url'], $msg);
    $headers = "From: admin@energogazrezerv.com.ua\r\n";
    $headers .= "Reply-To: admin@energogazrezerv.com.ua\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    mail('cms@energogazrezerv.com.ua',mb_encode_mimeheader(__('new contract in CMS by number: ','egr-consumer').$PCN." | ".__('from','egr-consumer').": ".$firm_name), $msg, $headers);

    echo '<div class="success">Контракт додано .<br>Очікуйте на підтвердження від менеджера.<br></div><br><a href="?page=gas&action=newcontract_form">Додати інший контракт</a> | <a href="?page=gas">Газопостачання</a> | <a href="?page=energy">Електроенергія</a>';
  }else{
    $error_msg="Скан договору не завантажено на сервер. Будь ласка повідомте адміністратора";
  }


} else {
echo $error_msg;
}

// && $db_contr_num->consumer_contract_number = $_POST['consumer_contract_number']

//    DD($upload_res);
break;


case "addplan_form":

// define minimum plan start date
        $ts = getdate();
//check allowed time for plan start date
        if ($ts['hours'] < 10 ) { $ts['hours']="0".$ts['hours']; } // add leading zero to hour number
        if ($ts['mon'] < 10 ) { $ts['mon']="0".$ts['mon']; } // add leading zero to month number

        if ( $ts['hours'] < 13 ) {
          if ($ts['mday'] <10 ) { $ts['mday'] = "0".$ts['mday']; }
          $psd_min =$ts['year']."-".$ts['mon']."-".$ts['mday']; }
        else {
          $nday = strval($ts['mday'])+2;
          if ($nday <10 ) { $nday = "0".$nday; }
          if ($ts['mon'] <10 ) { $nmon = "0".$nmon; } else { $nmon = $ts['mon']; }
          $psd_min =strval($ts['year'])."-".$nmon."-".$nday;
        }


      $query = 'SELECT * FROM consumers_contracts WHERE contract_type="gas" AND consumer_id='.get_current_user_id().' AND contract_end_date > CURDATE()+2';
      $contracts = $wpdb->get_results($query, OBJECT);
      $select_opt = '<option value="">--------</option>';
      $contract_end_date ='';
      if (!empty($contracts)) {
          foreach ($contracts as $contract) {
            $contract_dates .= '<input type="hidden" id="S'.$contract->internal_id.'" name="S'.$contract->internal_id.'" value="'.date("Y-m-d", strtotime($contract->contract_start_date)).'">';
            $contract_dates .= '<input type="hidden" id="E'.$contract->internal_id.'" name="E'.$contract->internal_id.'" value="'.date("Y-m-d", strtotime($contract->contract_end_date)).'">';

            $select_opt .="<option value=".$contract->internal_id.">".$contract->consumer_contract_number."</option>";
          }
        ?>
        <div class="wrap">
          <center>
          <div class="contracts_hdr">Запит на плановий об'єм</div>
          <form name="add_plan" id="add_plan" action="?page=gas&action=addplan_processing" method="post" enctype="multipart/form-data">
            <?php echo $contract_dates; ?>
            <label>Контракт:
            <select id="contract_id" name="contract_id" required><?php echo $select_opt; ?></select></label></br>
            <i>при обранні контракту, дата закінчення планового об'єму автоматично виставляється згідно дати закінчення контракту.<br> Але ви можете обрати будь яку дату до вищевказаної'</i></br>
            <label>Дата початку дії контракту:<input id="csd_info" type="text" disabled></label></br>
            <label>Дата закінчення дії контракту:<input id="ced_info" type="text" disabled></label></br>
            <label>Плановий об'єм:
            <input type="number" id="plan_capacity" name="plan_capacity" step=0.00001  min=0.00001 max=99999.99999 title="будь ласка введіть обєм у формати хххxxx,ххххх" size="30" required></label><br><br>

            <label>Дата початку:
              <input type="date" id="plan_start_date" name="plan_start_date" value="<?php echo $psd_min; ?>" max="" size="30"></label><br><br>
            <label>Дата закінчення:
                <input type="date" id="plan_end_date" name="plan_end_date" max="" placeholder="" size="30"></label><br><br>

            <input type="submit" class="form_submit" value="Подати">
          </form>
          </center>
        </div>
        <?php

      } else {
        $error_msg=' <div class="error"><br>Ви не маєте активних договорів, по яким можна було б подати планове споживання.<br> Спочатку додайте договір у розділі <a href="?page=gas&action=newcontract_form">"Укласти договір"</a><br><br></div>';
        echo $error_msg;
      }

break;
case "addplan_processing" :

  $addplan_res = $wpdb->insert('consumer_contract_plans', array(
    'contract_id'     => $_POST['contract_id'],
    'plan_capacity'   => $_POST['plan_capacity'],
    'plan_start_date' => date("Y-m-d 00:00:00", strtotime($_POST['plan_start_date'])),
    'plan_end_date'    => date("Y-m-d 23:59:00", strtotime($_POST['plan_end_date']))
  ));

  if ($addplan_res == 1) {
    echo "Планове споживання додано до контракту.<br>Очікуйте на підтвердження від менеджера.<br><br><a href='?page=gas&action=addplan_form'>Додати план до іншого контракту</a> | <a href='?page=gas'>Газопостачання</a> | <a href='?page=energy'>Електроенергія</a>";

    //act 3  get internal contract number
     $q = 'SELECT consumer_contract_number FROM consumers_contracts WHERE internal_id='.$_POST['contract_id'];
    $icn = $wpdb->get_var($q);
    // DD($icn);

    // action 4: prepare and send email to managers



    $firm_name = get_usermeta(get_current_user_id(),'firm_name');
    $msg = file_get_contents(plugin_dir_url(__FILE__)."mail_tmpl_plan.html");
    $msg = str_replace("{CONTRACT_NUMBER}", $icn, $msg);
    $msg = str_replace("{FIRM_NAME}", $firm_name, $msg);
    $msg = str_replace("{CAPACITY}", $_POST['plan_capacity'], $msg);
    $msg = str_replace("{LINK_2_FILE}",$upload_res['url'], $msg);
    $headers = "From: admin@energogazrezerv.com.ua\r\n";
    $headers .= "Reply-To: admin@energogazrezerv.com.ua\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    if (mail('cms@energogazrezerv.com.ua',mb_encode_mimeheader(__('New plan in CMS by number:','egr-consumer').$icn." | ".__('from','egr-consumer').": ".$firm_name), $msg, $headers)) {
      echo '<div class="success">Планове споживання додано .<br>Очікуйте на підтвердження від менеджера.<br></div><br><a href="?page=gas&action=addplan_form">Додати інший план</a> | <a href="?page=gas">Газопостачання</a> | <a href="?page=energy">Електроенергія</a>';

    }

  }
break;
case "addcorr_form":

  //action 1: get all contracts by consuber_id
  $q = 'SELECT internal_id, consumer_contract_number,contract_end_date FROM consumers_contracts WHERE contract_type="gas" AND contract_end_date > CURDATE()  and consumer_id='.get_current_user_id().' ORDER BY contract_end_date DESC';
  $contracts = $wpdb->get_results($q);
  echo '<div id="addcorr_list"> <div class="contracts_hdr">ПОДАНІ ПЛАНОВІ СПОЖИВАННЯ та КОРИГУВАННЯ по АКТИВНИХ КОНТРАКТАХ</div>';
  foreach ($contracts as $contract) {
    echo '<div class="contract_row">контракт №: <b>'.$contract->consumer_contract_number.'</b></div>';
    $plans = $wpdb->get_results("SELECT * FROM consumer_contract_plans WHERE contract_id=".$contract->internal_id." ORDER BY plan_submission_date ASC");
      echo "<div class='plan_submission_date plan_clm'>Дата подачі</div><div class='plan_capacity plan_clm'>Плановий об'єм</div><div class='plan_startdate plan_clm'>Дата початку</div><div class='plan_enddate plan_clm'>Дата закінчення</div><br>";
      foreach ($plans as $plan) {
          echo "<a id='add_corr' href='#corr_form' title='додати коригування'><div class='plan_submission_date plan_clm'>".$plan->plan_submission_date."</div>";
          echo "<div class='plan_capacity plan_clm'>".floatval($plan->plan_capacity)."</div><div class='plan_startdate plan_clm'>".$plan->plan_start_date."</div><div class='plan_enddate plan_clm'>".$plan->plan_end_date."</div><img class='addcorr_btn' src='".plugin_dir_url(__FILE__)."/icons/add_correction.png'></a>";
          echo '<div class="corr_title">коригування</div>';
          $corrs = $wpdb->get_results("SELECT * FROM consumer_plans_corrections WHERE corr_plan_id=".$plan->plan_id);
          foreach ($corrs as $corr) {
            echo '<div class="corr_smbdate">'.$corr->corr_sumbission_date.'</div><div class="corr_capacity">'.floatval($corr->corr_capacity).'</div><br>';
            // code...
          }
      }
  } // contracts foreach
  ?>
</div> <!-- addcorr_list -->

<hr>
<div class="corr_form">
<h3>Форма подання коригування</h3>
  <form id="corr_form" action="?page=gas&action=addcorr_processing" method="post" enctype="multipart/form-data">
    <input type="hidden" name="addcorr_plan_id" id="addcorr_plan_id">
    <input type="text" id="plnchk_indicator" value="план не обраний" disabled><br>
    Об'єм коригування: <input type="number" id="corr_capacity" name="corr_capacity" step=0.00001  min=0.00001 max=99999.99999 title="будь ласка введіть обєм у формати хххxxx,ххххх" size="30" required disabled><br>
    <input type="submit" name="submit" class="form_submit" value="Подати коригування">
  </form>
</div>
  <?php
break;
case "addcorr_processing":

    $addcorr_res = $wpdb->insert('consumer_plans_corrections', array(
    'corr_plan_id'     => $_POST['addcorr_plan_id'],
    'corr_capacity'   => $_POST['corr_capacity']
  ));
  if ($addcorr_res == 1) {
    echo '<div class="success">Коригування додано .<br>Очікуйте на підтвердження від менеджера.<br></div><br><a href="?page=gas&action=addcorr_form">Додати інше коригування</a> | <a href="?page=gas">Газопостачання</a> | <a href="?page=energy">Електроенергія</a>';
    $ctr_id = $wpdb->get_var("SELECT contract_id FROM consumer_contract_plans WHERE plan_id=".$_POST['addcorr_plan_id']); // get contract id by current plan_id;
    $ctr_name = $wpdb->get_var("SELECT provider_contract_number FROM consumers_contracts WHERE internal_id=".$ctr_id);
    $units = '';
    if ($_GET['page']=='gas') { $units = " куб.м."; }
    // send email notification to managers
    $firm_name = get_usermeta(get_current_user_id(),'firm_name');
    $msg = file_get_contents(plugin_dir_url(__FILE__)."mail_tmpl_correction.html");
    $msg = str_replace("{PROVIDER_CONTRACT_NUMBER}", $ctr_name, $msg);
    $msg = str_replace("{FIRM_NAME}", $firm_name, $msg);
    $msg = str_replace("{CAPACITY}", $_POST['corr_capacity'].$units, $msg);
    $headers = "From: admin@energogazrezerv.com.ua\r\n";
    $headers .= "Reply-To: admin@energogazrezerv.com.ua\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    mail('cms@energogazrezerv.com.ua',mb_encode_mimeheader(__('New correction in CMS from:','egr-consumer').$firm_name), $msg, $headers);

  } else {
      echo '<div class="error">Коригування не додано<br>Зателефонуйте будь ласка менеджеру</div>';
  }

break;
case "addfact_form":
  $query = "SELECT * FROM consumers_contracts WHERE contract_type='gas' AND consumer_id=".get_current_user_id(). " AND contract_end_date > CURDATE()+2";
  $contracts = $wpdb->get_results($query, OBJECT);
  $select_opt = '<option value="">--------</option>';
  $contract_end_date ='';
  if (!empty($contracts)) {
      foreach ($contracts as $contract) {
        $contract_end_date .= '<input type="hidden" id="'.$contract->internal_id.'" name="'.$contract->internal_id.'" value="'.date("Y-m-d", strtotime($contract->contract_end_date)).'">';

        $select_opt .="<option value=".$contract->internal_id.">".$contract->consumer_contract_number."</option>";
      }
  }
  ?>
  <div class="wrap">
    <div class="contracts_hdr">Введення фактичного споживання</div>
      <div class="addfact_form">
        <form id="fact_form" action="?page=gas&action=addfact_processing" method="post" enctype="multipart/form-data">
        Оберіть контракт: <select id="fact_contract_id" name="fact_contract_id" required><?php echo $select_opt; ?></select></label></br>
        Введіть об'єм: <input type="number" id="fact_corr_capacity" name="fact_corr_capacity" step=0.00001  min=0.00001 max=99999.99999 title="будь ласка введіть обєм у формати хххxxx,ххххх" size="30" required disabled><br>
        <input type="submit" id="fact_form_smb" class="form_submit" name="submit" value="Подати">
    </div>
  </div>
  <?php
break;
case "addfact_processing":
  $addfact_res = $wpdb->insert('consumer_contract_facts', array(
    'contract_id' => $_POST['fact_contract_id'] ,
    'fact_capacity' => $_POST['fact_corr_capacity']
  ));

  if ($addfact_res ==1) {
    echo '<div class="success">Фактичне споживання додано .<br>Очікуйте на підтвердження від менеджера.<br></div><br><a href="?page=gas&action=addfact_form">Додати ще факт</a> | <a href="?page=gas">Газопостачання</a> | <a href="?page=energy">Електроенергія</a>';
    $ctr_id = $wpdb->get_var("SELECT contract_id FROM consumer_contract_plans WHERE plan_id=".$_POST['addcorr_plan_id']); // get contract id by current plan_id;
    $ctr_name = $wpdb->get_var("SELECT provider_contract_number FROM consumers_contracts WHERE internal_id=".$ctr_id);
    // send email notification to managers
    $firm_name = get_usermeta(get_current_user_id(),'firm_name');
    $msg = file_get_contents(plugin_dir_url(__FILE__)."mail_tmpl_fact.html");
    $msg = str_replace("{PROVIDER_CONTRACT_NUMBER}", $ctr_name, $msg);
    $msg = str_replace("{FIRM_NAME}", $firm_name, $msg);
    $msg = str_replace("{CAPACITY}", $_POST['corr_capacity'], $msg);
    $headers = "From: admin@energogazrezerv.com.ua\r\n";
    $headers .= "Reply-To: admin@energogazrezerv.com.ua\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    mail('cms@energogazrezerv.com.ua',mb_encode_mimeheader(__('New correction in CMS from:','egr-consumer').$firm_name), $msg, $headers);

  } else {
      echo '<div class="error">Коригування не додано<br>Зателефонуйте будь ласка менеджеру</div>';
  }



break;
case "getinvoice_form":
    ?>
    <div class="wrap">
      <div class="contracts_hdr">Запит на формування рахунку</div>
    <?php
    $contracts  = $wpdb-> get_results('SELECT internal_id, consumer_contract_number FROM consumers_contracts WHERE consumer_id='.get_current_user_id()." AND contract_end_date > CURDATE() ORDER BY contract_end_date DESC");
    $opts = '<option value="---">--------------------</option>';
    foreach ($contracts as $contract) {
        $opts .= '<option value='.$contract->internal_id.'>'.$contract->consumer_contract_number.'</option>';

    }
    ?>
    <form action="?page=gas&action=invoice_processing" method="POST" enctype="multipart/form-data" id="inv_form">
      <input id="unit_type" type="hidden" value="gas">
     Оберіть договір: <select id="inv_contract_id" name="inv_contract_id" required><?php echo $opts; ?></select><br>
     Одиниці виміру: <select id="inv_valtype" name="inv_valtype" required disabled>
       <option value="---">-------</option>
       <option value="price">Сума</option>
       <option value="capacity">Об'єм</option>
       <input type="number" id="inv_amount" name="inv_amount" step="0.00001"  min="0.00001" max="" title="будь ласка введіть значення у формати хххxxx,ххххх" size="30" required disabled>
       <div id="inv_unit"></div><br>
       <input type="submit" name="sumbit" value="Подати запит" class="form_submit">
    </form>
  </div>
    <?php
break;
case "invoice_processing":
  $amount='';
  $ctr_name = $wpdb->get_var("SELECT provider_contract_number FROM consumers_contracts WHERE internal_id=".$_POST['inv_contract_id']);
  // send email notification to managers
  if ($_POST['inv_valtype'] =='price') {$amount = "Cумма рахунку: <b>".$_POST['inv_amount']." грн.</b>"; }
  else {
    $amount = "Об'єм споживання газу: <b>".$_POST['inv_amount']." куб.м.</b>";
  }
  $firm_name = get_usermeta(get_current_user_id(),'firm_name');
  $msg = file_get_contents(plugin_dir_url(__FILE__)."mail_tmpl_invoice.html");
  $msg = str_replace("{PROVIDER_CONTRACT_NUMBER}", $ctr_name, $msg);
  $msg = str_replace("{FIRM_NAME}", $firm_name, $msg);
  $msg = str_replace("{AMOUNT}", $amount, $msg);
  $headers = "From: admin@energogazrezerv.com.ua\r\n";
  $headers .= "Reply-To: admin@energogazrezerv.com.ua\r\n";
  $headers .= "MIME-Version: 1.0\r\n";
  $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

  if (mail('cms@energogazrezerv.com.ua',mb_encode_mimeheader(__('Request for invoice', 'egr-consumer').$firm_name), $msg, $headers)) {
    echo '<div class="success">Запит відправлено.<br>Найближчим часом рахунок надійде вам на електронну скриньку.<br></div><br><a href="?page=gas&action=getinvoice_form">Додати інший запит</a> | <a href="?page=gas">Газопостачання</a> | <a href="?page=energy">Електроенергія</a>';
  } else {
    echo '<div class="error">Запит не відправлено<br>Зателефонуйте будь ласка менеджеру</div>';
  }

break;
} //sw

<?php 

interface iQuizz_UI {
    
    public function new_quizz_form();
}

class Quizz_UI implements iQuizz_UI {

    public function new_quizz_form(){ ?>
      <div class="wrap">
        <div><h1><?php _e('Add new quizz','quizz_manager')?></h1></div>
          <form id="NQ_form" action="?page=quizzes" method="post" enctype="multipart/form-data" >
            <?php _e('Quizz caption','quizz_manager')?>: <input type="text" name="quizz_name" value="" size="80"><br>
            <?php _e('Quizz start date','quizz_manager')?>: <input type="date" name="quizz_startdate" value="<?php echo date('Y-m-d'); ?>" min="<?php echo date('Y-m-d'); ?>">  | <?php _e('Quizz end date','quizz_manager')?>: <input type="date" name="quizz_enddate" value="" min="<?php echo date('Y-m-d'); ?> placeholder="select date pls"><br>
            <?php 
              foreach ($this->getQuizzStatusTypes() as $opt) {
                echo $opt.'<br>';
              }
            ?>
            
            <input type="submit" name="submit" value="Зберегти">
          </form>
        </div>
      </div>
        <?php 
        echo $tmpl;
    }

    private function getQuizzStatusTypes() {  // get quizz status types
      global $wpdb;
        $res = $wpdb->get_results('SELECT DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA="irish_wp" AND TABLE_NAME = "IRQ_quizzes" AND COLUMN_NAME="quizz_status"');
        $this->varDD($res);
      return explode(',',$res['quizz_status']->Type);  
      
    }

    private function varDD($var) {
      if ($_SERVER['REMOTE_ADDR'] == '78.137.6.240') {
        echo "<pre>";
        var_dump($var);
        echo "</pre>";
      }
    }
}

?>
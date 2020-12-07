<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                       <h4 class="modal-title" id="myModalLabel">
                        <span class="edit-title"><?php echo _l('Backup'); ?></span>
                        </h4>
                        <div class="clearfix"></div>
                        <hr class="hr-panel-heading" />

                        <div class="clearfix"></div>

                        <div class="clearfix mtop20"></div>
       <div class="message_box">
    <?php
        if (isset($success) && strlen($success)) {
            echo '<div class="success">';
            echo '<p>' . $success . '</p>';
            echo '</div>';
        }

        if (isset($errors) && strlen($errors)) {
            echo '<div class="error">';
            echo '<p>' . $errors . '</p>';
            echo '</div>';
        }

        if (validation_errors()) {
            echo validation_errors('<div class="error">', '</div>');
        }
    ?>
    </div>
    <?php
        $back_url = $this->uri->uri_string();
        $key = 'referrer_url_key';
        $this->session->set_flashdata($key, $back_url);
    ?>
    <div class="body body-s">
    <?php
        echo form_open($this->uri->uri_string());
    ?>
    <fieldset>
        <section>
            <label>Backup created at</label>
            <label>
                <select name="backup_type">
                <option value="" selected disabled>Backup Type</option>
                <option value="1" >Every Hour</option>
                <option value="2" >Every Week</option>
                <option value="3" >Every Month</option>
                </select>
            </label>
        </section>

        <section>
              <label>Auto remove bachup</label>
            <label>
                <select name="backup_remove">
                <option value="" selected disabled>Backup Type</option>
                <option value="1" >After Every Day</option>
                <option value="2" >After Every Week</option>
                <option value="3" >After Every Month</option>
                </select>
            </label>
        </section>
    </fieldset>

    <footer>
        <button type="submit" name="backup" value="backup" class="button">Get Backup</button>
    </footer>
    <?php
        echo form_close();
    ?>
                      
                   
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php init_tail();?>




</body>
</html>

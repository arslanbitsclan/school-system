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
                         <div class="_buttons">

                            <a href="<?php echo admin_url('Backup_crm/build_backups'); ?>" class="btn btn-info pull-left display-block"><?php echo _l('Backup'); ?></a>
                        </div>

                        <div class="clearfix"></div>

                        <div class="clearfix mtop20"></div>
     
   
    <div class="body body-s">
    
     <?php echo form_open($this->uri->uri_string(),array('class'=>'backup-form','autocomplete'=>'off')); ?>
    <fieldset>
         <div class="form-group select-placeholder">
                        <label for="backup_type"><?php echo _l('Backup created at'); ?></label>
                        <select class="selectpicker" data-none-selected-text="<?php echo _l('Every Hour'); ?>" data-width="100%" name="backup_type" id="backup_type">
                           <option value="" <?php if(isset($member) && empty($member->backup_type)){echo 'selected';} ?>></option>
                           <option value="everyhour" <?php if(isset($member) && $member->backup_type == 'everyhour'){echo 'selected';} ?>>Every Hour</option>
                           <option value="everyweek" <?php if(isset($member) && $member->backup_type == 'everyweek'){echo 'selected';} ?>>Every Week</option>
                           <option value="everymonth" <?php if(isset($member) && $member->backup_type == 'everymonth'){echo 'selected';} ?>>Every Month</option>
                        </select>
                     </div>

        

         <div class="form-group select-placeholder">
                        <label for="auto_remove_backup"><?php echo _l('Auto Remove Backup'); ?></label>
                        <select class="selectpicker" data-none-selected-text="<?php echo _l('After Every Day'); ?>" data-width="100%" name="auto_remove_backup" id="auto_remove_backup">
                        <option value="" <?php if(isset($member) && empty($member->auto_remove_backup)){echo 'selected';} ?>></option>
                        <option value="aftereveryday" <?php if(isset($member) && $member->auto_remove_backup == 'aftereveryday'){echo 'selected';} ?>>After Every Day</option>
                        <option value="aftereveryweek" <?php if(isset($member) && $member->auto_remove_backup == 'aftereveryweek'){echo 'selected';} ?>>After Every Week</option>
                        <option value="aftereverymonth" <?php if(isset($member) && $member->auto_remove_backup == 'aftereverymonth'){echo 'selected';} ?>>After Every Month</option>
                        </select>
                     </div>
    </fieldset>

                        <div class="">
                            <button type="submit" class="btn btn-info pull-right"><?php echo _l('submit'); ?></button>
                        </div>
                     <?php
                         echo form_close();
                        ?>
                      
                   
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php init_tail();?>
    <script>
       
    window.addEventListener('load',function(){
        appValidateForm($('.backup-form'), {
            backup_type: 'required',
            auto_remove_backup: 'required',
            
            
            
         });
         });
</script>




</body>
</html>

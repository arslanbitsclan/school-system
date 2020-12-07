<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="_buttons">

                            <a href="<?php echo admin_url('Backup_crm/backupsettings'); ?>" class="btn btn-info pull-left display-block"><?php echo _l('Backup Settings'); ?></a>
                        </div>
                        <div class="clearfix"></div>
                        <hr class="hr-panel-heading" />

                        <div class="clearfix"></div>

                        <div class="clearfix mtop20"></div>
                        <a href="#" data-toggle="modal" data-target="#customers_bulk_action" class="bulk-actions-btn table-btn hide" data-table=".table-backups"><?php echo _l('bulk_actions'); ?></a>
                        <div class="modal fade bulk_actions" id="customers_bulk_action" tabindex="-1" role="dialog">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title"><?php echo _l('bulk_actions'); ?></h4>
                                    </div>
                                    <div class="modal-body">
                                            <div class="checkbox checkbox-danger">
                                                <input type="checkbox" name="mass_delete" id="mass_delete">
                                                <label for="mass_delete"><?php echo _l('mass_delete'); ?></label>
                                            </div>
                                            <hr class="mass_delete_separator" />

                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                                        <a href="" class="btn btn-info" onclick="customers_bulk_action(this); return false;"><?php echo _l('confirm'); ?></a>
                                    </div>
                                </div>
                                <!-- /.modal-content -->
                            </div>
                            <!-- /.modal-dialog -->
                        </div>
                         <?php
                        $table_data = array();
                        $_table_data = array(
                      '<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="clients"><label></label></div>',
                       array(
                         'name'=>_l('id'),
                         'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-number')
                        ),
                         array(
                         'name'=>_l('backup_name'),
                         'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-company')
                        ),
                         array(
                         'name'=>_l('backup_location'),
                         'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-primary-contact')
                        ),
                         array(
                         'name'=>_l('created_date'),
                         'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-primary-contact-email')
                        ),
                       
                       
                      );
                     foreach($_table_data as $_t){
                      array_push($table_data,$_t);
                     }

                     
                     $table_data = hooks()->apply_filters('backups_table_columns', $table_data);

                     render_datatable($table_data,'backups',[],[
                           'data-last-order-identifier' => 'backups',
                           'data-default-order'         => get_table_last_order('backups'),
                     ]);
                     ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php init_tail();?>

<script>
    
     $(function(){
       var backupsServerParams = {};
       
     // alert();
       var tAPI = initDataTable('.table-backups', admin_url+'Backup_crm/table', [0], [0], backupsServerParams,<?php echo hooks()->apply_filters('backups_table_default_order', json_encode(array(2,'asc'))); ?>);
       $('input[name="exclude_inactive"]').on('change',function(){
           tAPI.ajax.reload();
       });
   });
  function customers_bulk_action(event) {
        var r = confirm(app.lang.confirm_action_prompt);
        if (r == false) {
            return false;
        } else {
            var mass_delete = $('#mass_delete').prop('checked');
            var ids = [];
            var data = {};

                data.mass_delete = true;
            var rows = $('.table-backups').find('tbody tr');
            //console.log(rows);
            $.each(rows, function() {
                var checkbox = $($(this).find('td').eq(0)).find('input');
                if (checkbox.prop('checked') == true) {
                    ids.push(checkbox.val());
                }
            });
            data.ids = ids;
            $(event).addClass('disabled');
            setTimeout(function(){
                $.post(admin_url +'Backup_crm/bulk_action', data).done(function() {

                    window.location.reload();
                });
            },50);
        }
    }
    
</script>



</body>
</html>

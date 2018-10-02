<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
$action_buttons[]=array
(
    'label'=>$CI->lang->line("ACTION_BACK"),
    'href'=>site_url($CI->controller_url)
);
if((isset($CI->permissions['action1']) && ($CI->permissions['action1']==1)) || (isset($CI->permissions['action2']) && ($CI->permissions['action2']==1)))
{
    $action_buttons[]=array
    (
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_SAVE"),
        'id'=>'button_action_save',
        'data-form'=>'#save_form'
    );
}
$action_buttons[]=array(
    'type'=>'button',
    'label'=>$CI->lang->line("ACTION_CLEAR"),
    'id'=>'button_action_clear',
    'data-form'=>'#save_form'
);
$CI->load->view('action_buttons',array('action_buttons'=>$action_buttons));

?>
<form class="form_valid" id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save');?>" method="post">
    <input type="hidden" id="id" name="id" value="<?php echo $item_head['id']; ?>" />
    <input type="hidden" id="id" name="item[farmer_id]" value="<?php echo $item_head['farmer_id']; ?>" />
    <input type="hidden" id="system_save_new_status" name="system_save_new_status" value="0" />
    <div class="row widget">
        <div class="widget-header">
            <div class="title">
                <?php echo $title; ?>
            </div>
            <div class="clearfix"></div>
        </div>

        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DIVISION_NAME');?>:</label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $item_head['division_name'];?></label>
            </div>
        </div>

        <div class="row show-grid" id="zone_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ZONE_NAME');?>:</label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $item_head['zone_name'];?></label>
            </div>
        </div>

        <div class="row show-grid" id="territory_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_TERRITORY_NAME');?>:</label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $item_head['territory_name'];?></label>
            </div>
        </div>

        <div class="row show-grid" id="district_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DISTRICT_NAME');?>:</label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $item_head['district_name'];?></label>
            </div>
        </div>

        <div class="row show-grid" id="customer_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right">Outlet:</label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $item_head['outlet'];?></label>
            </div>
        </div>

        <div class="row show-grid" id="farmer_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right">Dealer:</label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $item_head['farmer_name'];?></label>
            </div>
        </div>

        <div id="files_container">
            <div style="overflow-x: auto;" class="row show-grid">
                <div class="col-xs-4">

                </div>
                <div class="col-sm-4 col-xs-8">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th style="min-width: 250px;">Dealer Information File<span style="font-size: 10px; color:green;"><i> (Allowed types:xls,xlsx,csv,doc,docx,pdf)</i></span></th>
                            <th style="min-width: 50px;">Upload</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $serial=1;
                        foreach($items as $index=>$item)
                        {
                            ?>
                            <tr>
                                <td>
                                    <div class="preview_container_file" id="preview_container_file_<?php echo $index+1;?>">
                                        <a href="<?php echo $CI->config->item('system_base_url_picture').$item['image_location']; ?>" class="external btn btn-danger" target="_blank"><?php echo 'File '.$serial; $serial++ ?></a>
                                    </div>
                                </td>
                                <td>
                                    <input type="file" id="file_<?php echo $index+1; ?>" name="file_<?php echo $item['id']; ?>" data-current-id="<?php echo $index+1;?>" data-preview-container="#preview_container_file_<?php echo $index+1;?>" class="browse_button_edit"><br>
                                    <input type="hidden" class="dealer_visit" name="old_files[<?php echo $item['id'];?>]" value="<?php  echo $item['id'];?>">
                                </td>

                            </tr>
                        <?php
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">

            </div>
            <div class="col-xs-4 col-xs-8">
                <button type="button" class="btn btn-warning system_button_add_more pull-right" data-current-id="<?php echo sizeof($items);?>"><?php echo $CI->lang->line('LABEL_ADD_MORE');?></button>
            </div>
            <div class="col-xs-4">

            </div>
        </div>

    </div>

    <div class="clearfix"></div>
</form>

<div id="system_content_add_more" style="display: none;">
    <table>
        <tbody>
        <tr>
            <td>
                <div class="preview_container_file">
                </div>
            </td>
            <td>
                <input type="file" class="browse_button"><br>
                <button type="button" class="btn btn-danger system_button_add_delete"><?php echo $CI->lang->line('DELETE'); ?></button>
                <input type="hidden" class="dealer_visit" name="" value="0">
            </td>
        </tr>
        </tbody>
    </table>
</div>

<script type="text/javascript">
    jQuery(document).ready(function()
    {
        system_preset({controller:'<?php echo $CI->router->class; ?>'});
        $(document).off('click', '.system_button_add_more');
        $(".browse_button_edit").filestyle({input: false, icon: false, buttonText: "Upload",buttonName: "btn-primary"});
        $(document).on("click", ".system_button_add_more", function(event)
        {
            var current_id=parseInt($(this).attr('data-current-id'));
            current_id=current_id+1;
            $(this).attr('data-current-id',current_id);
            var content_id='#system_content_add_more table tbody';
            $(content_id+' .browse_button').attr('data-preview-container','#preview_container_file_'+current_id);
            $(content_id+' .browse_button').attr('name','file_'+current_id);
            $(content_id+' .browse_button').attr('id','file_'+current_id);
            $(content_id+' .preview_container_file').attr('id','preview_container_file_'+current_id);
            $(content_id+' .dealer_visit').attr('name','files['+current_id+']');
            var html=$(content_id).html();
            //console.log(html);
            $("#files_container tbody").append(html);
            $(content_id+' .browse_button').removeAttr('data-preview-container');
            $(content_id+' .browse_button').removeAttr('name');
            $(content_id+' .browse_button').removeAttr('id');
            $(content_id+' .preview_container_file').removeAttr('id');
            $('#file_'+current_id).filestyle({input: false,icon: false,buttonText: "Upload",buttonName: "btn-primary"});

        });
        $(document).on("click", ".system_button_add_delete", function(event)
        {
            $(this).closest('tr').remove();
        });
    });
</script>

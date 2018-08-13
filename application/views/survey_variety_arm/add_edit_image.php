<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_BACK"),
    'href'=>site_url($CI->controller_url.'/index/list_image/'.$item_head['variety_id'])
);
$action_buttons[]=array(
    'type'=>'button',
    'label'=>$CI->lang->line("ACTION_SAVE"),
    'id'=>'button_action_save',
    'data-form'=>'#save_form'
);
$action_buttons[]=array(
    'type'=>'button',
    'label'=>$CI->lang->line("ACTION_CLEAR"),
    'id'=>'button_action_clear',
    'data-form'=>'#save_form'
);
$CI->load->view('action_buttons',array('action_buttons'=>$action_buttons));
?>
<form id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save_file');?>" method="post">
    <input type="hidden" id="variety_id" name="item[variety_id]" value="<?php echo $item_head['variety_id']; ?>" />
    <input type="hidden" id="id" name="id" value="<?php echo $item['id']; ?>" />
    <div class="row widget">
        <div class="widget-header">
            <div class="title">
                <?php echo $title; ?>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <label class=""><a class="external text-danger" data-toggle="collapse" data-target="#collapse_variety_info" href="#">+ Variety Info</a></label>
                </h4>
            </div>
            <div id="collapse_variety_info" class="panel-collapse collapse">
                <div class="row show-grid">
                    <div class="col-xs-4">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_NAME');?></label>
                    </div>
                    <div class="col-sm-4 col-xs-8">
                        <label class="control-label"><?php echo $item_head['crop_name'];?></label>
                    </div>
                </div>

                <div class="row show-grid">
                    <div class="col-xs-4">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_TYPE_NAME');?></label>
                    </div>
                    <div class="col-sm-4 col-xs-8">
                        <label class="control-label"><?php echo $item_head['crop_type_name'];?></label>
                    </div>
                </div>
                <div class="row show-grid">
                    <div class="col-xs-4">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_NAME');?></label>
                    </div>
                    <div class="col-sm-4 col-xs-8">
                        <label class="control-label"><?php echo $item_head['name'];?></label>
                    </div>
                </div>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Picture</label>
            </div>


            <div class="col-xs-4">
                <input type="file" class="browse_button" data-preview-container="#image_variety_info" data-preview-width="300" name="file_name">
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"></label>
            </div>
            <div class="col-xs-4" id="image_variety_info">
                <img style="max-width: 250px;" src="<?php echo $CI->config->item('system_base_url_arm_variety_info').$item['file_location']; ?>" alt="<?php echo $item['file_name']; ?>">
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_REMARKS');?></label>
            </div>
            <div class="col-xs-4">
                <textarea name="item[remarks]" class="form-control"><?php echo $item['remarks'] ?></textarea>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_STATUS');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="status" name="item[status]" class="form-control">
                    <option value="<?php echo $CI->config->item('system_status_active'); ?>" <?php if ($item['status'] == $CI->config->item('system_status_active')) { echo "selected='selected'"; } ?> ><?php echo $CI->lang->line('ACTIVE') ?></option>
                    <option value="<?php echo $CI->config->item('system_status_inactive'); ?>" <?php if ($item['status'] == $CI->config->item('system_status_inactive')) { echo "selected='selected'"; } ?> ><?php echo $CI->lang->line('INACTIVE') ?></option>
                </select>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
</form>
<script type="text/javascript">
    jQuery(document).ready(function()
    {
        system_preset({controller:'<?php echo $CI->router->class; ?>'});
        $(".browse_button").filestyle({input: false,icon: false,buttonText: "Upload Picture",buttonName: "btn-primary"});
    });
</script>

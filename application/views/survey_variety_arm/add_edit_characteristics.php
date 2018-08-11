<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_BACK"),
    'href'=>site_url($CI->controller_url)
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
<form class="form_valid" id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save');?>" method="post">
    <input type="hidden" id="id" name="id" value="<?php echo $item_head['id']; ?>" />
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
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CHARACTERISTICS');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <textarea class="form-control" name="item[characteristics]"><?php echo $item['characteristics']; ?></textarea>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Cultivation Period 1</label>
            </div>
            <div class="col-xs-2">
                <input type="text" name="item[date_start1]" class="form-control datepicker" value="<?php if($item['date_start1']!=0){echo date('d-F',$item['date_start1']);}?>" readonly/>
            </div>
            <div class="col-xs-2">
                <input type="text" name="item[date_end1]" class="form-control datepicker" value="<?php if($item['date_end1']!=0){echo date('d-F',$item['date_end1']);}?>" readonly/>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Cultivation Period 2</label>
            </div>
            <div class="col-xs-2">
                <input type="text" name="item[date_start2]" class="form-control datepicker" value="<?php if($item['date_start2']!=0){echo date('d-F',$item['date_start2']);}?>" readonly/>
            </div>
            <div class="col-xs-2">
                <input type="text" name="item[date_end2]" class="form-control datepicker" value="<?php if($item['date_end2']!=0){echo date('d-F',$item['date_end2']);}?>" readonly/>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Compare With Other Variety</label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <textarea class="form-control" name="item[comparison]"><?php echo $item['comparison']; ?></textarea>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_REMARKS');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <textarea class="form-control" name="item[remarks]"><?php echo $item['remarks']; ?></textarea>
            </div>
        </div>
    </div>

    <div class="clearfix"></div>
</form>
<script type="text/javascript">
    jQuery(document).ready(function()
    {
        system_preset({controller:'<?php echo $CI->router->class; ?>'});
        $(".datepicker").datepicker({dateFormat : 'dd-MM'});
    });
</script>

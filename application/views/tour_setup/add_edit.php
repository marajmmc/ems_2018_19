<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();

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
$CI->load->view("action_buttons",array('action_buttons'=>$action_buttons));
?>
<style>
    .datepicker{
        cursor: pointer !important;
    }
</style>
<form class="form_valid" id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save');?>" method="post">
<input type="hidden" id="id" name="id" value="<?php echo $item['id']; ?>" />
<input type="hidden" id="system_save_new_status" name="system_save_new_status" value="0" />
<div class="row widget">
    <div class="widget-header">
        <div class="title">
            <?php echo $title; ?>
        </div>
        <div class="clearfix"></div>
    </div>

    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right">Name</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label"><?php echo $item['name']?> (<?php echo $item['employee_id']?>)</label>
        </div>
    </div>

    <?php if($item['designation']){?>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Designation</label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $item['designation']?></label>
            </div>
        </div>
    <?php } ?>

    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right">Department</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label"><?php if($item['department_name']){echo $item['department_name'];}else{echo 'N/A';}?></label>
        </div>
    </div>

    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right">Title<span style="color:#FF0000">*</span></label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <input type="text" name="item[title]" id="title" class="form-control" value="<?php echo $item['title'];?>"/>
        </div>
    </div>

    <div style="" class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE').' From';?><span style="color:#FF0000">*</span></label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <input type="text" name="item[date_from]" class="form-control datepicker" value="<?php echo System_helper::display_date($item['date_from']);?>" readonly/>
        </div>
    </div>

    <div style="" class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE').' To';?><span style="color:#FF0000">*</span></label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <input type="text" name="item[date_to]" class="form-control datepicker" value="<?php echo System_helper::display_date($item['date_to']);?>" readonly/>
        </div>
    </div>

    <div id="tour_setup_container">
        <div style="overflow-x: auto;" class="row show-grid">
            <div class="col-xs-4">

            </div>
            <div class="col-sm-4 col-xs-8">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th style="min-width: 250px;">Purpose</th>
                    </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach($items as $item_purpose)
                        {
                            ?>
                            <tr>
                                <td>
                                    <input type="text" class="form-control purpose" name="items[<?php echo $item_purpose['id']; ?>]" value="<?php echo $item_purpose['purpose']; ?>" />
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-sm system_button_add_delete"><?php echo $CI->lang->line('DELETE'); ?></button>
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
            <button type="button" class="btn btn-warning system_button_add_more pull-right" data-current-id="0"><?php echo $CI->lang->line('LABEL_ADD_MORE');?></button>
        </div>
        <div class="col-xs-4">

        </div>
    </div>

    <!-----Added By Mahmud------>
    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_AMOUNT_IOU');?><span style="color:#FF0000">*</span></label>
        </div>
        <div class="col-xs-4">
            <input type="text" id="amount_iou" name="item[amount_iou]" value="<?php echo $item['amount_iou'] ?>" class="form-control float_type_positive price_unit_tk"  />
        </div>
    </div>

    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_IOU_DETAILS');?><span style="color:#FF0000">*</span></label>
        </div>
        <div class="col-xs-4">
            <textarea id="iou_details" name="item[iou_details]" class="form-control"><?php echo $item['iou_details'] ?></textarea>
        </div>
    </div>
    <!-----Added By Mahmud(END)------>

    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right">Remarks</label>
        </div>
        <div class="col-xs-4">
            <textarea id="remarks" name="item[remarks]" class="form-control"><?php echo $item['remarks'] ?></textarea>
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
                <input type="text" class="form-control purpose" />
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm system_button_add_delete"><?php echo $CI->lang->line('DELETE'); ?></button>
            </td>
        </tr>
        </tbody>
    </table>
</div>

<script type="text/javascript">

jQuery(document).ready(function()
{
    $(".datepicker").datepicker({dateFormat : display_date_format});

    $(document).off("click", ".system_button_add_more");
    $(document).off("click", ".system_button_add_delete");

    $(document).on("click", ".system_button_add_more", function(event)
    {
        var current_id=parseInt($(this).attr('data-current-id'));
        current_id=current_id-1;
        $(this).attr('data-current-id',current_id);
        var content_id='#system_content_add_more table tbody';
        $(content_id+' .purpose').attr('name','items['+current_id+']');
        var html=$(content_id).html();
        $("#tour_setup_container tbody").append(html);
    });
    $(document).on("click", ".system_button_add_delete", function(event)
    {
        $(this).closest('tr').remove();
    });

});
</script>

<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();
$action_buttons = array();
$action_buttons[] = array(
    'label' => $CI->lang->line("ACTION_BACK"),
    'href' => site_url($CI->controller_url . '/index/list/')
);
$action_buttons[] = array(
    'type' => 'button',
    'label' => $CI->lang->line("ACTION_CLEAR"),
    'id' => 'button_action_clear',
    'data-form' => '#save_form'
);
$CI->load->view('action_buttons', array('action_buttons' => $action_buttons));
?>
<style>
    .purpose-list table tr td:first-child {
        width: 50px
    }
    label{margin-top:5px}
    label.normal{font-weight:normal !important}
    .remarks-req {
        color: #FF0000;
        display: none;
        font-style:italic;
    }
</style>

<div class="row widget">
    <div class="widget-header">
        <div class="title">
            <?php echo $title; ?>
        </div>
        <div class="clearfix"></div>
    </div>

    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right">Name:</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label"><?php echo $item['name'] ?></label>
        </div>
    </div>

    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right">Designation:</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label"><?php echo ($item['designation'])? $item['designation'] :'N/A'; ?></label>
        </div>
    </div>

    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right">Department:</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label"><?php echo ($item['department_name']) ? $item['department_name'] :'N/A'; ?></label>
        </div>
    </div>

    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right">Title:</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label"><?php echo $item['title'] ?></label>
        </div>
    </div>

    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE'); ?>:</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            From &nbsp;<label class="control-label"><?php echo System_helper::display_date($item['date_from']) ?></label> &nbsp;
            To &nbsp;<label class="control-label"><?php echo System_helper::display_date($item['date_to']) ?></label>
        </div>
    </div>

    <?php echo Tour_helper::tour_purpose_view($item['tour_setup_id']); ?>

    <?php echo Tour_helper::iou_items_summary_view('', $item); ?>

    <?php if($item['remarks']){ ?>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Remarks:</label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label normal"><?php echo nl2br($item['remarks']); ?></label>
            </div>
        </div>
    <?php } ?>

    <form class="form_valid" id="save_form" action="<?php echo site_url($CI->controller_url . '/index/save_approve'); ?>" method="post">
        <input type="hidden" id="id" name="id" value="<?php echo $item['tour_setup_id']; ?>"/>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Approve <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select name="item[status_approved_tour]" class="form-control status-combo">
                    <option value=""><?php echo $this->lang->line('SELECT'); ?></option>
                    <option value="<?php echo $CI->config->item('system_status_approved') ?>">Approve</option>
                    <option value="<?php echo $CI->config->item('system_status_rollback') ?>">Roll Back</option>
                    <option value="<?php echo $CI->config->item('system_status_rejected') ?>">Reject</option>
                </select>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Supervisors Remarks <span class="remarks-req">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <textarea name="item[remarks_approve_reject]" class="form-control"></textarea>
            </div>
            <div class="col-xs-4">
                <label class="control-label normal remarks-req"> </label>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">

            </div>
            <div class="col-sm-4 col-xs-4">
                <div class="action_button pull-right">
                    <button id="button_action_save" type="button" class="btn" data-form="#save_form">Save</button>
                </div>
            </div>
            <div class="col-sm-4 col-xs-4">

            </div>
        </div>
    </form>
</div>
<div class="clearfix"></div>

<script type="text/javascript">
    jQuery(document).ready(function () {
        $(".status-combo").on('change', function (event) {
            $(".remarks-req").css('display','none');
            var options = $(this).val();
            if (options == '<?php echo $this->config->item('system_status_approved'); ?>') {
                $("#button_action_save").attr('data-message-confirm', '<?php echo $this->lang->line('MSG_CONFIRM_APPROVE'); ?>');
            } else if (options == '<?php echo $this->config->item('system_status_rollback'); ?>') {
                $("label.remarks-req").text('This field is required for Rollback');
                $(".remarks-req").css('display','inline-block');
                $("#button_action_save").attr('data-message-confirm', '<?php echo $this->lang->line('MSG_CONFIRM_ROLLBACK'); ?>');
            } else if (options == '<?php echo $this->config->item('system_status_rejected'); ?>') {
                $("label.remarks-req").text('This field is required for Reject');
                $(".remarks-req").css('display','inline-block');
                $("#button_action_save").attr('data-message-confirm', '<?php echo $this->lang->line('MSG_CONFIRM_REJECT'); ?>');
            } else {
                $("#button_action_save").removeAttr('data-message-confirm');
            }
        });
    });
</script>

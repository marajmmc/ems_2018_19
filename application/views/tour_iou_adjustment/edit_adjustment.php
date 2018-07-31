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

    label {
        margin-top: 5px
    }

    label.normal, span.normal {
        font-weight: normal !important
    }

    .right-align {
        text-align: right !important
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
            <label class="control-label"><?php echo $item['name'] ?> (<?php echo $item['employee_id'] ?>)</label>
        </div>
    </div>

    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right">Designation:</label>
        </div>
        <div class="col-sm-5 col-xs-8">
            <label class="control-label"><?php echo ($item['designation']) ? $item['designation'] : 'N/A'; ?></label>
        </div>
    </div>

    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right">Department:</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label">
                <?php echo ($item['department_name']) ? $item['department_name'] : 'N/A'; ?>
            </label>
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
            From &nbsp;<label class="control-label"><?php echo System_helper::display_date($item['date_from']) ?></label> &nbsp; To &nbsp;<label class="control-label"><?php echo System_helper::display_date($item['date_to']) ?></label>
        </div>
    </div>

    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right">Duration:</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label"><?php echo Tour_helper::tour_duration($item['tour_setup_id']); ?></label>
        </div>
    </div>

    <?php echo Tour_helper::iou_items_summary_view('', $item); ?>

    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right">IOU Amount Already Paid:</label>
        </div>
        <div class="col-sm-4 col-xs-8 right-align">
            <label class="control-label"><?php echo System_helper::get_string_amount($total_iou_amount); ?></label>
        </div>
    </div>

    <form class="form_valid" id="save_form" action="<?php echo site_url($CI->controller_url . '/index/save_adjustment'); ?>" method="post">
        <input type="hidden" id="id" name="id" value="<?php echo $item['tour_setup_id']; ?>"/>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">IOU Voucher Amount <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="item[amount_iou_adjustment]" class="form-control float_type_positive price_unit_tk iou_adjustment_input" value="<?php echo $item['amount_iou_adjustment']; ?>" />
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Adjustment:</label>
            </div>
            <div class="col-sm-4 col-xs-8 right-align">
                <label class="control-label adjustment_amount"> 0.00 </label>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                &nbsp;
            </div>
            <div class="col-sm-4 col-xs-4">
                <div class="action_button pull-right" style="margin-right:0">
                    <button id="button_action_save" type="button" class="btn" data-form="#save_form">Save</button>
                </div>
            </div>
            <div class="col-sm-4 col-xs-4">
                &nbsp;
            </div>
        </div>
    </form>

    <div class="clearfix"></div>
</div>

<script type="text/javascript">
    jQuery(document).ready(function ($) {
        var exp_amt = parseFloat($(".iou_adjustment_input").val().trim());
        check_input_amount(exp_amt); // Called, when Page Loads First time

        $(".iou_adjustment_input").on("change keyup", function (event) {
            var exp_amt = parseFloat($(this).val().trim());
            check_input_amount(exp_amt); // Called, when an Amount is Typed
        });
    });

    function check_input_amount(exp_amt){
        var paid_amt = parseFloat(<?php echo trim($total_iou_amount); ?>);
        if ((paid_amt !== '') && (exp_amt !== '') && !isNaN(paid_amt) && !isNaN(exp_amt)) {
            var adj_amt = exp_amt - paid_amt;
            if (adj_amt > 0) {
                var note = "(Pay To Employee)";
            } else if (adj_amt < 0) {
                var note = "(Return To Accounts)";
            } else {
                var note = "(No Adjustment Needed)";
            }
            note = '<span class="normal">' + note + '</span>';
        } else {
            var adj_amt = 0.00;
            note = '';
        }
        $(".adjustment_amount").html(note + " &nbsp;&nbsp;" + get_string_amount(Math.abs(adj_amt)));
    }
</script>

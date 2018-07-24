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

    label.normal {
        font-weight: normal !important
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

    <?php echo Tour_helper::tour_purpose_view($item['tour_setup_id']); ?>

    <?php echo Tour_helper::iou_items_summary_view('', $item); ?>

    <?php if (($item['remarks']))
    {
        ?>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Remarks:</label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label normal"><?php echo nl2br($item['remarks']); ?></label>
            </div>
        </div>
    <?php } ?>

    <form class="form_valid" id="save_form" action="<?php echo site_url($CI->controller_url . '/index/save'); ?>" method="post">
        <input type="hidden" id="id" name="id" value="<?php echo $item['tour_setup_id']; ?>"/>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_AMOUNT_IOU_PAY'); ?> <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-xs-4">
                <input type="text" name="item[amount_iou_payment]" value="<?php echo ($item['amount_iou_payment'] > 0) ? $item['amount_iou_payment'] : $item['amount_iou_request']; ?>" class="form-control float_type_positive price_unit_tk iou_payment_input"/>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                &nbsp;
            </div>
            <div class="col-sm-4 col-xs-4">
                <div class="action_button pull-right">
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
    jQuery(document).ready(function () {
        $(".iou_payment_input").on("blur", function (event) { // Puts a Zero if blank
            var iou_value = parseFloat($(this).val());
            if (iou_value == '' || isNaN(iou_value)) {
                $(this).val('0');
            }
        });
    });
</script>

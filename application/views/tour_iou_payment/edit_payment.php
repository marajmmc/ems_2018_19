<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();

$action_buttons = array();
$action_buttons[] = array(
    'label' => $CI->lang->line("ACTION_BACK"),
    'href' => site_url($CI->controller_url . '/index/list/')
);
if (isset($CI->permissions['action4']) && ($CI->permissions['action4'] == 1))
{
    $action_buttons[] = array(
        'type' => 'button',
        'label' => 'Print Requisition',
        'onClick' => "window.print()"
    );
}
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
    .req-print-wrap > div .hidden-print{display:none !important;}
    @media print {
        .req-print-wrap{border:none !important;}
        .req-print-wrap div{padding:0 !important;}
    }
</style>

<div class="row widget hidden-print">
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
            <label class="control-label"><?php echo $item['name']; ?> (<?php echo $item['employee_id'] ?>)</label>
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
            <label class="control-label"><?php echo $item['title']; ?></label>
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

    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right">Approved IOU Amount:</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label"><?php echo System_helper::get_string_amount($total_iou_amount); ?></label>
        </div>
    </div>

    <?php if(!empty($item['remarks_approved_payment'])){ ?>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Approver's Remarks:</label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo nl2br($item['remarks_approved_payment']); ?></label>
            </div>
        </div>
    <?php } ?>

    <form class="form_valid" id="save_form" action="<?php echo site_url($CI->controller_url . '/index/save_payment'); ?>" method="post">
        <input type="hidden" id="id" name="id" value="<?php echo $item['tour_setup_id']; ?>"/>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Payment <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select name="item[status_paid_payment]" class="form-control status-combo">
                    <option value=""><?php echo $this->lang->line('SELECT'); ?></option>
                    <option value="<?php echo $this->config->item('system_status_paid'); ?>">Paid</option>
                </select>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Remarks</label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <textarea name="item[remarks_paid_payment]" class="form-control"></textarea>
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


<div class="row widget req-print-wrap">
    <div class="widget-header hidden-print">
        <div class="title"> Requisition Print Page </div>
        <div class="clearfix"></div>
    </div>

    <div class="row show-grid">
        <div class="col-xs-12">
            <?php echo $requisition_print_page; ?>
        </div>
    </div>
</div>

<script type="text/javascript">
    jQuery(document).ready(function ($) {
        system_off_events(); // Triggers

        $(".status-combo").on('change', function (event) {
            var options = $(this).val();
            if (options == '<?php echo $this->config->item('system_status_paid'); ?>') {
                $("#button_action_save").attr('data-message-confirm', '<?php echo $this->lang->line('MSG_CONFIRM_PAYMENT'); ?>');
            } else {
                $("#button_action_save").removeAttr('data-message-confirm');
            }
        });
    });
</script>

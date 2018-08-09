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

    .center-align {
        text-align: center !important
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
            <label class="control-label"><?php echo Tour_helper::tour_duration($item['date_from'], $item['date_to']); ?></label>
        </div>
    </div>

    <?php
    $iou_items = Tour_helper::get_iou_items();
    if ($iou_items)
    {
        $i = 0;
        $amount_iou_items = array();
        $total_iou_amount = 0.0;
        $total_voucher_amount = 0.0;

        $amount_iou_adj_items = $amount_iou_items = json_decode($item['amount_iou_items'], TRUE);
        if ($item['amount_iou_adjustment_items'] && ($item['amount_iou_adjustment_items'] != ''))
        {
            $amount_iou_adj_items = json_decode($item['amount_iou_adjustment_items'], TRUE);
        }
        ?>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">IOU Items:</label>
            </div>
            <div class="col-xs-2 right-align" style="border-bottom:1px solid #000; padding-bottom:5px">
                <label class="control-label normal"><br/> Item </label>
            </div>
            <div class="col-xs-1 right-align" style="border-bottom:1px solid #000; padding-bottom:5px">
                <label class="control-label normal"><br/> Paid</label>
            </div>
            <div class="col-xs-1 right-align" style="border-bottom:1px solid #000; padding-bottom:5px">
                <label class="control-label normal">Voucher<br/>Amount</label>
            </div>
        </div>
        <?php
        foreach ($iou_items as $iou_item)
        {
            $iou_amount = $iou_adj_amount = 0;
            if (isset($amount_iou_items[$iou_item]))
            {
                $iou_amount = $amount_iou_items[$iou_item];
            }
            if (isset($amount_iou_adj_items[$iou_item]))
            {
                $iou_adj_amount = $amount_iou_adj_items[$iou_item];
            }
            ?>
            <div class="row show-grid">
                <div class="col-xs-4"> &nbsp;</div>
                <div class="col-xs-2">
                    <label class="control-label pull-right normal"><?php echo Tour_helper::to_label($iou_item); ?>:</label>
                </div>
                <div class="col-xs-1" style="padding-left:0">
                    <label class="control-label pull-right"><?php echo(System_helper::get_string_amount($iou_amount)); ?></label>
                </div>
                <div class="col-xs-1">
                    <label class="control-label pull-right"><?php echo(System_helper::get_string_amount($iou_adj_amount)); ?></label>
                </div>
            </div>
            <?php
            $total_iou_amount += $iou_amount;
            if ($item['amount_iou_adjustment_items'] && ($item['amount_iou_adjustment_items'] != ''))
            {
                $total_voucher_amount += $iou_adj_amount;
            }
            $i++;
        }
        ?>
        <div class="row show-grid" style="margin-bottom:15px">
            <div class="col-xs-4"> &nbsp; </div>
            <div class="col-xs-2" style="border-top:1px solid #000; padding-top:5px">
                <label class="control-label pull-right normal">Total:</label>
            </div>
            <div class="col-xs-1 right-align" style="border-top:1px solid #000; padding-top:5px; padding-left:0;">
                <label class="control-label"><?php echo System_helper::get_string_amount($total_iou_amount); ?></label>
            </div>
            <div class="col-xs-1 right-align" style="border-top:1px solid #000; padding-top:5px; padding-left:0;">
                <label class="control-label voucher_amount"><?php echo System_helper::get_string_amount($total_voucher_amount); ?></label>
            </div>
        </div>

        <div class="row show-grid" style="margin-bottom:40px">
            <div class="col-xs-4"> &nbsp; </div>
            <div class="col-xs-2">
                <label class="control-label pull-right normal">Adjustment:</label>
            </div>
            <div class="col-xs-2 right-align" style="padding-left:0;">
                <label class="control-label adjustment_amount">
                    <?php
                    $adj_amt = $total_voucher_amount - $total_iou_amount;
                    if ($adj_amt > 0)
                    {
                        $note = "(Pay To Employee)";
                    }
                    else if ($adj_amt < 0)
                    {
                        $note = "(Return To Accounts)";
                    }
                    else
                    {
                        $note = "(No Adjustment Needed)";
                    }
                    $note = '<br/><span class="normal">' . $note . '</span>';
                    echo System_helper::get_string_amount(abs($adj_amt)).$note;
                    ?>
                </label>
            </div>
        </div>
    <?php } ?>

    <form class="form_valid" id="save_form" action="<?php echo site_url($CI->controller_url . '/index/save_approve'); ?>" method="post">
        <input type="hidden" id="id" name="id" value="<?php echo $item['tour_setup_id']; ?>"/>

        <div class="row show-grid" style="margin-top:25px">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_APPROVE'); ?>
                    <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select name="item[status_approved_adjustment]" class="form-control status-combo">
                    <option value=""><?php echo $this->lang->line('SELECT'); ?></option>
                    <option value="<?php echo $this->config->item('system_status_approved'); ?>">Approve</option>
                </select>
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
    jQuery(document).ready(function () {
        $(".status-combo").on('change', function (event) {
            var options = $(this).val();
            if (options == '<?php echo $this->config->item('system_status_approved'); ?>') {
                $("#button_action_save").attr('data-message-confirm', '<?php echo $this->lang->line('MSG_CONFIRM_APPROVE'); ?>');
            } else {
                $("#button_action_save").removeAttr('data-message-confirm');
            }
        });
    });
</script>

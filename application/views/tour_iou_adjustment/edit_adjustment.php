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

    .table-wrap table tr td {
        padding: 5px;
    }

    .col-head {
        border-bottom: 1px solid #333;
    }

    .col-bottom {
        border-top: 1px solid #333;
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

    <form class="form_valid" id="save_form" action="<?php echo site_url($CI->controller_url . '/index/save_adjustment'); ?>" method="post">
        <input type="hidden" id="id" name="id" value="<?php echo $item['tour_setup_id']; ?>"/>

        <?php
        $iou_items = Tour_helper::get_iou_items();
        if ($iou_items)
        {
            $i = 0;
            $amount_iou_items = array();
            $total_iou_amount = 0.0;
            $amount_iou_adj_items = array();
            $total_voucher_amount = 0.0;
            $adjustment_done = 0; // Flag for Adjustment note

            $amount_iou_adj_items = $amount_iou_items = json_decode($item['amount_iou_items'], TRUE);
            if ($item['amount_iou_adjustment_items'] && ($item['amount_iou_adjustment_items'] != ''))
            {
                $amount_iou_adj_items = json_decode($item['amount_iou_adjustment_items'], TRUE);
                $adjustment_done = 1;
            }
            ?>
            <div class="row show-grid" style="margin-bottom:40px">
                <div class="col-xs-4">
                    <label class="control-label pull-right">IOU Items:</label>
                </div>
                <div class="col-xs-5 right-align table-wrap">
                    <table>
                        <tr class="col-head">
                            <td><label class="control-label"> Item </label></td>
                            <td><label class="control-label"> Paid </label></td>
                            <td style="width:30%" class="right-align">
                                <label class="control-label"> Voucher Amount </label></td>
                        </tr>
                        <?php
                        foreach ($iou_items as $key => $iou_item)
                        {
                            $iou_amount = $iou_adj_amount = 0;
                            if (isset($amount_iou_items[$key]))
                            {
                                $iou_amount = $amount_iou_items[$key];
                            }
                            if (isset($amount_iou_adj_items[$key]))
                            {
                                $iou_adj_amount = $amount_iou_adj_items[$key];
                            }

                            if (($iou_item['status'] == $CI->config->item('system_status_inactive')) && !($iou_amount > 0))
                            {
                                continue;
                            }
                            ?>
                            <tr>
                                <td><?php echo $iou_item['name']; ?>:</td>
                                <td><?php echo(System_helper::get_string_amount($iou_amount)); ?></td>
                                <td style="padding-left:20px">
                                    <input type="text" name="items[<?php echo $key; ?>]" class="form-control float_type_positive price_unit_tk iou_adjustment_input" value="<?php echo $iou_adj_amount; ?>"/>
                                </td>
                            </tr>

                            <?php
                            $total_iou_amount += $iou_amount;
                            if ($item['amount_iou_adjustment_items'] && ($item['amount_iou_adjustment_items'] != ''))
                            {
                                $total_voucher_amount += $iou_adj_amount;
                            }
                            $i++;
                        }
                        ?>
                        <tr class="col-bottom">
                            <td><label class="control-label"> Total: </label></td>
                            <td>
                                <label class="control-label"> <?php echo System_helper::get_string_amount($total_iou_amount); ?> </label>
                            </td>
                            <td>
                                <label class="control-label voucher_amount"> <?php echo System_helper::get_string_amount($total_voucher_amount); ?> </label>
                            </td>
                        </tr>
                        <tr>
                            <td><label class="control-label"> Adjustment: </label></td>
                            <td colspan="2"><label class="control-label adjustment_amount"> 0.00 </label></td>
                        </tr>
                    </table>
                </div>
            </div>
        <?php } ?>

        <div class="row show-grid">
            <div class="col-xs-12">
                <div class="action_button center-align" style="width:100%;">
                    <button id="button_action_save" type="button" class="btn" data-form="#save_form">Save</button>
                </div>
            </div>
        </div>
    </form>

    <div class="clearfix"></div>
</div>

<script type="text/javascript">
    jQuery(document).ready(function ($) {
        check_input_amount(false); // When page loads 1st time

        $(document).on("change keyup", ".iou_adjustment_input", function (event) {
            check_input_amount(true); // When voucher amounts are entered
        });
    });

    function check_input_amount(show_note) {
        var sum = parseFloat(0);
        var item_amount = parseFloat(0);
        $(".iou_adjustment_input").each(function (e) {
            item_amount = parseFloat($(this).val());
            if (!isNaN(item_amount) && (item_amount > 0)) {
                sum += item_amount;
            }
        });
        $(".voucher_amount").text(get_string_amount(sum));

        var paid_amt = parseFloat(<?php echo trim($total_iou_amount); ?>);
        var expense_amt = sum;
        var note = "";
        var adj_done = <?php echo $adjustment_done; ?>;

        if ((paid_amt !== '') && (expense_amt !== '') && !isNaN(paid_amt) && !isNaN(expense_amt)) {
            var adj_amt = expense_amt - paid_amt;
        } else {
            var adj_amt = 0.00;
        }

        if (show_note || (adj_done == 1)) { // When 'KEYUP/CHANGE' event is fired
            if (adj_amt > 0) {
                note = "(Pay To Employee)";
            } else if (adj_amt < 0) {
                note = "(Return To Accounts)";
            } else {
                note = "(No Adjustment Needed)";
            }

            if (note != "") {
                note = '<span class="normal">' + note + '</span> &nbsp;';
            }
        } else {
            note = '';
        }

        var final_txt = note + get_string_amount(Math.abs(adj_amt));
        $(".adjustment_amount").html(final_txt);
    }

    $(document).on("blur", ".iou_adjustment_input", function (event) { // Puts a Zero if blank
        var iou_value = parseFloat($(this).val());
        if (iou_value == '' || isNaN(iou_value)) {
            $(this).val('0');
        }
    });
</script>

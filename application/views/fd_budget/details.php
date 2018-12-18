<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI =& get_instance();

$action_buttons = array();
if (isset($method_from))
{
    $action_buttons[] = array(
        'label' => $CI->lang->line("ACTION_BACK"),
        'href' => site_url($CI->controller_url . '/index/' . $method_from)
    );
}
else
{
    $action_buttons[] = array(
        'label' => $CI->lang->line("ACTION_BACK"),
        'href' => site_url($CI->controller_url)
    );
}

if (isset($CI->permissions['action4']) && ($CI->permissions['action4'] == 1))
{
    $action_buttons[] = array(
        'type' => 'button',
        'label' => $CI->lang->line("ACTION_PRINT"),
        'onClick' => "window.print()"
    );
}
$CI->load->view('action_buttons', array('action_buttons' => $action_buttons));

?>

<div class="row widget">
    <div class="widget-header" style="margin-bottom:20px">
        <div class="title"><?php echo $title; ?></div>
        <div class="clearfix"></div>
    </div>

    <?php echo $CI->load->view("info_basic",'',true); ?>


    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <label><a class="external text-danger" data-toggle="collapse" data-target="#collapse1" href="#"> + Budget Expense &amp; Payment Summary</a></label>
            </h4>
        </div>
        <div id="collapse1" class="panel-collapse collapse out">
            <table class="table table-bordered table-responsive system_table_details_view">
                <tr>
                    <td class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_STATUS_PAYMENT_APPROVE'); ?></label></td>
                    <td>
                        <label class="control-label"><?php echo $item['status_payment_approve']; ?></label>
                    </td>
                    <td rowspan="4" style="width:50%">
                        <div class="row show-grid">
                            <div class="col-xs-12" style="padding:0">
                                <table style="width:100%">
                                    <?php
                                    $total_budget = 0;
                                    foreach ($expense_items as $expense)
                                    {
                                        ?>
                                        <tr>
                                            <td style="text-align:right"><?php echo $expense['name']; ?> :</td>
                                            <td style="text-align:right; padding:10px; font-weight:bold"><?php echo System_helper::get_string_amount($expense['amount']); ?></td>
                                        </tr>
                                        <?php
                                        $total_budget += $expense['amount'];
                                    }
                                    ?>

                                    <tr>
                                        <td style="border-top:1px solid #000; text-align:right; font-weight:bold"><?php echo $CI->lang->line('LABEL_TOTAL_FIELD_DAY_BUDGET'); ?> :</td>
                                        <td style="border-top:1px solid #000; text-align:right; font-weight:bold; width:25%; padding:10px"><?php echo System_helper::get_string_amount($total_budget); ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_STATUS_PAYMENT_PAY'); ?></label></td>
                    <td>
                        <label class="control-label"><?php echo $item['status_payment_pay']; ?></label>
                    </td>
                </tr>
                <tr>
                    <td class="widget-header header_caption"><label class="control-label pull-right">Adjustment Forward Status</label></td>
                    <td>
                        <label class="control-label">Pending</label>
                    </td>
                </tr>
                <tr>
                    <td class="widget-header header_caption"><label class="control-label pull-right">Adjustment Approval Status</label></td>
                    <td>
                        <label class="control-label">Pending</label>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>

<script type="application/javascript">
    jQuery(document).ready(function ($) {
        system_off_events(); // Triggers

        /* $('a.ext_details').click(function (e) {
            e.preventDefault();
            var $anchor = $('tr#ext_details').offset();
            window.scrollTo($anchor.left, $anchor.top);
            return false;
        }); */
    });
</script>

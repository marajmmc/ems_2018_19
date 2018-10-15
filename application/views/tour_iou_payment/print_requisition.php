<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI =& get_instance();

$action_buttons = array();
$action_buttons[] = array(
    'label' => $CI->lang->line("ACTION_BACK") . ' to Pending List',
    'href' => site_url($CI->controller_url)
);
$action_buttons[] = array(
    'label' => $CI->lang->line("ACTION_BACK") . ' to All list',
    'href' => site_url($CI->controller_url . '/index/list_all')
);
if (isset($CI->permissions['action4']) && ($CI->permissions['action4'] == 1))
{
    $action_buttons[] = array(
        'type' => 'button',
        'label' => $CI->lang->line("ACTION_PRINT"),
        'onClick' => "window.print()"
    );
}
$CI->load->view('action_buttons', array('action_buttons' => $action_buttons));

// Fetching Approver Info.
if(isset($item['user_approved_payment']) && ($item['user_approved_payment'] != ""))
{
    $user_ids = array($item['user_approved_payment']);
    $users_info = System_helper::get_users_info($user_ids);
}
/*-------------------------------- PAGE PRINT CONFIGURATION -----------------------------------*/
$width = 8.27 * 100;
$height = 11.69 * 100;
$row_per_page = 20;
$header_image = $CI->config->item('system_base_url_picture').'images/print/header.jpg';
$footer_image = $CI->config->item('system_base_url_picture').'images/print/footer.jpg';

$result = Query_helper::get_info($CI->config->item('table_system_setup_print'), '*', array('controller ="' . $this->controller_url . '"', 'method ="print_requisition"'), 1);
if ($result)
{
    $width = $result['width'] * 100;
    $height = $result['height'] * 100;
    $row_per_page = $result['row_per_page'];
    $header_image = $CI->config->item('system_base_url_picture') . $result['image_header_location'];
    $footer_image = $CI->config->item('system_base_url_picture') . $result['image_footer_location'];
}

/*----------------------IOU Items------------------------------*/
$iou_items = Tour_helper::get_iou_items();
$amount_iou_items = json_decode($item['amount_iou_items'], TRUE);

/*-------------------------------- No. of PAGE CONFIGURATION -----------------------------------*/
$total_records = sizeof($amount_iou_items);
$num_pages = ceil($total_records / $row_per_page);
?>

<div id="system_print_container" style="width:<?php echo $width; ?>px;">
    <?php
    for ($page = 0; $page < $num_pages; $page++)
    {
        ?>
        <div class="page page_no_<?php echo $page; ?>" style="width:<?php echo $width; ?>px;height:<?php echo $height; ?>px;">

            <div class="row show-grid">
                <div class="col-xs-12">
                    <img src="<?php echo $header_image; ?>" style="width: 100%">
                    <div class="clearfix"></div>
                </div>
            </div>

            <div class="row show-grid">
                <div class="col-xs-12">
                    <table class="table table-bordered" style="margin:0">
                        <tr>
                            <td><strong>Tour Title</strong></td>
                            <td colspan="3"><?php echo $item['title']; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Tour ID</strong></td>
                            <td><?php echo $item['tour_setup_id']; ?></td>
                            <td><strong><?php echo $CI->lang->line('LABEL_DATE'); ?></strong></td>
                            <td>
                                <b><?php echo System_helper::display_date($item['date_from']) ?></b>
                                &nbsp; to &nbsp; <b><?php echo System_helper::display_date($item['date_to']) ?></b>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Name</strong></td>
                            <td><?php echo $item['name'] ?></td>
                            <td><strong>Department</strong></td>
                            <td><?php if ($item['department_name'])
                                {
                                    echo $item['department_name'];
                                }
                                else
                                {
                                    echo 'N/A';
                                } ?>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Designation</strong></td>
                            <td>
                                <?php if ($item['designation'])
                                {
                                    echo $item['designation'];
                                }
                                else
                                {
                                    echo 'N/A';
                                } ?>
                            </td>
                            <td><strong>Employee ID</strong></td>
                            <td><?php echo $item['employee_id']; ?></td>
                        </tr>
                        <?php
                        if(isset($item['user_approved_payment']) && ($item['user_approved_payment'] != ""))
                        {
                        ?>
                        <tr>
                            <td><strong>Approved By</strong></td>
                            <td><?php echo $users_info[$item['user_approved_payment']]['name']; ?></td>
                            <td><strong>Approved Time</strong></td>
                            <td><?php echo System_helper::display_date_time($item['date_approved_payment']); ?></td>
                        </tr>
                        <?php
                        }
                        ?>
                    </table>
                </div>
            </div>
            <div class="row show-grid">
                <div class="col-xs-12">
                    <table class="table table-bordered" style="margin:0">
                        <tr>
                            <th style="width:7%"><?php echo $CI->lang->line('LABEL_SL_NO'); ?></th>
                            <th style="text-align:center">Purpose (IOU Item)</th>
                            <th style="text-align:center">Amount (BDT)</th>
                        </tr>
                        <?php
                        $i = 0;
                        $total_amount = 0;
                        foreach ($iou_items as $key => $iou_item)
                        {
                            if(!isset($amount_iou_items[$key]) || $amount_iou_items[$key] <=0){
                                continue;
                            }
                            $current_iou_amount = $amount_iou_items[$key];
                            ?>
                            <tr>
                                <td><?php echo ++$i; ?></td>
                                <td><?php echo $iou_item['name']; ?></td>
                                <td style="text-align:right"><?php echo System_helper::get_string_amount($current_iou_amount); ?></td>
                            </tr>
                            <?php
                            $total_amount += $current_iou_amount;
                        }
                        ?>
                        <tr>
                            <td colspan="2">
                                <strong>In Words:</strong> <?php echo Tour_helper::get_string_amount_inword($total_amount); ?>
                            </td>
                            <td style="width:25%;text-align:right">
                                <strong><?php echo System_helper::get_string_amount($total_amount); ?></strong>
                            </td>
                        </tr>
                        <?php if($item['remarks_approved_tour']!=""){ ?>
                        <tr>
                            <td colspan="3">
                                <strong>Remarks:</strong> <?php echo $item['remarks_approved_tour']; ?>
                            </td>
                        </tr>
                        <?php } ?>
                    </table>
                </div>
            </div>

            <div class="row show-grid">
                <div class="col-xs-12">
                    <img src="<?php echo $footer_image; ?>" style="width: 100%;" />
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    <?php
    }
    ?>
</div>

<script type="text/javascript">
    jQuery(document).ready(function () {
        system_off_events(); // Triggers
    });
</script>

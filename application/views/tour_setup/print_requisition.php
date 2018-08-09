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
$action_buttons[] = array(
    'label' => $CI->lang->line("ACTION_BACK") . ' to Waiting list',
    'href' => site_url($CI->controller_url . '/index/list_waiting')
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
/*-------------------------------- PAGE PRINT CONFIGURATION -----------------------------------*/
$width = 8.27 * 100;
$height = 11.69 * 100;
$row_per_page = 20;
$header_image = base_url('images/print/header.jpg');
$footer_image = base_url('images/print/footer.jpg');

$result = Query_helper::get_info($CI->config->item('table_system_setup_print'), '*', array('controller ="' . $this->controller_url . '"', 'method ="print_requisition"'), 1);
if ($result)
{
    $width = $result['width'] * 100;
    $height = $result['height'] * 100;
    $row_per_page = $result['row_per_page'];
    $header_image = base_url($CI->config->item('system_base_url_picture_setup_print') . $result['image_header_location']);
    $footer_image = base_url($CI->config->item('system_base_url_picture_setup_print') . $result['image_footer_location']);
}
// IOU Items
$items = json_decode($item['amount_iou_items']);
/*-------------------------------- No. of PAGE CONFIGURATION -----------------------------------*/
$total_records = sizeof($items);
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
                                <?php echo System_helper::display_date($item['date_from']) ?>
                                to <?php echo System_helper::display_date($item['date_to']) ?>
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
                        foreach ($items as $item_name => $amount)
                        {
                            if($amount <= 0){
                                continue;
                            }
                            ?>
                            <tr>
                                <td><?php echo ++$i; ?></td>
                                <td><?php echo Tour_helper::to_label($item_name); ?></td>
                                <td style="text-align:right"><?php echo System_helper::get_string_amount($amount); ?></td>
                            </tr>
                        <?php
                            $total_amount += $amount;
                        } ?>

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
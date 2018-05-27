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
?>
<?php
$width = 8.27 * 100;
$height = 11.69 * 100;
$row_per_page = 20;
$header_image = base_url('images/print/header.jpg');
$footer_image = base_url('images/print/footer.jpg');

$num_pages = 1;
?>

<div id="system_print_container" style="width:<?php echo $width; ?>px;">
    <?php
    for ($page = 0; $page < $num_pages; $page++)
    {
        ?>
        <div class="page page_no_<?php echo $page; ?>"
             style="width:<?php echo $width; ?>px;height:<?php echo $height; ?>px;position: relative;">
            <img src="<?php echo $header_image; ?>" style="width: 100%">

            <div class="row show-grid">
                <div class="col-xs-12">
                    <table class="table table-bordered">
                        <tbody>
                        <tr>
                            <td colspan="4"><strong>Tour Title :: <?php echo $item['title'] ?></strong></td>
                        </tr>
                        <tr>
                            <td style="width: 15%"><strong>Name</strong></td>
                            <td><?php echo $item['name'] ?></td>
                            <td style="width: 15%"><strong>Designation</strong></td>
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
                        </tr>
                        <tr>
                            <td style="width: 15%"><strong><?php echo $CI->lang->line('LABEL_DATE'); ?></strong></td>
                            <td>From: <?php echo System_helper::display_date($item['date_from']) ?>
                                To: <?php echo System_helper::display_date($item['date_to']) ?></td>
                            <td style="width: 15%"><strong>Department</strong></td>
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
                            <td style="width: 15%"><strong><?php echo $CI->lang->line('LABEL_AMOUNT_IOU'); ?></strong></td>
                            <td colspan="3"><?php echo number_format($item['amount_iou'], 2); ?></td>
                        </tr>
                        <tr>
                            <td style="width: 15%"><strong><?php echo $CI->lang->line('LABEL_IOU_DETAILS'); ?></strong></td>
                            <td colspan="3"><?php echo $item['iou_details']; ?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row show-grid">
                <div class="col-xs-12">
                    <table class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th colspan="2" class="text-center">Purpose(s)</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $i = 0;
                        foreach ($items as $row)
                        {
                            ?>
                            <tr>
                                <td style="width:7%"><?php echo ++$i; ?></td>
                                <td><?php echo $row['purpose']; ?></td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                    <?php if ($item['remarks'] || $item['superior_comment'])
                    {
                        ?>
                        <table class="table table-bordered table-striped">
                            <tbody>
                            <tr>
                                <td colspan="21" class="text-center"><strong>Remarks</strong></td>
                            </tr>
                            <tr>
                                <td style="width: 15%"><strong>Applicant </strong></td>
                                <td style="width: 15%"><strong>Supervisor</strong></td>
                            </tr>
                            <tr>
                                <td style="width: 15%"><?php if ($item['remarks'])
                                    {
                                        echo $item['remarks'];
                                    }
                                    else
                                    {
                                        echo 'N/A';
                                    } ?></td>
                                <td style="width: 15%"><?php if ($item['superior_comment'])
                                    {
                                        echo $item['superior_comment'];
                                    }
                                    else
                                    {
                                        echo 'N/A';
                                    } ?></td>
                            </tr>
                            </tbody>
                        </table>
                    <?php } ?>
                </div>
            </div>
            <img src="<?php echo $footer_image; ?>" style="width: 100%;position: absolute;left 0px;bottom: 0px;">
        </div>
    <?php
    }
    ?>
</div>
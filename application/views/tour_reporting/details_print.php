<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI =& get_instance();
$action_buttons = array();
$action_buttons[] = array(
    'label' => $CI->lang->line("ACTION_BACK"),
    'href' => site_url($CI->controller_url)
);
if (isset($CI->permissions['print']) && ($CI->permissions['print'] == 1))
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

$total_records = sizeof($items_purpose_others);
$num_pages = ceil($total_records / $row_per_page);
?>

<div id="system_print_container" style="width:<?php echo $width; ?>px;">
    <?php
    for ($page = 0; $page < $num_pages; $page++)
    {
        ?>
        <div class="page page_no_<?php echo $page; ?>" style="width:<?php echo $width; ?>px;height:<?php echo $height; ?>px;position: relative;">
            <img src="<?php echo $header_image; ?>" style="width: 100%">

            <div class="row show-grid">
                <div class="col-xs-12">
                    <table class="table table-bordered">
                        <tbody>
                        <tr>
                            <td style="width: 10%" colspan="21"><strong>Tour Title:: <?php echo $item['title'] ?></strong></td>
                        </tr>
                        <tr>
                            <td style="width: 10%"><strong>Name</strong></td>
                            <td><?php echo $item['name'] ?></td>
                            <td style="width: 10%"><strong>Designation</strong></td>
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
                            <td style="width: 10%"><strong><?php echo $CI->lang->line('LABEL_DATE'); ?></strong></td>
                            <td>From: <?php echo System_helper::display_date($item['date_from']) ?>
                                To: <?php echo System_helper::display_date($item['date_to']) ?>
                            </td>
                            <td style="width: 10%"><strong>Department</strong></td>
                            <td>
                                <?php if ($item['department_name'])
                                {
                                    echo $item['department_name'];
                                }
                                else
                                {
                                    echo 'N/A';
                                } ?>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row show-grid">
                <div class="col-xs-12">
                    <?php
                    if ($items_purpose_others)
                    {
                        $serial = 0;
                        foreach ($items_purpose_others as $items_purpose_other)
                        {
                            ++$serial;
                            ?>
                            <table class="table table-bordered">
                                <tbody>
                                <tr>
                                    <td colspan="21"><strong><?php echo $serial; ?>.
                                            Purpose:: <?php echo $items_purpose_other['purpose']; ?> </strong></td>
                                </tr>
                                <tr>
                                    <td style="width: 19%">Reporting Date</td>
                                    <td colspan="3"><?php echo $items_purpose_other['date_reporting'] ? System_helper::display_date($items_purpose_other['date_reporting']) : 'N/A'; ?></td>
                                </tr>
                                <tr>
                                    <td style="width: 19%">Report (Description)</td>
                                    <td colspan="3"><?php echo nl2br($items_purpose_other['report_description']) ? $items_purpose_other['report_description'] : 'N/A'; ?></td>
                                </tr>
                                <tr>
                                    <td style="width: 19%">Recommendation</td>
                                    <td colspan="3"><?php echo nl2br($items_purpose_other['recommendation']) ? $items_purpose_other['recommendation'] : 'N/A'; ?></td>
                                </tr>
                                <?php
                                if (isset($items_purpose_other['others']))
                                {
                                    ?>
                                    <tr>
                                        <td colspan="21" class="text-center"><strong>Other Information</strong></td>
                                    </tr>
                                    <tr>
                                        <th style="height: 10px;">Name</th>
                                        <th style="height: 10px;">Contact No</th>
                                        <th style="height: 10px;">Profession</th>
                                        <th style="height: 10px;">Discussion</th>
                                    </tr>
                                    <?php
                                    foreach ($items_purpose_other['others'] as $other)
                                    { ?>
                                        <tr>
                                            <td style="height: 10px;"><?php echo $other['name'] ?></td>
                                            <td style="height: 10px;"><?php echo $other['contact_no'] ?></td>
                                            <td style="height: 10px;"><?php echo $other['profession'] ?></td>
                                            <td style="height: 10px;"><?php echo $other['discussion'] ?></td>
                                        </tr>
                                    <?php
                                    }
                                }
                                ?>
                                </tbody>
                            </table>
                        <?php
                        }
                    }
                    else
                    {
                        ?>
                        <div class="alert alert-danger text-center"> Tour Purpose Not Setup</div>
                    <?php
                    }
                    ?>
                    <?php if ($item['remarks'] || $item['superior_comment'])
                    {
                        ?>
                        <table class="table table-bordered">
                            <tbody>
                            <tr>
                                <td colspan="21" class="text-center"><strong>Remarks</strong></td>
                            </tr>
                            <tr>
                                <td style="width: 15%"><strong>Applicant </strong></td>
                                <td style="width: 15%"><strong>Supervisor</strong></td>

                            </tr>
                            <tr>
                                <td style="width: 15%">
                                    <?php if ($item['remarks'])
                                    {
                                        echo $item['remarks'];
                                    }
                                    else
                                    {
                                        echo 'N/A';
                                    } ?>
                                </td>
                                <td style="width: 15%">
                                    <?php if ($item['superior_comment'])
                                    {
                                        echo $item['superior_comment'];
                                    }
                                    else
                                    {
                                        echo 'N/A';
                                    } ?>
                                </td>
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
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

<div class="row widget">
<div class="widget-header">
    <div class="title">
        <?php echo $title; ?>
    </div>
    <div class="clearfix"></div>
</div>
<div class="panel panel-default">
    <div class="panel-heading">
        <h4 class="panel-title">
            <label class=""><a class="external text-danger" data-toggle="collapse" data-target="#collapse3" href="#"> + Basic Information</a>
            </label></h4>
    </div>
    <div id="collapse3" class="panel-collapse collapse">
        <table class="table table-bordered table-responsive system_table_details_view">
            <tr>
                <th class="widget-header header_caption"><label class="control-label pull-right">Name</label></th>
                <th colspan="3"><label class="control-label"><?php echo $item['name']; ?></label></th>
            </tr>
            <tr>
                <th class="widget-header header_caption"><label class="control-label pull-right">Designation</label>
                </th>
                <th colspan="3">
                    <label class="control-label"><?php echo ($item['designation']) ? $item['designation'] : 'N/A'; ?></label>
                </th>
            </tr>
            <tr>
                <th class="widget-header header_caption"><label class="control-label pull-right">Department</label></th>
                <th colspan="3">
                    <label class="control-label"><?php echo ($item['department_name']) ? $item['department_name'] : 'N/A'; ?></label>
                </th>
            </tr>
            <tr>
                <th class="widget-header header_caption"><label class="control-label pull-right">Title</label></th>
                <th colspan="3"><label class="control-label"><?php echo $item['title']; ?></label></th>
            </tr>
            <tr>
                <th class="widget-header header_caption">
                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE'); ?></label></th>
                <th colspan="3">
                    <label class="control-label"> From: <?php echo System_helper::display_date($item['date_from']) ?>
                        To: <?php echo System_helper::display_date($item['date_to']) ?>
                    </label></th>
            </tr>
            <?php
            if ($item['user_created'])
            {
                ?>
                <tr>
                    <th class="widget-header header_caption">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CREATED_BY'); ?></label>
                    </th>
                    <th><label class="control-label"><?php echo $item['create_user']; ?></label></th>
                    <th class="widget-header header_caption">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_CREATED_TIME'); ?></label>
                    </th>
                    <th>
                        <label class="control-label"><?php echo System_helper::display_date_time($item['date_created']); ?></label>
                    </th>
                </tr>
            <?php
            }
            if ($item['user_updated'])
            {
                ?>
                <tr>
                    <th class="widget-header header_caption">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_UPDATED_BY'); ?></label>
                    </th>
                    <th><label class="control-label"><?php echo $item['update_user']; ?></label></th>
                    <th class="widget-header header_caption">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_UPDATED_TIME'); ?></label>
                    </th>
                    <th>
                        <label class="control-label"><?php echo System_helper::display_date_time($item['date_updated']); ?></label>
                    </th>
                </tr>
            <?php
            }
            if ($item['user_forwarded'])
            {
                ?>
                <tr>
                    <th class="widget-header header_caption"><label class="control-label pull-right">Forward Status</label></th>
                    <th><label class="control-label"><?php echo $item['status_forward']; ?></label></th>
                    <th class="widget-header header_caption"><label class="control-label pull-right">(Tour Setup) Number of Edit</label></th>
                    <th colspan="3"><label class="control-label"><?php echo($item['revision_count'] - 1); ?></label></th>
                </tr>
                <tr>
                    <th class="widget-header header_caption">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_FORWARDED_BY'); ?></label>
                    </th>
                    <th><label class="control-label"><?php echo $item['forward_user']; ?></label></th>
                    <th class="widget-header header_caption">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_FORWARDED_TIME'); ?></label>
                    </th>
                    <th>
                        <label class="control-label"><?php echo System_helper::display_date_time($item['date_forwarded']); ?></label>
                    </th>
                </tr>
            <?php
            }
            if ($item['user_approved'])
            {
                ?>
                <tr>
                    <th colspan="4" class="bg-info">Approval Information</th>
                </tr>
                <tr>
                    <th class="widget-header header_caption">
                        <label class="control-label pull-right">Approval Status</label></th>
                    <th><label class="control-label"><?php echo $item['status_approve']; ?></label></th>
                    <th class="widget-header header_caption">
                        <label class="control-label pull-right">(Tour Setup) Number of Rollback</label>
                    </th>
                    <th><label class="control-label"><?php echo $item['revision_count_rollback']; ?></label></th>
                </tr>
                <tr>
                    <th class="widget-header header_caption">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_APPROVED_BY'); ?></label>
                    </th>
                    <th><label class="control-label"><?php echo $item['approve_user']; ?></label></th>
                    <th class="widget-header header_caption">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_APPROVED_TIME'); ?></label>
                    </th>
                    <th>
                        <label class="control-label"><?php echo System_helper::display_date_time($item['date_approved']); ?></label>
                    </th>
                </tr>
            <?php } ?>
        </table>
    </div>
</div>
<div class="col-md-12">
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
                    <div class="panel panel-default">
                        <div class="panel-heading" style="background: green; color: #FFFFFF">
                            <strong class="panel-title">
                                <a class="accordion-toggle external" data-toggle="collapse" data-target="#collapse_<?php echo $serial; ?>" href="#"><?php echo $serial; ?>. Purpose: <?php echo $items_purpose_other['purpose']; ?> (+) </a>
                            </strong>
                        </div>
                        <div id="collapse_<?php echo $serial; ?>" class="panel-collapse <?php if ($serial == 1)
                        {
                            echo 'collapse-in';
                        }
                        else
                        {
                            echo 'collapse';
                        } ?>">
                            <div style="overflow-x: auto;" class="row show-grid">
                                <table class="table table-bordered">
                                    <tbody>
                                    <tr>
                                        <td style="width: 15%"><strong>Reporting Date: </strong></td>
                                        <td><?php echo $items_purpose_other['date_reporting'] ? System_helper::display_date($items_purpose_other['date_reporting']) : 'N/A'; ?></td>
                                    </tr>
                                    <tr>
                                        <td style="width: 15%"><strong>Report (Description): </strong></td>
                                        <td><?php echo nl2br($items_purpose_other['report_description']) ? $items_purpose_other['report_description'] : 'N/A'; ?></td>
                                    </tr>
                                    <tr>
                                        <td style="width: 15%"><strong>Recommendation: </strong></td>
                                        <td><?php echo nl2br($items_purpose_other['recommendation']) ? $items_purpose_other['recommendation'] : 'N/A'; ?></td>
                                    </tr>
                                    </tbody>
                                </table>
                                <?php
                                if (isset($items_purpose_other['others']))
                                {
                                    ?>
                                    <table class="table table-bordered">
                                        <tbody>
                                        <tr>
                                            <td colspan="21" class="text-center bg-danger">
                                                <strong>Other Information</strong></td>
                                        </tr>
                                        <tr>
                                            <th>Name</th>
                                            <th>Contact No</th>
                                            <th>Profession</th>
                                            <th>Discussion</th>
                                        </tr>
                                        <?php
                                        foreach ($items_purpose_other['others'] as $other)
                                        {
                                            ?>
                                            <tr>
                                                <td><?php echo $other['name'] ?></td>
                                                <td><?php echo $other['contact_no'] ?></td>
                                                <td><?php echo $other['profession'] ?></td>
                                                <td><?php echo $other['discussion'] ?></td>
                                            </tr>
                                        <?php
                                        }
                                        ?>
                                        </tbody>
                                    </table>
                                <?php
                                }
                                ?>
                            </div>
                        </div>
                    </div>
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
        </div>
    </div>

    <?php if ($item['remarks'])
    {
        ?>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Remarks:</label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <?php echo($item['remarks']); ?>
            </div>
        </div>
    <?php } ?>
    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right">Supervisors Comment:</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <?php if ($item['superior_comment'])
            {
                echo $item['superior_comment'];
            }
            else
            {
                echo 'N/A';
            } ?>
        </div>
    </div>
</div>
</div>

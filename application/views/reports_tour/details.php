<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
?>
<style>
    .purpose-list table tr td:first-child {
        width: 50px
    }
</style>

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
                <th class="widget-header header_caption"><label class="control-label pull-right">Designation</label></th>
                <th><label class="control-label"><?php echo ($item['designation']) ? $item['designation'] : 'N/A'; ?></label></th>
                <th class="widget-header header_caption"><label class="control-label pull-right">Department</label></th>
                <th><label class="control-label"><?php echo ($item['department_name']) ? $item['department_name'] : 'N/A'; ?></label></th>
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
            <tr>
                <th class="widget-header header_caption">
                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_AMOUNT_IOU_REQUEST'); ?></label>
                </th>
                <th colspan="3"><label class="control-label"><?php echo number_format($item['amount_iou_request'],2); ?></label></th>
            </tr>
            <tr>
                <th class="widget-header header_caption">
                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_IOU_DETAILS'); ?></label>
                </th>
                <th colspan="3"><label class="control-label"><?php echo $item['iou_details']; ?></label></th>
            </tr>
            <tr>
                <th class="widget-header header_caption">
                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CREATED_BY'); ?></label>
                </th>
                <th><label class="control-label"><?php echo $users[$item['user_created']]['name']; ?></label></th>
                <th class="widget-header header_caption">
                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_CREATED_TIME'); ?></label>
                </th>
                <th>
                    <label class="control-label"><?php echo System_helper::display_date_time($item['date_created']); ?></label>
                </th>
            </tr>
            <?php
            if ($item['user_updated'])
            {
                ?>
                <tr>
                    <th class="widget-header header_caption">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_UPDATED_BY'); ?></label>
                    </th>
                    <th><label class="control-label"><?php echo $users[$item['user_updated']]['name']; ?></label></th>
                    <th class="widget-header header_caption">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_UPDATED_TIME'); ?></label>
                    </th>
                    <th>
                        <label class="control-label"><?php echo System_helper::display_date_time($item['date_updated']); ?></label>
                    </th>
                </tr>
            <?php
            }
            ?>
            <tr>
                <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_STATUS_FORWARD');?></label></th>
                <th><label class="control-label"><?php echo $item['status_forward']; ?></label></th>
                <th class="widget-header header_caption"><label class="control-label pull-right">(Tour Setup) Number of Edit</label></th>
                <th colspan="3"><label class="control-label"><?php echo ($item['revision_count']-1); ?></label></th>
            </tr>
            <?php
            if ($item['user_forwarded'])
            {
                ?>

                <tr>
                    <th class="widget-header header_caption">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_FORWARDED_BY'); ?></label>
                    </th>
                    <th><label class="control-label"><?php echo $users[$item['user_forwarded']]['name']; ?></label></th>
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
                    <th><label class="control-label"><?php echo $users[$item['user_approved']]['name']; ?></label></th>
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
            if($items)
            {
                $serial=0;
                foreach($items as $purpose)
                {
                    ++$serial;
                    ?>
                    <div class="panel panel-default">
                        <div class="panel-heading" style="background: green; color: #FFFFFF">
                            <strong class="panel-title">
                                <a class="accordion-toggle external" data-toggle="collapse" data-target="#collapse_<?php echo $serial; ?>" href="#"><?php echo $serial; ?>. Purpose: <?php echo $purpose['purpose']; ?> (+) </a>
                            </strong>
                        </div>
                        <div id="collapse_<?php echo $serial; ?>" class="panel-collapse <?php echo ($serial == 1)? 'collapse-in':'collapse'; ?>">
                            <div style="overflow-x: auto;" class="row show-grid">
                                <table class="table table-bordered">
                                    <tbody>
                                    <tr>
                                        <td style="width: 15%"><strong>Reporting Date: </strong></td>
                                        <td><?php echo $purpose['date_reporting'] ? System_helper::display_date($purpose['date_reporting']) : 'N/A'; ?></td>
                                    </tr>
                                    <tr>
                                        <td style="width: 15%"><strong>Report (Description): </strong></td>
                                        <td><?php echo nl2br($purpose['report_description']) ? $purpose['report_description'] : 'N/A'; ?></td>
                                    </tr>
                                    <tr>
                                        <td style="width: 15%"><strong>Recommendation: </strong></td>
                                        <td><?php echo nl2br($purpose['recommendation']) ? $purpose['recommendation'] : 'N/A'; ?></td>
                                    </tr>
                                    </tbody>
                                </table>
                                <?php
                                if (isset($purpose['others']))
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
                                        foreach ($purpose['others'] as $key => $other)
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

            }
            ?>
        </div>
    </div>
    <?php
    if ($item['remarks'])
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
    <?php
    }
    ?>
    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right">Supervisors Comment:</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <?php echo $item['superior_comment']?$item['superior_comment']:'N/A'; ?>
        </div>
    </div>
</div>
</div>

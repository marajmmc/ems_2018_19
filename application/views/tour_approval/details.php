<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();
$action_buttons=array();
$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_BACK"),
    'href'=>site_url($CI->controller_url.'/index/list/'));

$action_buttons[]=array(
    'type'=>'button',
    'label'=>$CI->lang->line("ACTION_CLEAR"),
    'id'=>'button_action_clear',
    'data-form'=>'#save_form'
);
$CI->load->view('action_buttons',array('action_buttons'=>$action_buttons));
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

    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right">Name:</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label"><?php echo $item['name']?></label>
        </div>
    </div>

    <?php if($item['designation']){?>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Designation:</label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $item['designation']?></label>
            </div>
        </div>
    <?php } ?>

    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right">Department:</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label"><?php if($item['department_name']){echo $item['department_name'];}else{echo 'N/A';}?></label>
        </div>
    </div>

    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right">Title:</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label"><?php echo $item['title']?></label>
        </div>
    </div>

    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE');?>:</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label">From: <?php echo System_helper::display_date($item['date_from'])?> To: <?php echo System_helper::display_date($item['date_to'])?></label>
        </div>
    </div>



    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right">Purpose(s):</label>
        </div>
        <div class="col-sm-4 col-xs-8 purpose-list">
            <table class="table table-bordered table-striped table-hover">
                <thead>
                <tr>
                    <th>Slno.</th>
                    <th>Purpose</th>
                </tr>
                </thead>
                <tbody>
                <?php
                if ($items)
                {
                    $serial = 0;
                    foreach ($items as $row)
                    {
                        ++$serial;
                        ?>
                        <tr>
                            <td><?php echo $serial . '.'; ?></td>
                            <td><?php echo $row['purpose']; ?></td>
                        </tr>
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
                </tbody>
            </table>

        </div>
    </div>

    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_IOU_AMOUNT'); ?>:</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label
                class="control-label"><?php echo ($item['amount_iou']) ? number_format($item['amount_iou']) : "N/A"; ?></label>
        </div>
    </div>

    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_IOU_DETAILS'); ?>:</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label
                class="control-label"><?php echo ($item['iou_details']) ? nl2br($item['iou_details']) : "N/A"; ?></label>
        </div>
    </div>


    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right">Remarks:</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label"><?php if($item['remarks']){echo nl2br($item['remarks']);}else{echo 'N/A';}?></label>
        </div>
    </div>

    <?php /* <div class="row show-grid">
        <div class="col-xs-12">
            <?php
            if($items_purpose_others)
            {
                $serial=0;
                foreach($items_purpose_others as $items_purpose_other)
                {
                    ++$serial;
                    ?>
                    <div class="panel panel-default">
                        <div class="panel-heading" style="background: green; color: #FFFFFF">
                            <strong class="panel-title">
                                <a class="accordion-toggle external" data-toggle="collapse"  data-target="#collapse_<?php echo $serial; ?>" href="#"><?php echo $serial;?>. Purpose: <?php echo $items_purpose_other['purpose'];?> (+) </a>
                            </strong>
                        </div>
                        <div id="collapse_<?php echo $serial; ?>" class="panel-collapse <?php if($serial==1){echo 'collapse-in';}else{echo 'collapse';}?>">
                            <div style="overflow-x: auto;" class="row show-grid">
                                <table class="table table-bordered">
                                    <tbody>
                                    <tr>
                                        <td style="width: 15%"><strong>Reporting Date: </strong></td>
                                        <td><?php echo $items_purpose_other['date_reporting']?System_helper::display_date($items_purpose_other['date_reporting']):'N/A';?></td>
                                    </tr>
                                    <tr>
                                        <td style="width: 15%"><strong>Report (Description): </strong></td>
                                        <td><?php echo nl2br($items_purpose_other['report_description'])?$items_purpose_other['report_description']:'N/A';?></td>
                                    </tr>
                                    <tr>
                                        <td style="width: 15%"><strong>Recommendation: </strong></td>
                                        <td><?php echo nl2br($items_purpose_other['recommendation'])?$items_purpose_other['recommendation']:'N/A';?></td>
                                    </tr>
                                    </tbody>
                                </table>
                                <?php
                                if($items_purpose_other['purpose_others_id'])
                                {
                                    ?>
                                    <table class="table table-bordered">
                                        <tbody>
                                        <tr>
                                            <td colspan="21" class="text-center bg-danger"><strong>Other Information</strong></td>
                                        </tr>
                                        <tr>
                                            <td>Name</td>
                                            <td>Contact No</td>
                                            <td>Profession</td>
                                            <td>Discussion</td>
                                        </tr>
                                        <?php
                                        foreach($items_purpose_other['others'] as $other)
                                        {
                                            ?>
                                            <tr>
                                                <td><?php echo $other['name']?></td>
                                                <td><?php echo $other['contact_no']?></td>
                                                <td><?php echo $other['profession']?></td>
                                                <td><?php echo $other['discussion']?></td>
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
    </div>  */ ?>

    <?php if($item['superior_comment']){ ?>
    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right">Supervisors Comment:</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <?php if($item['superior_comment']){echo $item['superior_comment'];}else{echo 'N/A';} ?>
        </div>
    </div>
    <?php } ?>
</div>
<div class="clearfix"></div>

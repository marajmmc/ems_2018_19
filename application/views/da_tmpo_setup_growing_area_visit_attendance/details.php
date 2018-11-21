<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_BACK").' to All list',
    'href'=>site_url($CI->controller_url.'/index/list_all')
);
$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_BACK").' to Pending List',
    'href'=>site_url($CI->controller_url)
);
$CI->load->view('action_buttons',array('action_buttons'=>$action_buttons));
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
                <label class=""><a class="external text-danger" data-toggle="collapse" data-target="#collapse3" href="#">+ Basic Information</a></label>
            </h4>
        </div>
        <div id="collapse3" class="panel-collapse collapse">
            <table class="table table-bordered table-responsive system_table_details_view">
                <thead>
                <tr>
                    <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_VISIT');?></label></th>
                    <th class=""><label class="control-label"><?php echo System_helper::display_date($item_head['date_visit']);?></label></th>
                    <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DIVISION_NAME');?></label></th>
                    <th class=" header_value"><label class="control-label"><?php echo $item_head['division_name'];?></label></th>
                </tr>
                <tr>
                    <th colspan="2">&nbsp;</th>
                    <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ZONE_NAME');?></label></th>
                    <th class=" header_value"><label class="control-label"><?php echo $item_head['zone_name'];?></label></th>
                </tr>
                <tr>
                    <th colspan="2">&nbsp;</th>
                    <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_TERRITORY_NAME');?></label></th>
                    <th class=" header_value"><label class="control-label"><?php echo $item_head['territory_name'];?></label></th>
                </tr>
                <tr>
                    <th colspan="2">&nbsp;</th>
                    <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DISTRICT_NAME');?></label></th>
                    <th class=" header_value"><label class="control-label"><?php echo $item_head['district_name'];?></label></th>
                </tr>
                <tr>
                    <th colspan="2">&nbsp;</th>
                    <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_OUTLET_NAME');?></label></th>
                    <th class=" header_value"><label class="control-label"><?php echo $item_head['outlet_name'];?></label></th>
                </tr>
                <tr>
                    <th colspan="2">&nbsp;</th>
                    <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_AREA_NAME');?></label></th>
                    <th class=" header_value"><label class="control-label"><?php echo $item_head['area_name'];?></label></th>
                </tr>
                <tr>
                    <th colspan="2">&nbsp;</th>
                    <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_AREA_ADDRESS');?></label></th>
                    <th class=" header_value"><label class="control-label"><?php echo $item_head['area_address'];?></label></th>
                </tr>
                <?php
                if($item_head['other_info'])
                {
                    ?>
                    <tr>
                        <th class="widget-header header_caption" style="vertical-align: top"><label class="control-label pull-right">Others Activities</label></th>
                        <th class=" header_value" colspan="3"><label class="control-label"><?php echo nl2br($item_head['other_info']);?></label></th>
                    </tr>
                <?php
                }
                ?>
                <?php
                if($item_head['remarks'])
                {
                    ?>
                    <tr>
                        <th class="widget-header header_caption" style="vertical-align: top"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_REMARKS');?></label></th>
                        <th class=" header_value" colspan="3"><label class="control-label"><?php echo nl2br($item_head['remarks']);?></label></th>
                    </tr>
                <?php
                }
                ?>
                <tr>
                    <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CREATED_BY');?></label></th>
                    <th class=" header_value"><label class="control-label"><?php echo $users[$item_head['user_created']]['name'];?></label></th>
                    <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_CREATED_TIME');?></label></th>
                    <th class=""><label class="control-label"><?php echo System_helper::display_date_time($item_head['date_created']);?></label></th>
                </tr>
                <?php
                if($item_head['user_updated'])
                {
                    ?>
                    <tr>
                        <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_UPDATED_BY');?></label></th>
                        <th class=" header_value"><label class="control-label"><?php echo $users[$item_head['user_updated']]['name'];?></label></th>
                        <th class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_UPDATED_TIME');?></label></th>
                        <th class=""><label class="control-label"><?php echo System_helper::display_date_time($item_head['date_updated']);?></label></th>
                    </tr>
                <?php
                }
                ?>
                <tr>
                    <th class="widget-header header_caption" style="vertical-align: top"><label class="control-label pull-right">Attendance Status</label></th>
                    <th class=" header_value" colspan="3"><label class="control-label"><?php echo nl2br($item_head['status_attendance']);?></label></th>
                </tr>
                <?php
                if($item_head['remarks_attendance'])
                {
                    ?>
                    <tr>
                        <th class="widget-header header_caption" style="vertical-align: top"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_STATUS_ATTENDANCE');?></label></th>
                        <th class=" header_value" colspan="3"><label class="control-label"><?php echo nl2br($item_head['remarks_attendance']);?></label></th>
                    </tr>
                <?php
                }
                ?>
                <?php
                if($item_head['user_attendance'])
                {
                    ?>
                    <tr>
                        <th class="widget-header header_caption"><label class="control-label pull-right">Attendance By</label></th>
                        <th class=" header_value"><label class="control-label"><?php echo $users[$item_head['user_attendance']]['name'];?></label></th>
                        <th class="widget-header header_caption"><label class="control-label pull-right">Attendance Time</label></th>
                        <th class=""><label class="control-label"><?php echo System_helper::display_date_time($item_head['date_attendance']);?></label></th>
                    </tr>
                <?php
                }
                ?>
                </thead>
            </table>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <label class=""><a class="external text-danger" data-toggle="collapse" data-target="#collapse_crop_type" href="#">+ Crop & Type Information</a></label>
            </h4>
        </div>
        <div id="collapse_crop_type" class="panel-collapse collapse in">
            <div class="row show-grid">
                <?php
                if(!$varieties)
                {
                    ?>
                    <div class="col-xs-12">
                        <div class="checkbox alert alert-danger text-center">
                            <strong>There is no variety setup.</strong>
                        </div>
                    </div>
                <?php
                }
                else
                {
                    $serial=0;
                    foreach($varieties as $variety)
                    {
                        ++$serial;
                        $crop_type_name=$serial.'. '.$variety['crop_name'].' ('.$variety['crop_type_name'].')';
                        if(!$variety['crop_type_id'])
                        {
                            $crop_type_name=$serial.'. '.$variety['crop_name'].' (All)';
                        }
                        ?>
                        <div class="col-xs-2">
                            <div class="checkbox">
                                <label title="<?php echo $crop_type_name;?>"><strong><?php echo $crop_type_name;?></strong></label>
                            </div>
                        </div>
                    <?php
                    }
                }
                ?>
            </div>
        </div>
    </div>
    <?php
    foreach($previous_visits as $previous_visit)
    {
        ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <label class=""><a class="external text-danger" data-toggle="collapse" data-target="#collapse_previous_visit_<?php echo $previous_visit['id']?>" href="#">+ <?php echo System_helper::display_date($previous_visit['date_visit'])?> </a></label>
                </h4>
            </div>
            <div id="collapse_previous_visit_<?php echo $previous_visit['id']?>" class="panel-collapse collapse ">
                <div class="row show-grid">
                    <br/>
                    <table class="table table-responsive table-bordered">
                        <thead>
                        <tr>
                            <th class="text-center bg-success" colspan="4">
                                Lead Farmer Information
                            </th>
                        </tr>
                        <tr>
                            <th>Lead Farmer Name</th>
                            <th>Description</th>
                            <th>Image</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        if(!$farmers)
                        {
                            ?>
                            <tr>
                                <td colspan="21" class="text-center bg-danger text-danger"><strong>There is no lead farmer setup.</strong></td>
                            </tr>
                        <?php
                        }
                        else
                        {
                            foreach($farmers as $farmer)
                            {
                                if(isset($previous_farmers[$previous_visit['id']][$farmer['farmer_id']]['image_location']) && $previous_farmers[$previous_visit['id']][$farmer['farmer_id']]['image_location'])
                                {
                                    $farmer_img=$CI->config->item('system_base_url_picture').$previous_farmers[$previous_visit['id']][$farmer['farmer_id']]['image_location'];
                                }
                                else
                                {
                                    $farmer_img=$CI->config->item('system_base_url_picture').'images/no_image.jpg';
                                }
                                ?>
                                <tr>
                                    <td style="width: 200px;"><?php echo $farmer['lead_farmers_name']?></td>
                                    <td style="width: 800px;">
                                        <?php
                                        echo isset($previous_farmers[$previous_visit['id']][$farmer['farmer_id']]['description'])?nl2br($previous_farmers[$previous_visit['id']][$farmer['farmer_id']]['description']):'--';
                                        ?>
                                    </td>
                                    <td>
                                        <a href="<?php echo $farmer_img; ?>" class="external" target="_blank">
                                            <img style="max-width: 250px;" src="<?php echo $farmer_img; ?>" alt="Lead Farmer Visit Picture">
                                        </a>
                                    </td>
                                </tr>
                            <?php
                            }
                        }
                        ?>
                        </tbody>
                        <thead>
                        <tr>
                            <th colspan="4">&nbsp;</th>
                        </tr>
                        <tr>
                            <th class="text-center bg-success" colspan="4">
                                Dealer Information
                            </th>
                        </tr>
                        <tr>
                            <th>Dealer Name</th>
                            <th>Description</th>
                            <th>Image</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        if(!$dealers)
                        {
                            ?>
                            <tr>
                                <td colspan="21" class="text-center bg-danger text-danger"><strong>There is no dealer setup.</strong></td>
                            </tr>
                        <?php
                        }
                        else
                        {
                            foreach($dealers as $dealer)
                            {
                                if(isset($previous_dealers[$previous_visit['id']][$dealer['dealer_id']]['image_location']) && $previous_dealers[$previous_visit['id']][$dealer['dealer_id']]['image_location'])
                                {
                                    $dealer_img=$CI->config->item('system_base_url_picture').$previous_dealers[$previous_visit['id']][$dealer['dealer_id']]['image_location'];
                                }
                                else
                                {
                                    $dealer_img=$CI->config->item('system_base_url_picture').'images/no_image.jpg';
                                }
                                ?>
                                <tr>
                                    <td><?php echo $dealer['dealer_name']?></td>
                                    <td>
                                        <?php
                                        echo isset($previous_dealers[$previous_visit['id']][$dealer['dealer_id']]['description'])?nl2br($previous_dealers[$previous_visit['id']][$dealer['dealer_id']]['description']):'--';
                                        ?>
                                    </td>
                                    <td>
                                        <a href="<?php echo $dealer_img; ?>" class="external" target="_blank">
                                            <img style="max-width: 250px;" src="<?php echo $dealer_img; ?>" alt="Dealer Visit Picture">
                                        </a>
                                    </td>
                                </tr>
                            <?php
                            }
                        }
                        ?>
                        <tr><td colspan="21">&nbsp;</td></tr>
                        <?php
                        if($previous_visit['other_info'])
                        {
                            ?>
                            <tr>
                                <td><strong>Others Activities</strong></td>
                                <td colspan="21"><?php echo $previous_visit['other_info'] ?></td>
                            </tr>
                        <?php
                        }
                        ?>
                        <?php
                        if($previous_visit['remarks'])
                        {
                            ?>
                            <tr>
                                <td><strong>Remarks</strong></td>
                                <td colspan="21"><?php echo $previous_visit['remarks'] ?></td>
                            </tr>
                        <?php
                        }
                        ?>
                        <?php
                        if($previous_visit['remarks_attendance'])
                        {
                            ?>
                            <tr>
                                <td><strong>Remarks for attendance</strong></td>
                                <td colspan="21"><?php echo $previous_visit['remarks_attendance'] ?></td>
                            </tr>
                        <?php
                        }
                        ?>
                        <tr>
                            <td><strong>Attendance</strong></td>
                            <td colspan="21"><?php echo $previous_visit['status_attendance'] ?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php
    }
    ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <label class=""><a class="external text-danger" data-toggle="collapse" data-target="#collapse_previous_visit_<?php echo $item_head['area_id']?>" href="#">+ <?php echo System_helper::display_date($item_head['date_visit'])?> (Current Visit)</a></label>
            </h4>
        </div>
        <div id="collapse_previous_visit_<?php echo $item_head['area_id']?>" class="panel-collapse collapse in">
            <div class="row show-grid">
                <div class="col-xs-12">
                    <br/>
                    <table class="table table-responsive table-bordered">
                        <thead>
                        <tr>
                            <th class="text-center bg-success" colspan="4">
                                Lead Farmer Information
                            </th>
                        </tr>
                        <tr>
                            <th>Lead Farmer Name</th>
                            <th>Description</th>
                            <th>Attachment</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        if(!$farmers)
                        {
                            ?>
                            <tr>
                                <td colspan="21" class="text-center bg-danger text-danger"><strong>There is no lead farmer setup.</strong></td>
                            </tr>
                        <?php
                        }
                        else
                        {
                            foreach($farmers as $farmer)
                            {
                                if($farmer['image_location'])
                                {
                                    $farmer_img=$CI->config->item('system_base_url_picture').$farmer['image_location'];
                                }
                                else
                                {
                                    $farmer_img=$CI->config->item('system_base_url_picture').'images/no_image.jpg';
                                }
                                ?>
                                <tr>
                                    <td><?php echo $farmer['lead_farmers_name']?></td>
                                    <td>
                                        <?php echo nl2br($farmer['description']) ?>
                                    </td>
                                    <td>
                                        <a href="<?php echo $farmer_img; ?>" class="external" target="_blank">
                                            <img style="max-width: 250px;" src="<?php echo $farmer_img; ?>" alt="<?php echo $farmer['image_name']; ?>">
                                        </a>
                                    </td>
                                </tr>
                            <?php
                            }
                        }
                        ?>
                        </tbody>
                        <thead>
                        <tr>
                            <th colspan="4">&nbsp;</th>
                        </tr>
                        <tr>
                            <th class="text-center bg-success" colspan="4">
                                Dealer Information
                            </th>
                        </tr>
                        <tr>
                            <th>Dealer Name</th>
                            <th>Description</th>
                            <th>Attachment</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        if(!$dealers)
                        {
                            ?>
                            <tr>
                                <td colspan="21" class="text-center bg-danger text-danger"><strong>There is no dealer setup.</strong></td>
                            </tr>
                        <?php
                        }
                        else
                        {
                            foreach($dealers as $dealer)
                            {
                                if($dealer['image_location'])
                                {
                                    $dealer_img=$CI->config->item('system_base_url_picture').$dealer['image_location'];
                                }
                                else
                                {
                                    $dealer_img=$CI->config->item('system_base_url_picture').'images/no_image.jpg';
                                }
                                ?>
                                <tr>
                                    <td><?php echo $dealer['dealer_name']?></td>
                                    <td>
                                        <?php echo nl2br($dealer['description']) ?>
                                    </td>
                                    <td>
                                        <a href="<?php echo $dealer_img; ?>" class="external" target="_blank">
                                            <img style="max-width: 250px;" src="<?php echo $dealer_img; ?>" alt="<?php echo $dealer['image_name']; ?>">
                                        </a>
                                    </td>
                                </tr>
                            <?php
                            }
                        }
                        ?>
                        <tr><td colspan="21">&nbsp;</td></tr>
                        <?php
                        if($item_head['other_info'])
                        {
                            ?>
                            <tr>
                                <td><strong>Others Activities</strong></td>
                                <td colspan="21"><?php echo $item_head['other_info'] ?></td>
                            </tr>
                        <?php
                        }
                        ?>
                        <?php
                        if($item_head['remarks'])
                        {
                            ?>
                            <tr>
                                <td><strong>Remarks</strong></td>
                                <td colspan="21"><?php echo $item_head['remarks'] ?></td>
                            </tr>
                        <?php
                        }
                        ?>
                        <?php
                        if($item_head['remarks_attendance'])
                        {
                            ?>
                            <tr>
                                <td><strong>Remarks for attendance</strong></td>
                                <td colspan="21"><?php echo $item_head['remarks_attendance'] ?></td>
                            </tr>
                        <?php
                        }
                        ?>
                        <tr>
                            <td><strong>Attendance</strong></td>
                            <td colspan="21"><?php echo $item_head['status_attendance'] ?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-xs-12">
                    <table class="table table-responsive table-bordered">

                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="clearfix"></div>
<script type="text/javascript">
    jQuery(document).ready(function()
    {
        system_preset({controller:'<?php echo $CI->router->class; ?>'});
    });
</script>

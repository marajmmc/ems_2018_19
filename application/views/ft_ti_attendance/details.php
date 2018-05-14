<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
$action_buttons[]=array
(
    'label'=>$CI->lang->line("ACTION_BACK"),
    'href'=>site_url($CI->controller_url)
);
$CI->load->view('action_buttons',array('action_buttons'=>$action_buttons));
?>

<style>
    .datepicker
    {
        cursor: pointer !important;
    }
</style>
<div class="row widget">

    <div class="widget-header">
        <div class="title">
            <?php echo $title; ?>
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="col-md-12">
        <table class="table table-bordered table-responsive system_table_details_view">
            <tbody>
            <tr>
                <td class="widget-header header_caption"><label class="control-label pull-right">Task Entry By</label></td>
                <td class="header_value"><label class="control-label"><?php echo $item['created_by'];?></label></td>
                <td class="widget-header header_caption"><label class="control-label pull-right">Task Entry Time</label></td>
                <td class="header_value"><label class="control-label"><?php echo System_helper::display_date_time($item['date_created']);?></label></td>
            </tr>
            <?php if($item['date_updated']){?>
                <tr>
                    <td class="widget-header header_caption"><label class="control-label pull-right">Task Updated By</label></td>
                    <td class="header_value"><label class="control-label"><?php echo $item['updated_by'];?></label></td>
                    <td class="widget-header header_caption"><label class="control-label pull-right">Task Update Time</label></td>
                    <td class="header_value"><label class="control-label"><?php echo System_helper::display_date_time($item['date_updated']);?></label></td>
                </tr>
            <?php } ?>

            <?php if($item['date_created_attendance']){?>
                <tr>
                    <td class="widget-header header_caption"><label class="control-label pull-right">Attendance Taken By</label></td>
                    <td class="header_value"><label class="control-label"><?php echo $item['attendance_taken_by'];?></label></td>
                    <td class="widget-header header_caption"><label class="control-label pull-right">Attendance Taken Time</label></td>
                    <td class="header_value"><label class="control-label"><?php echo System_helper::display_date_time($item['date_created_attendance']);?></label></td>
                </tr>
            <?php } ?>

            <?php if($item['date_updated_attendance']){?>
                <tr>
                    <td class="widget-header header_caption"><label class="control-label pull-right">Attendance Updated By</label></td>
                    <td class="header_value"><label class="control-label"><?php echo $item['attendance_updated_by'];?></label></td>
                    <td class="widget-header header_caption"><label class="control-label pull-right">Attendance Taken Time</label></td>
                    <td class="header_value"><label class="control-label"><?php echo System_helper::display_date_time($item['date_updated_attendance']);?></label></td>
                </tr>
            <?php } ?>

            <tr>
                <td class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE');?></label></td>
                <td class="header_value"><label class="control-label"><?php echo System_helper::display_date($item['date']);?></label></td>
                <td colspan="2">&nbsp;</td>
            </tr>

            <tr>
                <td class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DIVISION_NAME');?></label></td>
                <td class="header_value"><label class="control-label"><?php echo $item['division_name'];?></label></td>
                <td colspan="2">&nbsp;</td>
            </tr>

            <tr>
                <td class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ZONE_NAME');?></label></td>
                <td class="header_value"><label class="control-label"><?php echo $item['zone_name'];?></label></td>
                <td colspan="2">&nbsp;</td>
            </tr>

            <tr>
                <td class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_TERRITORY_NAME');?></label></td>
                <td class="header_value"><label class="control-label"><?php echo $item['territory_name'];?></label></td>
                <td colspan="2">&nbsp;</td>
            </tr>

            <tr>
                <td class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DISTRICT_NAME');?></label></td>
                <td class="header_value"><label class="control-label"><?php echo $item['district_name'];?></label></td>
                <td colspan="2">&nbsp;</td>
            </tr>

            <tr>
                <td class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_OUTLET_NAME');?></label></td>
                <td class="header_value"><label class="control-label"><?php echo $item['customer_name'];?></label></td>
                <td colspan="2">&nbsp;</td>
            </tr>

            <tr>
                <td class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DEALER');?></label></td>
                <td class="header_value"><label class="control-label"><?php echo $item['farmer_name'];?></label></td>
                <td colspan="2">&nbsp;</td>
            </tr>

            <tr>
                <td class="widget-header header_caption"><label class="control-label pull-right">Dealer Info File</label></td>
                <td class="header_value">
                    <label class="control-label">
                        <?php if(sizeof($dealer_info_file)>0){?>
                           <?php foreach($dealer_info_file as $key=>$file){$key++;?>
                               <a href="<?php echo $CI->config->item('system_base_url_dealer_and_farmer_visit').$file['image_location']; ?>" class="external btn btn-danger" target="_blank"><?php echo 'File '.$key;?></a>
                           <?php } ?>

                        <?php } ?>
                    </label>
                </td>
                <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
                <td class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_LEAD_FARMER_VISIT_ACTIVITIES_ONE');?></label></td>
                <td colspan="3" class=" header_value"><label class="control-label"><?php if($item['lead_farmer_visit_activities_one']){echo nl2br($item['lead_farmer_visit_activities_one']);}else{echo 'N/A';}?></label></td>
            </tr>

            <tr>
                <td class="widget-header header_caption"><label class="control-label pull-right">Attachment(Document)</label></td>
                <td colspan="3" class=" header_value"><img style="max-width: 250px;" src="<?php echo $CI->config->item('system_base_url_dealer_and_farmer_visit').$item['image_location_lead_farmer_visit_one']; ?>" alt="<?php echo $item['image_name_lead_farmer_visit_one']; ?>"></td>
            </tr>

            <tr>
                <td class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_LEAD_FARMER_VISIT_ACTIVITIES_TWO');?></label></td>
                <td colspan="3" class=" header_value"><label class="control-label"><?php if($item['lead_farmer_visit_activities_two']){echo nl2br($item['lead_farmer_visit_activities_two']);}else{echo 'N/A';}?></label></td>
            </tr>

            <tr>
                <td class="widget-header header_caption"><label class="control-label pull-right">Attachment(Document)</label></td>
                <td colspan="3" class=" header_value"><img style="max-width: 250px;" src="<?php echo $CI->config->item('system_base_url_dealer_and_farmer_visit').$item['image_location_lead_farmer_visit_two']; ?>" alt="<?php echo $item['image_name_lead_farmer_visit_two']; ?>"></td>
            </tr>

            <tr>
                <td class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_LEAD_FARMER_VISIT_ACTIVITIES_THREE');?></label></td>
                <td colspan="3" class=" header_value"><label class="control-label"><?php if($item['lead_farmer_visit_activities_three']){echo nl2br($item['lead_farmer_visit_activities_three']);}else{echo 'N/A';}?></label></td>
            </tr>

            <tr>
                <td class="widget-header header_caption"><label class="control-label pull-right">Attachment(Document)</label></td>
                <td colspan="3" class=" header_value"><img style="max-width: 250px;" src="<?php echo $CI->config->item('system_base_url_dealer_and_farmer_visit').$item['image_location_lead_farmer_visit_three']; ?>" alt="<?php echo $item['image_name_lead_farmer_visit_three']; ?>"></td>
            </tr>

            <tr>
                <td class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_FARMER_VISIT_ACTIVITIES');?></label></td>
                <td colspan="3" class=" header_value"><label class="control-label"><?php if($item['farmer_visit_activities']){echo nl2br($item['farmer_visit_activities']);}else{echo 'N/A';}?></label></td>
            </tr>

            <tr>
                <td class="widget-header header_caption"><label class="control-label pull-right">Attachment(Document)</label></td>
                <td colspan="3" class=" header_value"><img style="max-width: 250px;" src="<?php echo $CI->config->item('system_base_url_dealer_and_farmer_visit').$item['image_location_farmer_visit']; ?>" alt="<?php echo $item['image_name_farmer_visit']; ?>"></td>
            </tr>

            <tr>
                <td class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DEALER_VISIT_ACTIVITIES');?></label></td>
                <td colspan="3" class=" header_value"><label class="control-label"><?php if($item['dealer_visit_activities']){echo nl2br($item['dealer_visit_activities']);}else{echo 'N/A';}?></label></td>
            </tr>

            <tr>
                <td class="widget-header header_caption"><label class="control-label pull-right">Attachment(Document)</label></td>
                <td colspan="3" class=" header_value"><img style="max-width: 250px;" src="<?php echo $CI->config->item('system_base_url_dealer_and_farmer_visit').$item['image_location_dealer_visit']; ?>" alt="<?php echo $item['image_name_dealer_visit']; ?>"></td>
            </tr>

            <?php if($item['other_activities']){?>
                <tr>
                    <td class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_OTHER_ACTIVITIES');?></label></td>
                    <td colspan="3" class=" header_value"><label class="control-label"><?php echo nl2br($item['other_activities']);?></label></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>
<div class="clearfix"></div>


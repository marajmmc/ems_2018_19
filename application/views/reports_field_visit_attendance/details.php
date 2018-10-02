<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();
?>
<div class="row widget">
    <div class="widget-header">
        <div class="title">
            <?php echo $title; ?>
        </div>
        <div class="clearfix"></div>
    </div>

    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right">Task Entry By:</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label"><?php echo $users[$item['user_created']]['name']; ?></label>
        </div>

    </div>

    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right">Task Entry Time:</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label"><?php echo System_helper::display_date_time($item['date_created']);?></label>
        </div>

    </div>

    <?php if($item['date_updated']){?>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Task Updated By:</label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $users[$item['user_updated']]['name']; ?></label>
            </div>

        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Task Update Time:</label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo System_helper::display_date_time($item['date_updated']);?></label>
            </div>

        </div>
    <?php } ?>

    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE');?>:</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label"><?php echo System_helper::display_date($item['date']);?></label>
        </div>

    </div>

    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DIVISION_NAME');?>:</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label"><?php echo $item['division_name'];?></label>
        </div>
    </div>

    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ZONE_NAME');?>:</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label"><?php echo $item['zone_name'];?></label>
        </div>
    </div>

    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_TERRITORY_NAME');?>:</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label"><?php echo $item['territory_name'];?></label>
        </div>
    </div>

    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DISTRICT_NAME');?>:</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label"><?php echo $item['district_name'];?></label>
        </div>
    </div>

    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_OUTLET');?>:</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label"><?php echo $item['outlet'];?></label>
        </div>
    </div>

    <div class="row show-grid" id="farmer_id_container">
        <div class="col-xs-4">
            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DEALER');?>:</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label"><?php echo $item['dealer'];?></label>
        </div>
    </div>

    <?php if($item['date_created_attendance']){?>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Attendance Taken By:</label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $users[$item['user_created_attendance']]['name']; ?></label>
            </div>

        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Attendance Taken Time:</label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo System_helper::display_date_time($item['date_created_attendance']);?></label>
            </div>

        </div>
    <?php } ?>

    <?php if($item['date_updated_attendance']){?>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Attendance Updated By:</label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $users[$item['user_updated_attendance']]['name']; ?></label>
            </div>

        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Attendance Updated Time:</label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo System_helper::display_date_time($item['date_updated_attendance']);?></label>
            </div>

        </div>
    <?php } ?>

    <div class="row">
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_LEAD_FARMER_VISIT_ACTIVITIES_ONE');?></label>
            </div>
            <div class="col-xs-4">
                <label class="control-label"><?php echo $item['lead_farmer_visit_activities_one'];?></label>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
            </div>
            <div class="col-xs-4" id="image_lead_farmer_activities_one">
                <img style="max-width: 250px;" src="<?php echo $CI->config->item('system_base_url_picture').$item['image_location_lead_farmer_visit_one']; ?>" alt="<?php echo $item['image_name_lead_farmer_visit_one']; ?>">
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_LEAD_FARMER_VISIT_ACTIVITIES_TWO');?></label>
            </div>
            <div class="col-xs-4">
                <label class="control-label"><?php echo $item['lead_farmer_visit_activities_two'];?></label>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
            </div>
            <div class="col-xs-4" id="image_lead_farmer_activities_two">
                <img style="max-width: 250px;" src="<?php echo $CI->config->item('system_base_url_picture').$item['image_location_lead_farmer_visit_two']; ?>" alt="<?php echo $item['image_name_lead_farmer_visit_two']; ?>">
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_LEAD_FARMER_VISIT_ACTIVITIES_THREE');?></label>
            </div>
            <div class="col-xs-4">
                <label class="control-label"><?php echo $item['lead_farmer_visit_activities_three'];?></label>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
            </div>
            <div class="col-xs-4" id="image_lead_farmer_activities_three">
                <img style="max-width: 250px;" src="<?php echo $CI->config->item('system_base_url_picture').$item['image_location_lead_farmer_visit_three']; ?>" alt="<?php echo $item['image_name_lead_farmer_visit_three']; ?>">
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_FARMER_VISIT_ACTIVITIES');?></label>
            </div>
            <div class="col-xs-4">
                <label class="control-label"><?php echo $item['farmer_visit_activities'];?></label>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
            </div>
            <div class="col-xs-4" id="image_farmer_activities">
                <img style="max-width: 250px;" src="<?php echo $CI->config->item('system_base_url_picture').$item['image_location_farmer_visit']; ?>" alt="<?php echo $item['image_name_farmer_visit']; ?>">
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DEALER_VISIT_ACTIVITIES');?></label>
            </div>
            <div class="col-xs-4">
                <label class="control-label"><?php echo $item['dealer_visit_activities'];?></label>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
            </div>
            <div class="col-xs-4" id="image_dealer_activities">
                <img style="max-width: 250px;" src="<?php echo $CI->config->item('system_base_url_picture').$item['image_location_dealer_visit']; ?>" alt="<?php echo $item['image_name_dealer_visit']; ?>">
            </div>
        </div>

        <?php if($item['other_activities']){?>
            <div class="row show-grid">
                <div class="col-xs-4">
                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_OTHER_ACTIVITIES');?></label>
                </div>
                <div class="col-xs-4">
                    <label class="control-label"><?php echo $item['other_activities'];?></label>
                </div>
            </div>
        <?php } ?>

        <?php if($item['zsc_comment']){?>
            <div class="row show-grid">
                <div class="col-xs-4">
                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ZSC_COMMENT');?></label>
                </div>
                <div class="col-xs-4">
                    <label class="control-label"><?php echo nl2br($item['zsc_comment']);?></label></label>
                </div>
            </div>
        <?php } ?>

        <?php if($item['status_attendance']!=$CI->config->item('system_status_pending')){?>
            <div class="row show-grid">
                <div class="col-xs-4">
                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_STATUS_ATTENDANCE');?></label>
                </div>
                <div class="col-xs-4">
                    <label class="control-label"><?php echo $item['status_attendance'];?></label></label>
                </div>
            </div>
        <?php } ?>
    </div>
</div>
<div class="clearfix"></div>
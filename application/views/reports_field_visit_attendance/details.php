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

    <div style="" class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE');?>:</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label"><?php echo System_helper::display_date($item['date']);?></label>
        </div>

    </div>

    <div style="" class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DIVISION_NAME');?>:</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label"><?php echo $item['division_name'];?></label>
        </div>
    </div>

    <div style="" class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ZONE_NAME');?>:</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label"><?php echo $item['zone_name'];?></label>
        </div>
    </div>

    <div style="" class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_TERRITORY_NAME');?>:</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label"><?php echo $item['territory_name'];?></label>
        </div>
    </div>

    <div style="" class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DISTRICT_NAME');?>:</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label"><?php echo $item['district_name'];?></label>
        </div>
    </div>

    <div style="" class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right">Outlet:</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label"><?php echo $item['outlet'];?></label>
        </div>
    </div>

    <div style="" class="row show-grid" id="farmer_id_container">
        <div class="col-xs-4">
            <label class="control-label pull-right">Dealer:</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label"><?php echo $item['dealer'];?></label>
        </div>
    </div>

    <div style="" class="row">
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Lead Farmer Visit Activities (1)</label>
            </div>
            <div class="col-xs-4">
                <label class="control-label"><?php echo $item['lead_farmer_visit_activities_one'];?></label>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
            </div>
            <div class="col-xs-4" id="image_lead_farmer_activities_one">
                <img style="max-width: 250px;" src="<?php echo $CI->config->item('system_base_url_dealer_and_farmer_visit').$item['image_location_lead_farmer_visit_one']; ?>" alt="<?php echo $item['image_name_lead_farmer_visit_one']; ?>">
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Lead Farmer Visit Activities (2)</label>
            </div>
            <div class="col-xs-4">
                <label class="control-label"><?php echo $item['lead_farmer_visit_activities_two'];?></label>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
            </div>
            <div class="col-xs-4" id="image_lead_farmer_activities_two">
                <img style="max-width: 250px;" src="<?php echo $CI->config->item('system_base_url_dealer_and_farmer_visit').$item['image_location_lead_farmer_visit_two']; ?>" alt="<?php echo $item['image_name_lead_farmer_visit_two']; ?>">
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Lead Farmer Visit Activities (3)</label>
            </div>
            <div class="col-xs-4">
                <label class="control-label"><?php echo $item['lead_farmer_visit_activities_three'];?></label>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
            </div>
            <div class="col-xs-4" id="image_lead_farmer_activities_three">
                <img style="max-width: 250px;" src="<?php echo $CI->config->item('system_base_url_dealer_and_farmer_visit').$item['image_location_lead_farmer_visit_three']; ?>" alt="<?php echo $item['image_name_lead_farmer_visit_three']; ?>">
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Farmer Visit Activities</label>
            </div>
            <div class="col-xs-4">
                <label class="control-label"><?php echo $item['farmer_visit_activities'];?></label>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
            </div>
            <div class="col-xs-4" id="image_farmer_activities">
                <img style="max-width: 250px;" src="<?php echo $CI->config->item('system_base_url_dealer_and_farmer_visit').$item['image_location_farmer_visit']; ?>" alt="<?php echo $item['image_name_farmer_visit']; ?>">
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Dealer Visit Activities</label>
            </div>
            <div class="col-xs-4">
                <label class="control-label"><?php echo $item['dealer_visit_activities'];?></label>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
            </div>
            <div class="col-xs-4" id="image_dealer_activities">
                <img style="max-width: 250px;" src="<?php echo $CI->config->item('system_base_url_dealer_and_farmer_visit').$item['image_location_dealer_visit']; ?>" alt="<?php echo $item['image_name_dealer_visit']; ?>">
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Other Activities</label>
            </div>
            <div class="col-xs-4">
                <label class="control-label"><?php echo $item['other_activities'];?></label>
            </div>
        </div>
    </div>
</div>
<div class="clearfix"></div>
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

    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE');?></label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label"><?php echo System_helper::display_date($item['date']);?></label>
        </div>
    </div>

    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DIVISION_NAME');?></label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label"><?php echo $item['division_name'];?></label>
        </div>
    </div>

    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ZONE_NAME');?></label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label"><?php echo $item['zone_name'];?></label>
        </div>
    </div>

    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_TERRITORY_NAME');?></label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label"><?php echo $item['territory_name'];?></label>
        </div>
    </div>

    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DISTRICT_NAME');?></label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label"><?php echo $item['district_name'];?></label>
        </div>
    </div>

    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right">Outlet</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label"><?php echo $item['customer_name'];?></label>
        </div>
    </div>

    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right">Dealer</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label"><?php echo $item['farmer_name'];?></label>
        </div>

    </div>

    <?php if(sizeof($dealer_info_file)>0){?>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label for="dealer_info_file_id" class="control-label pull-right">Dealer Info File</label>
            </div>
            <div id="dealer_info_file_id" class="col-sm-4 col-xs-8">
                <?php foreach($dealer_info_file as $key=>$file){$key++;?>
                    <a href="<?php echo $CI->config->item('system_base_url_picture').$file['image_location']; ?>" class="external btn btn-danger" target="_blank"><?php echo 'File '.$key;?></a>
                <?php } ?>
            </div>
        </div>
    <?php } ?>

    <div class="row">
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Lead Farmer Visit Activities (1)</label>
            </div>
            <div class="col-xs-4">
                <?php if($item['lead_farmer_visit_activities_one']){echo $item['lead_farmer_visit_activities_one'];}else{echo 'N/A';} ?>
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
                <label class="control-label pull-right">Lead Farmer Visit Activities (1)</label>
            </div>
            <div class="col-xs-4">
                <?php if($item['lead_farmer_visit_activities_two']){echo $item['lead_farmer_visit_activities_two'];}else{echo 'N/A';} ?>
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
                <label class="control-label pull-right">Lead Farmer Visit Activities (3)</label>
            </div>
            <div class="col-xs-4">
                <?php if($item['lead_farmer_visit_activities_three']){echo $item['lead_farmer_visit_activities_three'];}else{echo 'N/A';} ?>
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
                <label class="control-label pull-right">Farmer Visit Activities</label>
            </div>
            <div class="col-xs-4">
                <?php if($item['farmer_visit_activities']){echo $item['farmer_visit_activities'];}else{echo 'N/A';} ?>
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
                <label class="control-label pull-right">Dealer Visit Activities</label>
            </div>
            <div class="col-xs-4">
                <?php if($item['dealer_visit_activities']){echo $item['dealer_visit_activities'];}else{echo 'N/A';} ?>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
            </div>
            <div class="col-xs-4" id="image_dealer_activities">
                <img style="max-width: 250px;" src="<?php echo $CI->config->item('system_base_url_picture').$item['image_location_dealer_visit']; ?>" alt="<?php echo $item['image_name_dealer_visit']; ?>">
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Other Activities</label>
            </div>
            <div class="col-xs-4">
                <?php if($item['other_activities']){echo $item['other_activities'];}else{echo 'N/A';} ?>
            </div>
        </div>
    </div>

</div>

<div class="clearfix"></div>


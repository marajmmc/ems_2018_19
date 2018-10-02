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
<div class="row widget">
<div class="widget-header">
    <div class="title">
        <?php echo $title; ?>
    </div>
    <div class="clearfix"></div>
</div>

<div style="" class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE');?></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label"><?php echo System_helper::display_date($item['date']);?></label>
    </div>
</div>

<div style="" class="row show-grid">
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
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_OUTLET');?></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label"><?php echo $item['outlet'];?></label>
    </div>
</div>

<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DEALER');?></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label"><?php echo $item['dealer'];?></label>
    </div>
</div>

<?php if(isset($dealer_info_file) && sizeof($dealer_info_file)>0){?>
    <div class="row show-grid" id="dealer_info_file_container">
        <div class="col-xs-4">
            <label for="dealer_info_file_id" class="control-label pull-right">Dealer Info File</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <?php foreach($dealer_info_file as $key=>$file){$key++;?>
                <a href="<?php echo $CI->config->item('system_base_url_picture').$file['image_location']; ?>" class="external btn btn-danger" target="_blank"><?php echo 'File '.$key;?></a>
            <?php } ?>
        </div>
    </div>
<?php } ?>

<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_LEAD_FARMER_VISIT_ACTIVITIES_ONE');?></label>
    </div>
    <div class="col-xs-4 col-xs-8">
        <?php if($item['lead_farmer_visit_activities_one']){echo $item['lead_farmer_visit_activities_one'];}else{echo 'N/A';} ?>
    </div>
</div>

<div class="row show-grid">
    <div class="col-xs-4">
    </div>
    <div class="col-xs-4 col-xs-8" id="image_lead_farmer_activities_one">
        <img style="max-width: 250px;" src="<?php echo $CI->config->item('system_base_url_picture').$item['image_location_lead_farmer_visit_one']; ?>" alt="<?php echo $item['image_name_lead_farmer_visit_one']; ?>">
    </div>
</div>

<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_LEAD_FARMER_VISIT_ACTIVITIES_TWO');?></label>
    </div>
    <div class="col-xs-4 col-xs-8">
        <?php if($item['lead_farmer_visit_activities_two']){echo $item['lead_farmer_visit_activities_two'];}else{echo 'N/A';} ?>
    </div>
</div>

<div class="row show-grid">
    <div class="col-xs-4">
    </div>
    <div class="col-xs-4 col-xs-8" id="image_lead_farmer_activities_two">
        <img style="max-width: 250px;" src="<?php echo $CI->config->item('system_base_url_picture').$item['image_location_lead_farmer_visit_two']; ?>" alt="<?php echo $item['image_name_lead_farmer_visit_two']; ?>">
    </div>
</div>

<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_LEAD_FARMER_VISIT_ACTIVITIES_THREE');?></label>
    </div>
    <div class="col-xs-4 col-xs-8">
        <?php if($item['lead_farmer_visit_activities_three']){echo $item['lead_farmer_visit_activities_three'];}else{echo 'N/A';} ?>
    </div>
</div>

<div class="row show-grid">
    <div class="col-xs-4">
    </div>
    <div class="col-xs-4 col-xs-8" id="image_lead_farmer_activities_three">
        <img style="max-width: 250px;" src="<?php echo $CI->config->item('system_base_url_picture').$item['image_location_lead_farmer_visit_three']; ?>" alt="<?php echo $item['image_name_lead_farmer_visit_three']; ?>">
    </div>
</div>

<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_FARMER_VISIT_ACTIVITIES');?></label>
    </div>
    <div class="col-xs-4 col-xs-8">
        <?php if($item['farmer_visit_activities']){echo $item['farmer_visit_activities'];}else{echo 'N/A';} ?>
    </div>
</div>

<div class="row show-grid">
    <div class="col-xs-4">
    </div>
    <div class="col-xs-4 col-xs-8" id="image_farmer_activities">
        <img style="max-width: 250px;" src="<?php echo $CI->config->item('system_base_url_picture').$item['image_location_farmer_visit']; ?>" alt="<?php echo $item['image_name_farmer_visit']; ?>">
    </div>
</div>

<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DEALER_VISIT_ACTIVITIES');?></label>
    </div>
    <div class="col-xs-4 col-xs-8">
        <?php if($item['dealer_visit_activities']){echo $item['dealer_visit_activities'];}else{echo 'N/A';} ?>
    </div>
</div>

<div class="row show-grid">
    <div class="col-xs-4">
    </div>
    <div class="col-xs-4 col-xs-8" id="image_dealer_activities">
        <img style="max-width: 250px;" src="<?php echo $CI->config->item('system_base_url_picture').$item['image_location_dealer_visit']; ?>" alt="<?php echo $item['image_name_dealer_visit']; ?>">
    </div>
</div>

<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_OTHER_ACTIVITIES');?></label>
    </div>
    <div class="col-xs-4 col-xs-8">
        <?php if($item['other_activities']){echo $item['other_activities'];}else{echo 'N/A';} ?>
    </div>
</div>

<form class="form_valid" id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save');?>" method="post">
    <input type="hidden" id="id" name="id" value="<?php echo $item['id']; ?>" />
    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ZSC_COMMENT');?></label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <textarea name="item[zsc_comment]" class="form-control"><?php echo $item['zsc_comment'] ?></textarea>
        </div>
    </div>

    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_STATUS_ATTENDANCE');?><span style="color:#FF0000">*</span></label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <select name="item[status_attendance]" class="form-control">
                <option value=""><?php echo $this->lang->line('SELECT');?></option>
                <option value="Present"
                    <?php
                    if ($item['status_attendance'] == 'Present') {
                        echo "selected='selected'";
                    }
                    ?> >Present
                </option>
                <option value="CL"
                    <?php
                    if ($item['status_attendance'] == 'CL') {
                        echo "selected='selected'";
                    }
                    ?> >CL</option>
                <option value="Absent"
                    <?php
                    if ($item['status_attendance'] == 'Absent') {
                        echo "selected='selected'";
                    }
                    ?> >Absent</option>
            </select>
        </div>
    </div>
    <div class="row show-grid">
        <div class="col-xs-4">

        </div>
        <div class="col-sm-4 col-xs-4">
            <div class="action_button">
                <button id="button_action_save" type="button" class="btn" data-form="#save_form">Save</button>
            </div>
        </div>
        <div class="col-sm-4 col-xs-4">

        </div>
    </div>

</div>

<div class="clearfix"></div>
</form>
<script type="text/javascript">
    jQuery(document).ready(function()
    {
        system_preset({controller:'<?php echo $CI->router->class; ?>'});
        $(".datepicker").datepicker({dateFormat : display_date_format});
    });
</script>

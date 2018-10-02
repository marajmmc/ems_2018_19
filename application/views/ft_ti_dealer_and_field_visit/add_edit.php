<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
$action_buttons[]=array
(
    'label'=>$CI->lang->line("ACTION_BACK"),
    'href'=>site_url($CI->controller_url)
);
if((isset($CI->permissions['action1']) && ($CI->permissions['action1']==1)) || (isset($CI->permissions['action2']) && ($CI->permissions['action2']==1)))
{
    $action_buttons[]=array
    (
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_SAVE"),
        'id'=>'button_action_save',
        'data-form'=>'#save_form'
    );
}
$action_buttons[]=array(
    'type'=>'button',
    'label'=>$CI->lang->line("ACTION_CLEAR"),
    'id'=>'button_action_clear',
    'data-form'=>'#save_form'
);
$CI->load->view('action_buttons',array('action_buttons'=>$action_buttons));

?>
<style>
    .datepicker
    {
        cursor: pointer !important;
    }
</style>
<form class="form_valid" id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save');?>" method="post">
<input type="hidden" id="id" name="id" value="<?php echo $item['id']; ?>" />
<input type="hidden" id="system_save_new_status" name="system_save_new_status" value="0" />
<div class="row widget">
<div class="widget-header">
    <div class="title">
        <?php echo $title; ?>
    </div>
    <div class="clearfix"></div>
</div>

<div style="" class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE');?><span style="color:#FF0000">*</span></label>
    </div>
    <?php if($item['id']>0){?>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label"><?php echo System_helper::display_date($item['date']);?></label>
        </div>
    <?php } else{?>
        <div class="col-sm-4 col-xs-8">
            <input type="text" name="item[date]" id="date" class="form-control datepicker" value="<?php echo System_helper::display_date($item['date']);?>" readonly/>
        </div>
    <?php } ?>
</div>

<div style="" class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DIVISION_NAME');?><span style="color:#FF0000">*</span></label>
    </div>
    <?php if($item['division_id']){?>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label"><?php echo $item['division_name'];?></label>
        </div>
    <?php } else{?>
        <div class="col-sm-4 col-xs-8">
            <?php
            if($CI->locations['division_id']>0)
            {
                ?>
                <label class="control-label"><?php echo $CI->locations['division_name'];?></label>
                <input type="hidden" value="<?php echo $CI->locations['division_id'];?>">
            <?php
            }
            else
            {
                ?>
                <select id="division_id" class="form-control">
                    <option value=""><?php echo $this->lang->line('SELECT');?></option>
                    <?php
                    foreach($divisions as $division)
                    {?>
                        <option value="<?php echo $division['value']?>" <?php if($division['value']==$item['division_id']){ echo "selected";}?>><?php echo $division['text'];?></option>
                    <?php
                    }
                    ?>
                </select>
            <?php
            }
            ?>
        </div>
    <?php } ?>
</div>

<div style="<?php if(!(sizeof($zones)>0)){echo 'display:none';} ?>" class="row show-grid" id="zone_id_container">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ZONE_NAME');?><span style="color:#FF0000">*</span></label>
    </div>
    <?php if($item['zone_id']){?>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label"><?php echo $item['zone_name'];?></label>
        </div>
    <?php } else{?>
        <div class="col-sm-4 col-xs-8">
            <?php
            if($CI->locations['zone_id']>0)
            {
                ?>
                <label class="control-label"><?php echo $CI->locations['zone_name'];?></label>
            <?php
            }
            else
            {
                ?>
                <select id="zone_id" class="form-control">
                    <option value=""><?php echo $this->lang->line('SELECT');?></option>
                    <?php
                    foreach($zones as $zone)
                    {?>
                        <option value="<?php echo $zone['value']?>" <?php if($zone['value']==$item['zone_id']){ echo "selected";}?>><?php echo $zone['text'];?></option>
                    <?php
                    }
                    ?>
                </select>
            <?php
            }
            ?>
        </div>
    <?php } ?>
</div>

<div style="<?php if(!(sizeof($territories)>0)){echo 'display:none';} ?>" class="row show-grid" id="territory_id_container">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_TERRITORY_NAME');?><span style="color:#FF0000">*</span></label>
    </div>
    <?php if($item['territory_id']){?>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label"><?php echo $item['territory_name'];?></label>
        </div>
    <?php } else{?>
        <div class="col-sm-4 col-xs-8">
            <?php
            if($CI->locations['territory_id']>0)
            {
                ?>
                <label class="control-label"><?php echo $CI->locations['territory_name'];?></label>
            <?php
            }
            else
            {
                ?>
                <select id="territory_id" class="form-control">
                    <option value=""><?php echo $this->lang->line('SELECT');?></option>
                    <?php
                    foreach($territories as $territory)
                    {?>
                        <option value="<?php echo $territory['value']?>" <?php if($territory['value']==$item['territory_id']){ echo "selected";}?>><?php echo $territory['text'];?></option>
                    <?php
                    }
                    ?>
                </select>
            <?php
            }
            ?>

        </div>
    <?php } ?>
</div>

<div style="<?php if(!(sizeof($districts)>0)){echo 'display:none';} ?>" class="row show-grid" id="district_id_container">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DISTRICT_NAME');?><span style="color:#FF0000">*</span></label>
    </div>
    <?php if($item['district_id']){?>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label"><?php echo $item['district_name'];?></label>
        </div>
    <?php } else{?>
        <div class="col-sm-4 col-xs-8">
            <?php
            if($CI->locations['district_id']>0)
            {
                ?>
                <label class="control-label"><?php echo $CI->locations['district_name'];?></label>
                <input type="hidden" value="<?php echo $CI->locations['district_id'];?>">
            <?php
            }
            else
            {
                ?>
                <select id="district_id" class="form-control">
                    <option value=""><?php echo $this->lang->line('SELECT');?></option>
                    <?php
                    foreach($districts as $district)
                    {?>
                        <option value="<?php echo $district['value']?>" <?php if($district['value']==$item['district_id']){ echo "selected";}?>><?php echo $district['text'];?></option>
                    <?php
                    }
                    ?>
                </select>
            <?php
            }
            ?>

        </div>
    <?php } ?>
</div>

<div style="<?php if(!($item['customer_id'])){echo 'display:none';} ?>" class="row show-grid" id="customer_id_container">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_OUTLET');?><span style="color:#FF0000">*</span></label>
    </div>
    <?php if($item['customer_id']){?>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label"><?php echo $item['customer_name'];?></label>
        </div>
    <?php } else{?>
        <div class="col-sm-4 col-xs-8">
            <select id="customer_id" name="item[customer_id]" class="form-control">
                <option value=""><?php echo $CI->lang->line('SELECT');?></option>
                <?php
                foreach($customers as $customer)
                {?>
                    <option value="<?php echo $customer['value']?>" <?php if($customer['value']==$item['customer_id']){ echo "selected";}?>><?php echo $customer['text'];?></option>
                <?php
                }
                ?>
            </select>
        </div>
    <?php } ?>
</div>

<div style="<?php if(!($item['farmer_id'])){echo 'display:none';} ?>" class="row show-grid" id="farmer_id_container">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DEALER');?><span style="color:#FF0000">*</span></label>
    </div>
    <?php if($item['farmer_id']){?>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label"><?php echo $item['farmer_name'];?></label>
        </div>
    <?php } else{?>
        <div class="col-sm-4 col-xs-8">
            <select id="farmer_id" name="item[farmer_id]" class="form-control">
                <option value=""><?php echo $CI->lang->line('SELECT');?></option>
                <?php
                foreach($farmers as $farmer)
                {?>
                    <option value="<?php echo $farmer['value']?>" <?php if($farmer['value']==$item['farmer_id']){ echo "selected";}?>><?php echo $farmer['text'];?></option>
                <?php
                }
                ?>
            </select>
        </div>
    <?php } ?>
</div>

<div style="<?php if(!($dealer_info_file)){echo 'display:none';} ?>" class="row show-grid" id="dealer_info_file_container">
    <div class="col-xs-4">
        <label for="dealer_info_file_id" class="control-label pull-right">Dealer Info File</label>
    </div>
    <div id="dealer_info_file_id" class="col-sm-4 col-xs-8">
        <?php foreach($dealer_info_file as $key=>$file){$key++;?>
            <a href="<?php echo $CI->config->item('system_base_url_picture').$file['image_location']; ?>" class="external btn btn-danger" target="_blank"><?php echo 'File '.$key;?></a>
        <?php } ?>
    </div>
</div>

<div style="<?php if(!($item['id']>0)){echo 'display:none';} ?>" class="row" id="dealer_activities_container">


    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_LEAD_FARMER_VISIT_ACTIVITIES_ONE');?></label>
        </div>
        <div class="col-xs-4">
            <textarea id="lead_farmer_visit_activities_one" name="item[lead_farmer_visit_activities_one]" class="form-control"><?php echo $item['lead_farmer_visit_activities_one'] ?></textarea>
        </div>
    </div>

    <div class="row show-grid">
        <div class="col-xs-4">
        </div>
        <div class="col-xs-4">
            <input type="file" class="browse_button" data-preview-container="#image_lead_farmer_activities_one" data-preview-width="300" name="image_lead_farmer_activities_one">
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
            <textarea id="lead_farmer_visit_activities_two" name="item[lead_farmer_visit_activities_two]" class="form-control"><?php echo $item['lead_farmer_visit_activities_two'] ?></textarea>
        </div>
    </div>

    <div class="row show-grid">
        <div class="col-xs-4">
        </div>
        <div class="col-xs-4">
            <input type="file" class="browse_button" data-preview-container="#image_lead_farmer_activities_two" data-preview-width="300" name="image_lead_farmer_activities_two">
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
            <textarea id="lead_farmer_visit_activities_three" name="item[lead_farmer_visit_activities_three]" class="form-control"><?php echo $item['lead_farmer_visit_activities_three'] ?></textarea>
        </div>
    </div>

    <div class="row show-grid">
        <div class="col-xs-4">
        </div>
        <div class="col-xs-4">
            <input type="file" class="browse_button" data-preview-container="#image_lead_farmer_activities_three" data-preview-width="300" name="image_lead_farmer_activities_three">
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
            <textarea id="farmer_visit_activities" name="item[farmer_visit_activities]" class="form-control"><?php echo $item['farmer_visit_activities'] ?></textarea>
        </div>
    </div>

    <div class="row show-grid">
        <div class="col-xs-4">
        </div>
        <div class="col-xs-4">
            <input type="file" class="browse_button" data-preview-container="#image_farmer_activities" data-preview-width="300" name="image_farmer_activities">
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
            <textarea id="dealer_visit_activities" name="item[dealer_visit_activities]" class="form-control"><?php echo $item['dealer_visit_activities'] ?></textarea>
        </div>
    </div>

    <div class="row show-grid">
        <div class="col-xs-4">
        </div>
        <div class="col-xs-4">
            <input type="file" class="browse_button" data-preview-container="#image_dealer_activities" data-preview-width="300" name="image_dealer_activities">
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
            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_OTHER_ACTIVITIES');?></label>
        </div>
        <div class="col-xs-4">
            <textarea id="other_activities" name="item[other_activities]" class="form-control"><?php echo $item['other_activities'] ?></textarea>
        </div>
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
    $(".browse_button").filestyle({input: false,icon: false,buttonText: "Upload Picture",buttonName: "btn-primary"});
    $(document).off('change', '#date');
    $(document).off('change', '#division_id');
    $(document).off('change', '#zone_id');
    $(document).off('change', '#territory_id');
    $(document).off('change', '#district_id');
    $(document).off('change', '#customer_id');
    $(document).off('change', '#farmer_id');

    $(document).on('change','#date',function()
    {
        $('#farmer_id').val('');
        $('#dealer_visit_activities').val('');
        $('#lead_farmer_visit_activities_one').val('');
        $('#lead_farmer_visit_activities_two').val('');
        $('#lead_farmer_visit_activities_three').val('');
        $('#farmer_visit_activities').val('');
        $('#other_activities').val('');
        var date=$('#date').val();
        var customer_id=$('#customer_id').val();
        $('#farmer_id_container').hide();
        $('#dealer_info_file_container').hide();
        $('#dealer_activities_container').hide();
        if(date && customer_id>0)
        {
            $.ajax({
                url:"<?php echo site_url($CI->controller_url.'/get_dropdown_farmers_by_customer_id/');?>",
                type: 'POST',
                datatype: "JSON",
                data:{date:date,customer_id:customer_id},
                success: function (data, status)
                {
                    if(data['status'])
                    {
                        $('#farmer_id_container').show();
                    }
                },
                error: function (xhr, desc, err)
                {
                    console.log("error");

                }
            });
        }
    });

    $(document).on('change','#division_id',function()
    {
        $('#zone_id').val('');
        $('#territory_id').val('');
        $('#district_id').val('');
        $('#customer_id').val('');
        $('#farmer_id').val('');
        $('#dealer_visit_activities').val('');
        $('#lead_farmer_visit_activities_one').val('');
        $('#lead_farmer_visit_activities_two').val('');
        $('#lead_farmer_visit_activities_three').val('');
        $('#farmer_visit_activities').val('');
        $('#other_activities').val('');
        var division_id=$('#division_id').val();
        $('#zone_id_container').hide();
        $('#territory_id_container').hide();
        $('#district_id_container').hide();
        $('#customer_id_container').hide();
        $('#farmer_id_container').hide();
        $('#dealer_info_file_container').hide();
        $('#dealer_activities_container').hide();
        if(division_id>0)
        {
            if(system_zones[division_id]!==undefined)
            {
                $('#zone_id_container').show();
                $('#zone_id').html(get_dropdown_with_select(system_zones[division_id]));
            }
        }
    });
    $(document).on('change','#zone_id',function()
    {
        $('#territory_id').val('');
        $('#district_id').val('');
        $('#customer_id').val('');
        $('#farmer_id').val('');
        $('#dealer_visit_activities').val('');
        $('#lead_farmer_visit_activities_one').val('');
        $('#lead_farmer_visit_activities_two').val('');
        $('#lead_farmer_visit_activities_three').val('');
        $('#farmer_visit_activities').val('');
        $('#other_activities').val('');
        var zone_id=$('#zone_id').val();
        $('#territory_id_container').hide();
        $('#district_id_container').hide();
        $('#customer_id_container').hide();
        $('#farmer_id_container').hide();
        $('#dealer_info_file_container').hide();
        $('#dealer_activities_container').hide();
        if(zone_id>0)
        {
            if(system_territories[zone_id]!==undefined)
            {
                $('#territory_id_container').show();
                $('#territory_id').html(get_dropdown_with_select(system_territories[zone_id]));
            }
        }
    });
    $(document).on('change','#territory_id',function()
    {
        $('#district_id').val('');
        $('#customer_id').val('');
        $('#farmer_id').val('');
        $('#dealer_visit_activities').val('');
        $('#lead_farmer_visit_activities_one').val('');
        $('#lead_farmer_visit_activities_two').val('');
        $('#lead_farmer_visit_activities_three').val('');
        $('#farmer_visit_activities').val('');
        $('#other_activities').val('');
        var territory_id=$('#territory_id').val();
        $('#district_id_container').hide();
        $('#customer_id_container').hide();
        $('#farmer_id_container').hide();
        $('#dealer_info_file_container').hide();
        $('#dealer_activities_container').hide();
        if(territory_id>0)
        {
            if(system_districts[territory_id]!==undefined)
            {
                $('#district_id_container').show();
                $('#district_id').html(get_dropdown_with_select(system_districts[territory_id]));
            }

        }
    });
    $(document).on('change','#district_id',function()
    {
        $('#customer_id').val('');
        $('#farmer_id').val('');
        $('#dealer_visit_activities').val('');
        $('#lead_farmer_visit_activities_one').val('');
        $('#lead_farmer_visit_activities_two').val('');
        $('#lead_farmer_visit_activities_three').val('');
        $('#farmer_visit_activities').val('');
        $('#other_activities').val('');
        var district_id=$('#district_id').val();
        $('#customer_id_container').hide();
        $('#farmer_id_container').hide();
        $('#dealer_info_file_container').hide();
        $('#dealer_activities_container').hide();
        if(district_id>0)
        {
            if(system_outlets[district_id]!==undefined)
            {
                $('#customer_id_container').show();
                $('#customer_id').html(get_dropdown_with_select(system_outlets[district_id]));
            }
        }
    });
    $(document).on('change','#customer_id',function()
    {
        $('#farmer_id').val('');
        $('#dealer_visit_activities').val('');
        $('#lead_farmer_visit_activities_one').val('');
        $('#lead_farmer_visit_activities_two').val('');
        $('#lead_farmer_visit_activities_three').val('');
        $('#farmer_visit_activities').val('');
        $('#other_activities').val('');
        var date=$('#date').val();
        var customer_id=$('#customer_id').val();
        $('#farmer_id_container').hide();
        $('#dealer_info_file_container').hide();
        $('#dealer_activities_container').hide();
        if(customer_id>0)
        {
            $.ajax({
                url:"<?php echo site_url($CI->controller_url.'/get_dropdown_farmers_by_customer_id/');?>",
                type: 'POST',
                datatype: "JSON",
                data:{date:date,customer_id:customer_id},
                success: function (data, status)
                {
                    if(data['status'])
                    {
                        $('#farmer_id_container').show();
                    }
                },
                error: function (xhr, desc, err)
                {
                    console.log("error");

                }
            });
        }
        else
        {
            $('#farmer_id_container').hide();
        }
    });

    $(document).on('change','#farmer_id',function()
    {
        $('#dealer_visit_activities').val('');
        $('#lead_farmer_visit_activities_one').val('');
        $('#lead_farmer_visit_activities_two').val('');
        $('#lead_farmer_visit_activities_three').val('');
        $('#farmer_visit_activities').val('');
        $('#other_activities').val('');
        var date=$('#date').val();
        var customer_id=$('#customer_id').val();
        var farmer_id=$('#farmer_id').val();
        $('#dealer_info_file_container').hide();
        $('#dealer_activities_container').hide();
        if(farmer_id>0)
        {
            $('#dealer_activities_container').show();
            $.ajax({
                url:"<?php echo site_url($CI->controller_url.'/get_dealer_info_file/');?>",
                type: 'POST',
                datatype: "JSON",
                data:{farmer_id:farmer_id},
                success: function (data, status)
                {
                    if(data['status'])
                    {
                        $('#dealer_info_file_container').show();
                        $("#dealer_info_file_id").text(data);
                    }

                },
                error: function (xhr, desc, err)
                {
                    console.log("error");

                }
            });
        }
    });
});
</script>

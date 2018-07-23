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
    $action_buttons[]=array
    (
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_SAVE_NEW"),
        'id'=>'button_action_save_new',
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
<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_YEAR');?></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label><?php echo $item['year'];?></label>
        <input type="hidden" name="item[year]" value="<?php echo $item['year']; ?>" />
    </div>
</div>
<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_SEASON');?><span style="color:#FF0000">*</span></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <select name="item[season_id]" class="form-control">
            <option value=""><?php echo $CI->lang->line('SELECT');?></option>
            <?php
            foreach($seasons as $season)
            {?>
                <option value="<?php echo $season['value']?>" <?php if($season['value']==$item['season_id']){ echo "selected";}?>><?php echo $season['text'];?></option>
            <?php
            }
            ?>
        </select>
    </div>
</div>
<div style="" class="row show-grid" id="crop_id_container">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_NAME');?><span style="color:#FF0000">*</span></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <select id="crop_id" name="crop_id" class="form-control">
            <option value=""><?php echo $CI->lang->line('SELECT');?></option>
            <?php
            foreach($crops as $crop)
            {?>
                <option value="<?php echo $crop['value']?>" <?php if($crop['value']==$item['crop_id']){ echo "selected";}?>><?php echo $crop['text'];?></option>
            <?php
            }
            ?>
        </select>
    </div>
</div>
<div style="<?php if(!($item['crop_id']>0)){echo 'display:none';} ?>" class="row show-grid" id="crop_type_id_container">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_TYPE_NAME');?><span style="color:#FF0000">*</span></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <select id="crop_type_id" name="crop_type_id" class="form-control">
            <option value=""><?php echo $CI->lang->line('SELECT');?></option>
            <?php
            foreach($types as $type)
            {?>
                <option value="<?php echo $type['value']?>" <?php if($type['value']==$item['type_id']){ echo "selected";}?>><?php echo $type['text'];?></option>
            <?php
            }
            ?>
        </select>
    </div>
</div>
<div style="<?php if(!($item['type_id']>0)){echo 'display:none';} ?>" class="row show-grid" id="variety_id_container">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_VARIETY_NAME');?><span style="color:#FF0000">*</span></label>
    </div>
    <div class="col-sm-4 col-xs-8" id="variety_list_container">
        <?php
        foreach($varieties as $variety)
        {
            ?>
            <div class="checkbox">
                <label><input type="checkbox" name="variety_ids[]" value="<?php echo $variety['value']; ?>" <?php if(isset($previous_varieties[$variety['value']])) echo 'checked'; ?>><?php echo $variety['text'].' ('.$variety['whose'].')'; ?></label>
            </div>
        <?php
        }
        ?>
    </div>
</div>
<div style="" class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DIVISION_NAME');?><span style="color:#FF0000">*</span></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <?php
        if($CI->locations['division_id']>0)
        {
            ?>
            <label class="control-label"><?php echo $CI->locations['division_name'];?></label>
        <?php
        }
        else
        {
            ?>
            <select id="division_id" name="division_id" class="form-control">
                <option value=""><?php echo $CI->lang->line('SELECT');?></option>
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
</div>

<div style="<?php if(!(sizeof($zones)>0)){echo 'display:none';} ?>" class="row show-grid" id="zone_id_container">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ZONE_NAME');?><span style="color:#FF0000">*</span></label>
    </div>
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
            <select id="zone_id" name="zone_id" class="form-control">
                <option value=""><?php echo $CI->lang->line('SELECT');?></option>
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
</div>
<div style="<?php if(!(sizeof($territories)>0)){echo 'display:none';} ?>" class="row show-grid" id="territory_id_container">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_TERRITORY_NAME');?><span style="color:#FF0000">*</span></label>
    </div>
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
            <select id="territory_id" name="territory_id" class="form-control">
                <option value=""><?php echo $CI->lang->line('SELECT');?></option>
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
</div>
<div style="<?php if(!(sizeof($districts)>0)){echo 'display:none';} ?>" class="row show-grid" id="district_id_container">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DISTRICT_NAME');?><span style="color:#FF0000">*</span></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <?php
        if($CI->locations['district_id']>0)
        {
            ?>
            <label class="control-label"><?php echo $CI->locations['district_name'];?></label>
        <?php
        }
        else
        {
            ?>
            <select id="district_id" name="district_id" class="form-control">
                <option value=""><?php echo $CI->lang->line('SELECT');?></option>
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
</div>
<div style="<?php if(!(sizeof($upazillas)>0)){echo 'display:none';} ?>" class="row show-grid" id="upazilla_id_container">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_UPAZILLA_NAME');?><span style="color:#FF0000">*</span></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <?php
        if($CI->locations['upazilla_id']>0)
        {
            ?>
            <label class="control-label"><?php echo $CI->locations['upazilla_name'];?></label>
            <input type="hidden" id="upazilla_id" name="item[upazilla_id]" value="<?php echo $CI->locations['upazilla_id']; ?>">
        <?php
        }
        else
        {
            ?>
            <select id="upazilla_id" name="item[upazilla_id]" class="form-control">
                <option value=""><?php echo $CI->lang->line('SELECT');?></option>
                <?php
                foreach($upazillas as $upazilla)
                {?>
                    <option value="<?php echo $upazilla['value']?>" <?php if($upazilla['value']==$item['upazilla_id']){ echo "selected";}?>><?php echo $upazilla['text'];?></option>
                <?php
                }
                ?>
            </select>
        <?php
        }
        ?>
    </div>
</div>

<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right">Farmer's Name<span style="color:#FF0000">*</span></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <input type="text" name="item[name]" class="form-control" value="<?php echo $item['name']; ?>">
    </div>
</div>
<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ADDRESS');?><span style="color:#FF0000">*</span></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <textarea name="item[address]" class="form-control"><?php echo $item['address']; ?></textarea>
    </div>
</div>
<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CONTACT_NO');?></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <input type="text" name="item[contact_no]" class="form-control float_type_positive" value="<?php echo $item['contact_no']; ?>">
    </div>
</div>
<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_SOWING');?><span style="color:#FF0000">*</span></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <input type="text" name="item[date_sowing]" class="form-control datepicker" value="<?php echo System_helper::display_date($item['date_sowing']); ?>" readonly>
    </div>
</div>
<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_TRANSPLANT');?></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <input type="text" name="item[date_transplant]" class="form-control datepicker" value="<?php if($item['date_transplant']>0){echo System_helper::display_date($item['date_transplant']); }?>" readonly>
    </div>
</div>

<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_INTERVAL');?><span style="color:#FF0000">*</span></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <select name="item[interval]" class="form-control">
            <?php
            for($i=0;$i<=30;$i++)
            {
                ?>
                <option value="<?php echo $i;?>" <?php if($i==$item['interval']){ echo "selected";}?>><?php echo $i;?></option>
            <?php
            }
            ?>
        </select>
    </div>
</div>
<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_NUM_VISITS');?><span style="color:#FF0000">*</span></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <select name="item[num_visits]" class="form-control">
            <?php
            for($i=0;$i<=30;$i++)
            {
                ?>
                <option value="<?php echo $i;?>" <?php if($i==$item['num_visits']){ echo "selected";}?>><?php echo $i;?></option>
            <?php
            }
            ?>
        </select>
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
        $(document).off('change','#division_id');
        $(document).on("change","#division_id",function()
        {
            $("#zone_id").val("");
            $("#territory_id").val("");
            $("#district_id").val("");
            $("#upazilla_id").val("");
            $('#zone_id_container').hide();
            $('#territory_id_container').hide();
            $('#district_id_container').hide();
            $('#upazilla_id_container').hide();
            var division_id=$('#division_id').val();
            if(division_id>0)
            {
                if(system_zones[division_id]!==undefined)
                {
                    $('#zone_id_container').show();
                    $("#zone_id").html(get_dropdown_with_select(system_zones[division_id]));
                }
            }
        });
        $(document).off('change','#zone_id');
        $(document).on("change","#zone_id",function()
        {

            $("#territory_id").val("");
            $("#district_id").val("");
            $("#upazilla_id").val("");
            $('#territory_id_container').hide();
            $('#district_id_container').hide();
            $('#upazilla_id_container').hide();
            var zone_id=$('#zone_id').val();
            if(zone_id>0)
            {
                if(system_territories[zone_id]!==undefined)
                {
                    $('#territory_id_container').show();
                    $("#territory_id").html(get_dropdown_with_select(system_territories[zone_id]));
                }
            }
        });
        $(document).off('change','#territory_id');
        $(document).on("change","#territory_id",function()
        {
            $("#district_id").val("");
            $("#upazilla_id").val("");
            $('#district_id_container').hide();
            $('#upazilla_id_container').hide();
            var territory_id=$('#territory_id').val();
            if(territory_id>0)
            {
                if(system_districts[territory_id]!==undefined)
                {
                    $('#district_id_container').show();
                    $("#district_id").html(get_dropdown_with_select(system_districts[territory_id]));
                }
            }

        });
        $(document).off('change','#district_id');
        $(document).on("change","#district_id",function()
        {

            $("#upazilla_id").val("");
            var district_id=$("#district_id").val();
            if(district_id>0)
            {
                $('#upazilla_id_container').show();
                $.ajax({
                    url:"<?php echo site_url('/common_controller/get_dropdown_upazillas_by_districtid/');?>",
                    type: 'POST',
                    datatype: "JSON",
                    data:{district_id:district_id},
                    success: function (data, status)
                    {

                    },
                    error: function (xhr, desc, err)
                    {
                        console.log("error");

                    }
                });
            }
            else
            {
                $('#upazilla_id_container').hide();
            }

        });
        $(document).off('change','#crop_id');
        $(document).on("change","#crop_id",function()
        {
            $("#crop_type_id").val("");
            $('#variety_list_container').html('');
            var crop_id=$('#crop_id').val();
            $('#crop_type_id_container').hide();
            $('#variety_id_container').hide();
            if(crop_id>0)
            {
                if(system_types[crop_id]!==undefined)
                {
                    $('#crop_type_id_container').show();
                    $("#crop_type_id").html(get_dropdown_with_select(system_types[crop_id]));
                }
            }
        });
        $(document).off('change','#crop_type_id');
        $(document).on("change","#crop_type_id",function()
        {
            $('#variety_list_container').html('');
            $('#variety_id_container').hide();
            var crop_type_id=$('#crop_type_id').val();
            if(crop_type_id>0)
            {
                $('#variety_id_container').show();
                $.ajax({
                    url:"<?php echo site_url($CI->controller_url.'/index/list_variety');?>",
                    type: 'POST',
                    datatype: "JSON",
                    data:{crop_type_id:crop_type_id},
                    success: function (data, status)
                    {

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

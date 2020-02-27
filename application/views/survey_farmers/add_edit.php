<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();

$action_buttons = array();
$action_buttons[] = array
(
    'label' => $CI->lang->line("ACTION_BACK"),
    'href' => site_url($CI->controller_url)
);
if ((isset($CI->permissions['action1']) && ($CI->permissions['action1'] == 1)) || (isset($CI->permissions['action2']) && ($CI->permissions['action2'] == 1))) {
    $action_buttons[] = array
    (
        'type' => 'button',
        'label' => $CI->lang->line("ACTION_SAVE"),
        'id' => 'button_action_save',
        'data-form' => '#save_form'
    );
}
$action_buttons[] = array
(
    'type' => 'button',
    'label' => $CI->lang->line("ACTION_CLEAR"),
    'id' => 'button_action_clear',
    'data-form' => '#save_form'
);
$CI->load->view("action_buttons", array('action_buttons' => $action_buttons));
?>

<form class="form_valid" id="save_form" action="<?php echo site_url($CI->controller_url . '/index/save'); ?>" method="post">
    <input type="hidden" id="id" name="id" value="<?php echo $item['id']; ?>"/>
    <div class="row widget">
        <div class="widget-header">
            <div class="title">
                <?php echo $title; ?>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-12">

                <table class="table table-bordered table-responsive-sm ">
                    <tbody>
                    <tr>
                        <th style="width: 30%"><?php echo $this->lang->line('SURVEY_FARMER_TITLE_FARMER_NAME');?> <strong class="text-danger">*</strong></th>
                        <th style="width: 70%"><input type="text" class="form-control" id="farmer_name" name="item[farmer_name]" value="<?php echo $item['farmer_name'];?>" /></th>
                    </tr>
                    <tr>
                        <th><?php echo $this->lang->line('SURVEY_FARMER_TITLE_FATHER_HUSBAND_NAME');?> <strong class="text-danger">*</strong></th>
                        <th><input type="text" class="form-control" id="father_husband_name" name="item[father_husband_name]" value="<?php echo $item['father_husband_name'];?>" /></th>
                    </tr>
                    <tr>
                        <th colspan="2"><?php echo $this->lang->line('SURVEY_FARMER_TITLE_ADDRESS');?></th>
                    </tr>
                    <tr>
                        <th colspan="2">
                            <div class="form-row">
                                <div class="col-md-3">
                                    <label for="validationTooltip01"><?php echo $this->lang->line('SURVEY_FARMER_DISTRICT_NAME');?></label>
                                    <select class="form-control" id="district_id" name="item[district_id]">
                                        <option value=""><?php echo $CI->lang->line('SELECT'); ?></option>
                                        <?php
                                        foreach ($districts as $district)
                                        {
                                            ?>
                                            <option value="<?php echo $district['value'] ?>" <?php if($item['district_id']==$district['value']){echo "selected='selected'";}?>><?php echo $district['text']; ?></option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="validationTooltip02"><?php echo $this->lang->line('SURVEY_FARMER_UPAZILLA_NAME');?></label>
                                    <select class="form-control" id="upazilla_id" name="item[upazilla_id]">
                                        <option value=""><?php echo $CI->lang->line('SELECT'); ?></option>

                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="validationTooltip02"><?php echo $this->lang->line('SURVEY_FARMER_UNION_NAME');?></label>
                                    <select class="form-control" id="union_id" name="item[union_id]">
                                        <option value=""><?php echo $CI->lang->line('SELECT'); ?></option>

                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="validationTooltip02"><?php echo $this->lang->line('SURVEY_FARMER_VILLAGE_NAME');?></label>
                                    <input type="text" class="form-control" id="village_name" name="item[village_name]" value="<?php echo $item['village_name'];?>" />
                                </div>
                                <div class="col-md-12">
                                    <hr/>
                                </div>
                                <div class="col-md-3">
                                    <label for="validationTooltip02"><?php echo $this->lang->line('SURVEY_FARMER_MOBILE_NO');?> <strong class="text-danger">*</strong></label>
                                    <input type="text" class="form-control" id="mobile_no" name="item[mobile_no]" value="<?php echo $item['mobile_no'];?>" />
                                </div>
                                <div class="col-md-3">
                                    <label for="validationTooltip02"><?php echo $this->lang->line('SURVEY_FARMER_NID_NO');?></label>
                                    <input type="text" class="form-control" id="nid_no" name="item[nid_no]" value="<?php echo $item['nid_no'];?>" />
                                </div>
                                <div class="col-md-3">
                                    <label for="validationTooltip02"><?php echo $this->lang->line('SURVEY_FARMER_GROWING_AREA');?></label>
                                    <input type="text" class="form-control" id="growing_area" name="item[growing_area]" value="<?php echo $item['growing_area'];?>" />
                                </div>
                            </div>
                        </th>
                    </tr>
                    <tr>
                        <th><?php echo $this->lang->line('SURVEY_FARMER_TITLE_FAMILY_MEMBER');?></th>
                        <th>
                            <div class="col-md-3">
                                <label for="validationTooltip02"><?php echo $this->lang->line('SURVEY_FARMER_FAMILY_MEMBER_FEMALE');?></label>
                                <input type="text" class="form-control family_member float_type_positive" id="family_member_female" name="item[family_member_female]" value="<?php echo $item['family_member_female'];?>" />
                            </div>
                            <div class="col-md-3">
                                <label for="validationTooltip02"><?php echo $this->lang->line('SURVEY_FARMER_FAMILY_MEMBER_MALE');?></label>
                                <input type="text" class="form-control family_member float_type_positive" id="family_member_male" name="item[family_member_male]" value="<?php echo $item['family_member_male'];?>" />
                            </div>
                            <div class="col-md-3">
                                <label for="validationTooltip02"><?php echo $this->lang->line('SURVEY_FARMER_FAMILY_MEMBER_OTHERS');?></label>
                                <input type="text" class="form-control family_member float_type_positive" id="family_member_others" name="item[family_member_others]" value="<?php echo $item['family_member_others'];?>" />
                            </div>
                            <div class="col-md-3">
                                <label for="validationTooltip02"><?php echo $this->lang->line('SURVEY_FARMER_FAMILY_MEMBER_TOTAL');?></label>
                                <input type="text" class="form-control float_type_positive" id="family_member_total" name="" value="<?php echo ($item['family_member_female']+$item['family_member_male']+$item['family_member_others']);?>" />
                            </div>
                            <script>
                                $(document).ready(function(){
                                    $('.family_member').on('input', function()
                                    {
                                        var family_member_total=0;
                                        $('.family_member').each(function()
                                        {
                                            if($(this).val()!='')
                                            {
                                                family_member_total+=parseInt($(this).val())
                                            }
                                        });
                                        $('#family_member_total').val(family_member_total)
                                    })
                                })
                            </script>
                        </th>
                    </tr>
                    <tr>
                        <th><?php echo $this->lang->line('SURVEY_FARMER_TITLE_LAND_SIZE');?></th>
                        <th>
                            <div class="col-md-3">
                                <label for="land_size_cultivable"><?php echo $this->lang->line('SURVEY_FARMER_LAND_SIZE_CULTIVABLE');?></label>
                                <input type="text" class="form-control float_type_positive" id="land_size_cultivable" name="item[land_size_cultivable]" value="<?php echo $item['land_size_cultivable']?>" />
                            </div>
                            <div class="col-md-3">
                                <label for="land_size_residential"><?php echo $this->lang->line('SURVEY_FARMER_LAND_SIZE_RESIDENTIAL');?></label>
                                <input type="text" class="form-control float_type_positive" id="land_size_residential" name="item[land_size_residential]" value="<?php echo $item['land_size_residential']?>" />
                            </div>
                            <div class="col-md-3">
                                <label for="land_size_others"><?php echo $this->lang->line('SURVEY_FARMER_OTHERS');?></label>
                                <input type="text" class="form-control float_type_positive" id="land_size_others" name="item[land_size_others]" value="<?php echo $item['land_size_others']?>" />
                            </div>
                            <div class="col-md-3">
                                <label for="land_size_others_remarks" class="text-danger"><?php echo $this->lang->line('SURVEY_FARMER_OTHERS_PLACEHOLDER');?></label>
                                <input type="text" class="form-control" id="land_size_others_remarks" name="item[land_size_others_remarks]" value="<?php echo $item['land_size_others_remarks']?>" />
                            </div>
                        </th>
                    </tr>
                    <tr>
                        <th><?php echo $this->lang->line('SURVEY_FARMER_TITLE_CULTIVATED_AREA');?></th>
                        <th>
                            <div class="col-md-3">
                                <label for="cultivated_area_vegetables"><?php echo $this->lang->line('SURVEY_FARMER_CULTIVATED_AREA_VEGETABLES');?></label>
                                <input type="text" class="form-control float_type_positive" id="cultivated_area_vegetables" name="item[cultivated_area_vegetables]" value="<?php echo $item['cultivated_area_vegetables']?>" />
                            </div>
                            <div class="col-md-3">
                                <label for="cultivated_area_others"><?php echo $this->lang->line('SURVEY_FARMER_OTHERS');?></label>
                                <input type="text" class="form-control float_type_positive" id="cultivated_area_others" name="item[cultivated_area_others]" value="<?php echo $item['cultivated_area_others']?>" />
                            </div>
                            <div class="col-md-3">
                                <label for="land_size_others_remarks" class="text-danger"><?php echo $this->lang->line('SURVEY_FARMER_OTHERS_PLACEHOLDER');?></label>
                                <input type="text" class="form-control" id="land_size_others_remarks" name="item[cultivated_area_others_remarks]" value="<?php echo $item['cultivated_area_others_remarks']?>" />
                            </div>
                        </th>
                    </tr>
                    <tr>
                        <th><?php echo $this->lang->line('SURVEY_FARMER_TITLE_LAST_YEAR_CULTIVATED')?></th>
                        <th>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="last_year_cultivated_paddy" name="item[last_year_cultivated_paddy]" value="1" <?php if($item['last_year_cultivated_paddy']==1){echo "checked=true";}?> />
                                <label class="form-check-label" for="last_year_cultivated_paddy"><?php echo $this->lang->line('SURVEY_FARMER_LAST_YEAR_CULTIVATED_PADDY')?></label>

                                <input class="form-check-input" type="checkbox" id="last_year_cultivated_jute" name="item[last_year_cultivated_jute]" value="1" <?php if($item['last_year_cultivated_jute']==1){echo "checked=true";}?> />
                                <label class="form-check-label" for="last_year_cultivated_jute"><?php echo $this->lang->line('SURVEY_FARMER_LAST_YEAR_CULTIVATED_JUTE')?></label>

                                <input class="form-check-input" type="checkbox" id="last_year_cultivated_wheat" name="item[last_year_cultivated_wheat]" value="1" <?php if($item['last_year_cultivated_wheat']==1){echo "checked=true";}?> />
                                <label class="form-check-label" for="last_year_cultivated_wheat"><?php echo $this->lang->line('SURVEY_FARMER_LAST_YEAR_CULTIVATED_WHEAT')?></label>

                                <input class="form-check-input" type="checkbox" id="last_year_cultivated_mustard" name="item[last_year_cultivated_mustard]" value="1" <?php if($item['last_year_cultivated_mustard']==1){echo "checked=true";}?> />
                                <label class="form-check-label" for="last_year_cultivated_mustard"><?php echo $this->lang->line('SURVEY_FARMER_LAST_YEAR_CULTIVATED_MUSTARD')?></label>

                                <input class="form-check-input" type="checkbox" id="last_year_cultivated_maize" name="item[last_year_cultivated_maize]" value="1" <?php if($item['last_year_cultivated_maize']==1){echo "checked=true";}?> />
                                <label class="form-check-label" for="last_year_cultivated_maize"><?php echo $this->lang->line('SURVEY_FARMER_LAST_YEAR_CULTIVATED_MAIZE')?></label>
                                <br/>
                                <input class="form-check-input" type="checkbox" id="last_year_cultivated_others" name="item[last_year_cultivated_others]" value="1" <?php if($item['last_year_cultivated_others']==1){echo "checked=true";}?> />
                                <label class="form-check-label" for="last_year_cultivated_others">
                                    <?php echo $this->lang->line('SURVEY_FARMER_OTHERS')?>
                                    <input type="text" class=" form-inline" id="last_year_cultivated_others_remarks" name="item[last_year_cultivated_others_remarks]" value="<?php echo $item['last_year_cultivated_others_remarks']?>" placeholder="<?php echo $this->lang->line('SURVEY_FARMER_OTHERS_PLACEHOLDER')?>"  style="width: 250px;"/>
                                </label>
                            </div>
                        </th>
                    </tr>
                    <tr>
                        <th><?php echo $this->lang->line('SURVEY_FARMER_TITLE_CROPPING_INTENSITY');?></th>
                        <th>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="cropping_intensity_single" name="item[cropping_intensity_single]" value="1" <?php if($item['cropping_intensity_single']==1){echo "checked=true";}?> />
                                <label class="form-check-label" for="cropping_intensity_single"><?php echo $this->lang->line('SURVEY_FARMER_CROPPING_INTENSITY_SINGLE');?></label>

                                <input class="form-check-input" type="checkbox" id="cropping_intensity_double" name="item[cropping_intensity_double]" value="1" <?php if($item['cropping_intensity_double']==1){echo "checked=true";}?> />
                                <label class="form-check-label" for="cropping_intensity_double"><?php echo $this->lang->line('SURVEY_FARMER_CROPPING_INTENSITY_DOUBLE');?></label>

                                <input class="form-check-input" type="checkbox" id="cropping_intensity_triple" name="item[cropping_intensity_triple]" value="1" <?php if($item['cropping_intensity_triple']==1){echo "checked=true";}?> />
                                <label class="form-check-label" for="cropping_intensity_triple"><?php echo $this->lang->line('SURVEY_FARMER_CROPPING_INTENSITY_TRIPLE');?></label>

                                <input class="form-check-input" type="checkbox" id="cropping_intensity_multiple" name="item[cropping_intensity_multiple]" value="1" <?php if($item['cropping_intensity_multiple']==1){echo "checked=true";}?> />
                                <label class="form-check-label" for="cropping_intensity_multiple"><?php echo $this->lang->line('SURVEY_FARMER_CROPPING_INTENSITY_MULTIPLE');?></label>
                            </div>
                        </th>
                    </tr>
                    <tr>
                        <th colspan="2"><?php echo $this->lang->line('SURVEY_FARMER_TITLE_LAST_YEAR_VEGETABLES_CULTIVATION');?></th>
                    </tr>
                    </tbody>
                </table>
                <div class="col-md-12 col-xs-12" style=" overflow: scroll" id="system_add_more_table_container">
                    <table class="table table-bordered table-responsive" style="">
                        <thead>
                        <tr>
                            <th class="text-center" rowspan="2"><?php echo $this->lang->line('SURVEY_FARMER_TITLE_LAST_YEAR_VEGETABLES_VARIETY_NAME');?></th>
                            <th class="text-center" rowspan="2"><?php echo $this->lang->line('SURVEY_FARMER_TITLE_LAST_YEAR_VEGETABLES_AREA_SIZE');?></th>
                            <th class="text-center" rowspan="2"><?php echo $this->lang->line('SURVEY_FARMER_TITLE_LAST_YEAR_VEGETABLES_PRODUCTION_TOTAL_KG');?></th>
                            <th class="text-center" rowspan="2"><?php echo $this->lang->line('SURVEY_FARMER_TITLE_LAST_YEAR_VEGETABLES_COST_TOTAL_PRODUCED');?></th>
                            <th class="text-center" colspan="6"> <?php echo $this->lang->line('SURVEY_FARMER_TITLE_LAST_YEAR_VEGETABLES_PRODUCTION_COST');?></th>
                            <th class="text-center" rowspan="2"><?php echo $this->lang->line('SURVEY_FARMER_TITLE_LAST_YEAR_VEGETABLES_COST_TOTAL');?> </th>
                            <th class="text-center" rowspan="2"> <?php echo $this->lang->line('SURVEY_FARMER_TITLE_LAST_YEAR_VEGETABLES_NET_PROFIT');?></th>
                            <th class="text-center" rowspan="2"> <?php echo $this->lang->line('SURVEY_FARMER_TITLE_LAST_YEAR_VEGETABLES_STATUS');?></th>
                        </tr>
                        <tr>
                            <th class="text-center"><?php echo $this->lang->line('SURVEY_FARMER_TITLE_LAST_YEAR_VEGETABLES_PRODUCTION_COST_LAND_PREPARATION');?></th>
                            <th class="text-center"><?php echo $this->lang->line('SURVEY_FARMER_TITLE_LAST_YEAR_VEGETABLES_PRODUCTION_COST_WAGES');?></th>
                            <th class="text-center"><?php echo $this->lang->line('SURVEY_FARMER_TITLE_LAST_YEAR_VEGETABLES_PRODUCTION_COST_IRRIGATION');?></th>
                            <th class="text-center"><?php echo $this->lang->line('SURVEY_FARMER_TITLE_LAST_YEAR_VEGETABLES_PRODUCTION_COST_FERTILIZERS');?></th>
                            <th class="text-center"><?php echo $this->lang->line('SURVEY_FARMER_TITLE_LAST_YEAR_VEGETABLES_PRODUCTION_COST_PESTICIDE');?></th>
                            <th class="text-center"><?php echo $this->lang->line('SURVEY_FARMER_TITLE_LAST_YEAR_VEGETABLES_PRODUCTION_COST_OTHERS');?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        foreach($items as $info)
                        {
                            ?>
                            <tr>
                                <td><input type="text" class="form-control" name="items[<?php echo $info['id'];?>][vegetable_variety_name]" id="vegetable_variety_name_<?php echo $info['id'];?>" value="<?php echo $info['vegetable_variety_name'];?>"></td>
                                <td><input type="text" class="form-control" name="items[<?php echo $info['id'];?>][area_size]" id="area_size_<?php echo $info['id'];?>" value="<?php echo $info['area_size'];?>"></td>
                                <td><input type="text" class="form-control" name="items[<?php echo $info['id'];?>][production_total_kg]" id="production_total_kg_<?php echo $info['id'];?>" value="<?php echo $info['production_total_kg'];?>"></td>
                                <td><input type="text" class="form-control" name="items[<?php echo $info['id'];?>][cost_total_produced]" id="cost_total_produced_<?php echo $info['id'];?>" value="<?php echo $info['cost_total_produced'];?>"></td>
                                <td><input type="text" class="form-control" name="items[<?php echo $info['id'];?>][production_cost_land_preparation]" id="production_cost_land_preparation_<?php echo $info['id'];?>" value="<?php echo $info['production_cost_land_preparation'];?>"></td>
                                <td><input type="text" class="form-control" name="items[<?php echo $info['id'];?>][production_cost_wages]" id="production_cost_wages_<?php echo $info['id'];?>" value="<?php echo $info['production_cost_wages'];?>"></td>
                                <td><input type="text" class="form-control" name="items[<?php echo $info['id'];?>][production_cost_irrigation]" id="production_cost_irrigation_<?php echo $info['id'];?>" value="<?php echo $info['production_cost_irrigation'];?>"></td>
                                <td><input type="text" class="form-control" name="items[<?php echo $info['id'];?>][production_cost_fertilizers]" id="production_cost_fertilizers_<?php echo $info['id'];?>" value="<?php echo $info['production_cost_fertilizers'];?>"></td>
                                <td><input type="text" class="form-control" name="items[<?php echo $info['id'];?>][production_cost_pesticide]" id="production_cost_pesticide_<?php echo $info['id'];?>" value="<?php echo $info['production_cost_pesticide'];?>"></td>
                                <td><input type="text" class="form-control" name="items[<?php echo $info['id'];?>][production_cost_others]" id="production_cost_others_<?php echo $info['id'];?>" value="<?php echo $info['production_cost_others'];?>"></td>
                                <td><input type="text" class="form-control" name="items[<?php echo $info['id'];?>][cost_total]" id="cost_total_<?php echo $info['id'];?>" value="<?php echo $info['cost_total'];?>"></td>
                                <td><input type="text" class="form-control" name="items[<?php echo $info['id'];?>][net_profit]" id="net_profit_<?php echo $info['id'];?>" value="<?php echo $info['net_profit'];?>"></td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-sm system_button_add_delete"><?php echo $CI->lang->line('DELETE'); ?></button>
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                        <tr class="add_more_tr_last">
                            <td colspan="14">
                                <button type="button" class="btn btn-success btn-sm system_button_add_more pull-right" data-current-id="<?php echo sizeof($items)+1?>">+ যোগ করুন</button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <table class="table table-bordered table-responsive ">
                    <tbody>
                    <tr>
                        <th style="width: 30%"><?php echo $this->lang->line('SURVEY_FARMER_TITLE_HAVE_VEGETABLES_TRAINING');?></th>
                        <th style="width: 70%">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="item[have_vegetables_training]" id="have_vegetables_training" value="1" <?php if($item['have_vegetables_training']==1){echo "checked=true";}?> />
                                <label class="form-check-label" for="have_vegetables_training"><?php echo $this->lang->line('SURVEY_FARMER_YES');?></label>

                                <input class="form-check-input" type="radio" name="item[have_vegetables_training]" id="have_vegetables_training" value="0" <?php if($item['have_vegetables_training']==0){echo "checked=true";}?> />
                                <label class="form-check-label" for="have_vegetables_training"><?php echo $this->lang->line('SURVEY_FARMER_NO');?></label>
                            </div>
                            <hr/>
                            <div class="col-md-4">
                                <label for="need_technical_training_media"><?php echo $this->lang->line('SURVEY_FARMER_HAVE_VEGETABLES_TRAINING_MEDIA');?></label>
                                <input type="text" class="form-control" id="have_vegetables_training_media" name="item[have_vegetables_training_media]" value="<?php echo $item['have_vegetables_training_media']?>" />
                            </div>
                            <div class="col-md-4">
                                <label for="need_technical_training_institute"><?php echo $this->lang->line('SURVEY_FARMER_HAVE_VEGETABLES_TRAINING_INSTITUTE');?></label>
                                <input type="text" class="form-control" id="have_vegetables_training_institute" name="item[have_vegetables_training_institute]" value="<?php echo $item['have_vegetables_training_institute']?>" />
                            </div>
                            <div class="col-md-4">
                                <label for="need_technical_training_subject"><?php echo $this->lang->line('SURVEY_FARMER_HAVE_VEGETABLES_TRAINING_SUBJECT');?></label>
                                <input type="text" class="form-control" id="have_vegetables_training_subject" name="item[have_vegetables_training_subject]" value="<?php echo $item['have_vegetables_training_subject']?>" />
                            </div>
                        </th>
                    </tr>
                    <tr>
                        <th><?php echo $this->lang->line('SURVEY_FARMER_TITLE_NEED_TECHNICAL_KNOWLEDGE_CULTIVATION');?></th>
                        <th>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="item[need_technical_knowledge_cultivation]" id="need_technical_knowledge_cultivation" value="1" <?php if($item['need_technical_knowledge_cultivation']==1){echo "checked=true";}?> />
                                <label class="form-check-label" for="need_technical_knowledge_cultivation"><?php echo $this->lang->line('SURVEY_FARMER_YES');?></label>

                                <input class="form-check-input" type="radio" name="item[need_technical_knowledge_cultivation]" id="need_technical_knowledge_cultivation" value="0" <?php if($item['need_technical_knowledge_cultivation']==0){echo "checked=true";}?> />
                                <label class="form-check-label" for="need_technical_knowledge_cultivation">
                                    <?php echo $this->lang->line('SURVEY_FARMER_NO');?>
                                    <!--<input type="text" class=" form-inline" id="crop_type_preference" name="item[crop_type_preference]" value="<?php /*echo $item['crop_type_preference']*/?>" placeholder="<?php /*echo $this->lang->line('SURVEY_FARMER_NO_PLACEHOLDER');*/?>"  style="width: 250px;"/>-->
                                </label>
                            </div>
                        </th>
                    </tr>
                    <tr>
                        <th><?php echo $this->lang->line('SURVEY_FARMER_TITLE_TECHNICAL_KNOWLEDGE_VEGETABLES_CULTIVATION');?></th>
                        <th>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="technical_knowledge_vegetables_cultivation" name="item[technical_knowledge_vegetables_cultivation]" value="1" <?php if($item['technical_knowledge_vegetables_cultivation']==1){echo "checked=true";}?>>
                                <label class="form-check-label" for="technical_knowledge_vegetables_cultivation"><?php echo $this->lang->line('SURVEY_FARMER_TECHNICAL_KNOWLEDGE_VEGETABLES_CULTIVATION');?></label>

                                <input class="form-check-input" type="checkbox" id="technical_knowledge_quality_seeds" name="item[technical_knowledge_quality_seeds]" value="1" <?php if($item['technical_knowledge_quality_seeds']==1){echo "checked=true";}?>>
                                <label class="form-check-label" for="technical_knowledge_quality_seeds"> <?php echo $this->lang->line('SURVEY_FARMER_TECHNICAL_KNOWLEDGE_QUALITY_SEEDS');?></label>

                                <input class="form-check-input" type="checkbox" id="technical_knowledge_pest_management" name="item[technical_knowledge_pest_management]" value="1" <?php if($item['technical_knowledge_pest_management']==1){echo "checked=true";}?>>
                                <label class="form-check-label" for="technical_knowledge_pest_management"><?php echo $this->lang->line('SURVEY_FARMER_TECHNICAL_KNOWLEDGE_PEST_MANAGEMENT');?></label>
                                <br/>
                                <input class="form-check-input" type="checkbox" id="technical_knowledge_others" name="item[technical_knowledge_others]" value="1" <?php if($item['technical_knowledge_others']==1){echo "checked=true";}?> >
                                <label class="form-check-label" for="technical_knowledge_others">
                                    <?php echo $this->lang->line('SURVEY_FARMER_OTHERS');?>
                                    <input type="text" class=" form-inline" id="technical_knowledge_others_remarks" name="item[technical_knowledge_others_remarks]" value="<?php echo $item['technical_knowledge_others_remarks']?>" placeholder="<?php echo $this->lang->line('SURVEY_FARMER_OTHERS_PLACEHOLDER');?>"  style="width: 250px;"/>
                                </label>
                            </div>
                        </th>
                    </tr>
                    <tr>
                        <th><?php echo $this->lang->line('SURVEY_FARMER_TITLE_SEEDS_COLLECT');?> </th>
                        <th>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="seeds_collect_dealers" name="item[seeds_collect_dealers]" value="1" <?php if($item['seeds_collect_dealers']==1){echo "checked=true";}?>>
                                <label class="form-check-label" for="seeds_collect_dealers"><?php echo $this->lang->line('SURVEY_FARMER_SEEDS_COLLECT_DEALERS');?> </label>

                                <input class="form-check-input" type="checkbox" id="seeds_collect_retailers" name="item[seeds_collect_retailers]" value="1" <?php if($item['seeds_collect_retailers']==1){echo "checked=true";}?>>
                                <label class="form-check-label" for="seeds_collect_retailers"> <?php echo $this->lang->line('SURVEY_FARMER_SEEDS_COLLECT_RETAILERS');?> </label>

                                <input class="form-check-input" type="checkbox" id="seeds_collect_leadfarmers" name="item[seeds_collect_leadfarmers]" value="1" <?php if($item['seeds_collect_leadfarmers']==1){echo "checked=true";}?>>
                                <label class="form-check-label" for="seeds_collect_leadfarmers"><?php echo $this->lang->line('SURVEY_FARMER_SEEDS_COLLECT_LEADFARMERS');?></label>

                                <input class="form-check-input" type="checkbox" id="seeds_collect_hatbazar" name="item[seeds_collect_hatbazar]" value="1" <?php if($item['seeds_collect_hatbazar']==1){echo "checked=true";}?>>
                                <label class="form-check-label" for="seeds_collect_hatbazar"><?php echo $this->lang->line('SURVEY_FARMER_SEEDS_COLLECT_HATBAZAR');?></label>

                                <input class="form-check-input" type="checkbox" id="seeds_collect_ownseeds" name="item[seeds_collect_ownseeds]" value="1" <?php if($item['seeds_collect_ownseeds']==1){echo "checked=true";}?>>
                                <label class="form-check-label" for="seeds_collect_ownseeds"><?php echo $this->lang->line('SURVEY_FARMER_SEEDS_COLLECT_OWNSEEDS');?></label>
                                <br/>
                                <input class="form-check-input" type="checkbox" id="seeds_collect_others" name="item[seeds_collect_others]" value="1" <?php if($item['seeds_collect_others']==1){echo "checked=true";}?>>
                                <label class="form-check-label" for="seeds_collect_others">
                                    <?php echo $this->lang->line('SURVEY_FARMER_OTHERS');?>
                                    <input type="text" class=" form-inline" id="seeds_collect_others_remarks" name="item[seeds_collect_others_remarks]" value="<?php echo $item['seeds_collect_others_remarks']?>" placeholder="<?php echo $this->lang->line('SURVEY_FARMER_OTHERS_PLACEHOLDER');?>"  style="width: 250px;"/>
                                </label>
                            </div>
                        </th>
                    </tr>
                    <tr>
                        <th> <?php echo $this->lang->line('SURVEY_FARMER_TITLE_ENSURE_SEED_QUALITY');?></th>
                        <th>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="ensure_seed_quality_germination" name="item[ensure_seed_quality_germination]" value="1" <?php if($item['ensure_seed_quality_germination']==1){echo "checked=true";}?>>
                                <label class="form-check-label" for="ensure_seed_quality_germination"><?php echo $this->lang->line('SURVEY_FARMER_ENSURE_SEED_QUALITY_GERMINATION');?></label>

                                <input class="form-check-input" type="checkbox" id="ensure_seed_quality_faith" name="item[ensure_seed_quality_faith]" value="1" <?php if($item['ensure_seed_quality_faith']==1){echo "checked=true";}?>>
                                <label class="form-check-label" for="ensure_seed_quality_faith"><?php echo $this->lang->line('SURVEY_FARMER_ENSURE_SEED_QUALITY_FAITH');?></label>

                                <br/>
                                <input class="form-check-input" type="checkbox" id="ensure_seed_quality_others" name="item[ensure_seed_quality_others]" value="1" <?php if($item['ensure_seed_quality_others']==1){echo "checked=true";}?> >
                                <label class="form-check-label" for="ensure_seed_quality_others">
                                    <?php echo $this->lang->line('SURVEY_FARMER_OTHERS');?>
                                    <input type="text" class=" form-inline" id="seeds_collect_others_remarks" name="item[seeds_collect_others_remarks]" value="<?php echo $item['seeds_collect_others_remarks']?>" placeholder="<?php echo $this->lang->line('SURVEY_FARMER_OTHERS_PLACEHOLDER');?>"  style="width: 250px;"/>
                                </label>
                            </div>
                        </th>
                    </tr>
                    <tr>
                        <th><?php echo $this->lang->line('SURVEY_FARMER_TITLE_GOOD_SEED_PURCHASE');?></th>
                        <th>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="item[good_seed_purchase]" id="good_seed_purchase" value="1" <?php if($item['good_seed_purchase']==1){echo "checked=true";}?>>
                                <label class="form-check-label" for="good_seed_purchase"><?php echo $this->lang->line('SURVEY_FARMER_YES');?></label>

                                <input class="form-check-input" type="radio" name="item[good_seed_purchase]" id="good_seed_purchase" value="0" <?php if($item['good_seed_purchase']==0){echo "checked=true";}?>>
                                <label class="form-check-label" for="good_seed_purchase">
                                    <?php echo $this->lang->line('SURVEY_FARMER_NO');?>
                                    <input type="text" class=" form-inline" id="good_seed_purchase_remarks" name="item[good_seed_purchase_remarks]" value="<?php echo $item['good_seed_purchase_remarks']?>" placeholder="<?php echo $this->lang->line('SURVEY_FARMER_NO_PLACEHOLDER');?>"  style="width: 250px;"/>
                                </label>
                            </div>
                        </th>
                    </tr>
                    <tr>
                        <th><?php echo $this->lang->line('SURVEY_FARMER_TITLE_SELL_VEGETABLES_TO');?></th>
                        <th>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="sell_vegetables_to_artodar_paikar" name="item[sell_vegetables_to_artodar_paikar]" value="1" <?php if($item['sell_vegetables_to_artodar_paikar']==1){echo "checked=true";}?>>
                                <label class="form-check-label" for="sell_vegetables_to_artodar_paikar"> <?php echo $this->lang->line('SURVEY_FARMER_SELL_VEGETABLES_TO_ARTODAR_PAIKAR');?></label>

                                <input class="form-check-input" type="checkbox" id="sell_vegetables_to_hatbazar" name="item[sell_vegetables_to_hatbazar]" value="1" <?php if($item['sell_vegetables_to_hatbazar']==1){echo "checked=true";}?>>
                                <label class="form-check-label" for="sell_vegetables_to_hatbazar"><?php echo $this->lang->line('SURVEY_FARMER_SELL_VEGETABLES_TO_HATBAZAR');?></label>

                                <input class="form-check-input" type="checkbox" id="sell_vegetables_in_group" name="item[sell_vegetables_in_group]" value="1" <?php if($item['sell_vegetables_in_group']==1){echo "checked=true";}?>>
                                <label class="form-check-label" for="sell_vegetables_in_group"><?php echo $this->lang->line('SURVEY_FARMER_SELL_VEGETABLES_IN_GROUP');?></label>

                                <br/>
                                <input class="form-check-input" type="checkbox" id="sell_vegetables_others" name="item[sell_vegetables_others]" value="1" <?php if($item['sell_vegetables_others']==1){echo "checked=true";}?> >
                                <label class="form-check-label" for="sell_vegetables_others">
                                    <?php echo $this->lang->line('SURVEY_FARMER_OTHERS');?>
                                    <input type="text" class=" form-inline" id="sell_vegetables_others_remarks" name="item[sell_vegetables_others_remarks]" value="<?php echo $item['sell_vegetables_others_remarks']?>" placeholder="<?php echo $this->lang->line('SURVEY_FARMER_OTHERS_PLACEHOLDER');?>"  style="width: 250px;"/>
                                </label>
                            </div>
                        </th>
                    </tr>
                    <tr>
                        <th><?php echo $this->lang->line('SURVEY_FARMER_TITLE_ADDRESS_SELLING_HATBAZAR');?></th>
                        <th><input type="text" class="form-control" id="address_selling_hatbazar" name="item[address_selling_hatbazar]" value="<?php echo $item['address_selling_hatbazar']?>" /></th>
                    </tr>
                    <tr>
                        <th><?php echo $this->lang->line('SURVEY_FARMER_TITLE_DOES_ARTODAR_PAIKAR');?></th>
                        <th>
                            <div class="form-check form-check-inline">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="item[does_artodar_paikar_helps]" id="does_artodar_paikar_helps1" value="1" <?php if($item['does_artodar_paikar_helps']==1){echo "checked=true";}?>>
                                    <label class="form-check-label" for="does_artodar_paikar_helps1"><?php echo $this->lang->line('SURVEY_FARMER_YES');?></label>

                                    <input class="form-check-input" type="radio" name="item[does_artodar_paikar_helps]" id="does_artodar_paikar_helps" value="0" <?php if($item['does_artodar_paikar_helps']==0){echo "checked=true";}?>>
                                    <label class="form-check-label" for="does_artodar_paikar_helps"><?php echo $this->lang->line('SURVEY_FARMER_NO');?></label>
                                </div>
                            </div>
                        </th>
                    </tr>
                    <tr>
                        <th>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <?php echo $this->lang->line('SURVEY_FARMER_TITLE_DOES_ARTODAR_PAIKAR_HELPS');?>
                        </th>
                        <th>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="does_artodar_paikar_helps_supplying_seeds" name="item[does_artodar_paikar_helps_supplying_seeds]" value="1" <?php if($item['does_artodar_paikar_helps_supplying_seeds']==1){echo "checked=true";}?>>
                                <label class="form-check-label" for="does_artodar_paikar_helps_supplying_seeds"><?php echo $this->lang->line('SURVEY_FARMER_DOES_ARTODAR_PAIKAR_HELPS_SUPPLYING_SEEDS');?></label>

                                <input class="form-check-input" type="checkbox" id="does_artodar_paikar_helps_credit_facilities" name="item[does_artodar_paikar_helps_credit_facilities]" value="1" <?php if($item['does_artodar_paikar_helps_credit_facilities']==1){echo "checked=true";}?>>
                                <label class="form-check-label" for="does_artodar_paikar_helps_credit_facilities"><?php echo $this->lang->line('SURVEY_FARMER_DOES_DOES_ARTODAR_PAIKAR_HELPS_CREDIT_FACILITIES');?></label>

                                <br/>
                                <input class="form-check-input" type="checkbox" id="does_artodar_paikar_helps_others" name="item[does_artodar_paikar_helps_others]" value="1" <?php if($item['does_artodar_paikar_helps_others']==1){echo "checked=true";}?> >
                                <label class="form-check-label" for="does_artodar_paikar_helps_others">
                                    <?php echo $this->lang->line('SURVEY_FARMER_OTHERS');?>
                                    <input type="text" class=" form-inline" id="does_artodar_paikar_helps_remarks" name="item[does_artodar_paikar_helps_remarks]" value="<?php echo $item['does_artodar_paikar_helps_remarks']?>" placeholder="<?php echo $this->lang->line('SURVEY_FARMER_OTHERS_PLACEHOLDER');?>"  style="width: 250px;"/>
                                </label>
                            </div>
                        </th>
                    </tr>
                    <tr>
                        <th><?php echo $this->lang->line('SURVEY_FARMER_TITLE_SEED_FACILITIES');?></th>
                        <th>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="item[seed_facilities]" id="seed_facilities" value="1" <?php if($item['seed_facilities']==1){echo "checked=true";}?>>
                                <label class="form-check-label" for="seed_facilities"><?php echo $this->lang->line('SURVEY_FARMER_YES');?></label>

                                <input class="form-check-input" type="radio" name="item[seed_facilities]" id="seed_facilities" value="0" <?php if($item['seed_facilities']==0){echo "checked=true";}?>>
                                <label class="form-check-label" for="seed_facilities">
                                    <?php echo $this->lang->line('SURVEY_FARMER_NO');?>
                                    <input type="text" class=" form-inline" id="seed_facilities_remarks" name="item[seed_facilities_remarks]" value="<?php echo $item['seed_facilities_remarks']?>" placeholder="<?php echo $this->lang->line('SURVEY_FARMER_NO_PLACEHOLDER');?>"  style="width: 250px;"/>
                                </label>
                            </div>
                        </th>
                    </tr>
                    <tr>
                        <th><?php echo $this->lang->line('SURVEY_FARMER_TITLE_DO_KNOW_ARM');?></th>
                        <th>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="item[do_know_arm]" id="do_know_arm1" value="1" <?php if($item['do_know_arm']==1){echo "checked=true";}?>>
                                <label class="form-check-label" for="do_know_arm1">
                                    <?php echo $this->lang->line('SURVEY_FARMER_YES');?>
                                    <input type="text" class=" form-inline" id="do_know_arm_remarks" name="item[do_know_arm_remarks]" value="<?php echo $item['do_know_arm_remarks']?>" placeholder="<?php echo $this->lang->line('SURVEY_FARMER_DO_KNOW_ARM_REMARKS');?>"  style="width: 250px;"/>
                                </label>

                                <input class="form-check-input" type="radio" name="item[do_know_arm]" id="do_know_arm" value="0" <?php if($item['do_know_arm']==0){echo "checked=true";}?>>
                                <label class="form-check-label" for="do_know_arm"><?php echo $this->lang->line('SURVEY_FARMER_NO');?></label>
                            </div>
                        </th>
                    </tr>
                    <tr>
                        <th colspan="2">
                            <div class="col-md-12">
                                <label for="remarks"><?php echo $this->lang->line('SURVEY_FARMER_TITLE_REMARKS');?></label>
                                <textarea class=" form-control" id="remarks" name="item[remarks]" rows="5" placeholder=""><?php echo $item['remarks'];?></textarea>
                            </div>
                        </th>
                    </tr>
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</form>
<div id="system_content_add_more" style="display:none;">
    <table>
        <tbody>
        <tr>
            <td>
                <input type="text" class="form-control vegetable_variety_name"/>
            </td>
            <td>
                <input type="text" class="form-control area_size"/>
            </td>
            <td>
                <input type="text" class="form-control production_total_kg"/>
            </td>
            <td>
                <input type="text" class="form-control cost_total_produced"/>
            </td>
            <td>
                <input type="text" class="form-control production_cost_land_preparation"/>
            </td>
            <td>
                <input type="text" class="form-control production_cost_wages"/>
            </td>
            <td>
                <input type="text" class="form-control production_cost_irrigation"/>
            </td>
            <td>
                <input type="text" class="form-control production_cost_fertilizers"/>
            </td>
            <td>
                <input type="text" class="form-control production_cost_pesticide"/>
            </td>
            <td>
                <input type="text" class="form-control production_cost_others"/>
            </td>
            <td>
                <input type="text" class="form-control cost_total"/>
            </td>
            <td>
                <input type="text" class="form-control net_profit"/>
            </td>
            <td style="width:1%">
                <button type="button" class="btn btn-danger btn-sm system_button_add_delete"><?php echo $CI->lang->line('DELETE'); ?></button>
            </td>
        </tr>
        </tbody>
    </table>
</div>

<script type="text/javascript">


    jQuery(document).ready(function ($)
    {
        system_preset({controller: '<?php echo $CI->router->class; ?>'});
        system_off_events(); // Triggers
        var system_upazillas = JSON.parse('<?php echo json_encode($upazillas); ?>');
        var system_unions = JSON.parse('<?php echo json_encode($unions); ?>');
        <?php
        if($item['id']>0)
        {
            ?>
            if(<?php echo $item['district_id']?> in system_upazillas)
            {
                $("#upazilla_id").html(get_dropdown_with_select(system_upazillas[<?php echo $item['district_id']?>],<?php echo $item['upazilla_id']?>));
                if(<?php echo $item['upazilla_id']?> in system_unions)
                {
                    $("#union_id").html(get_dropdown_with_select(system_unions[<?php echo $item['upazilla_id']?>],<?php echo $item['union_id']?>));
                }
            }
        <?php
        }
        ?>

        $(document).on("click", ".system_button_add_more", function (event)
        {
            var current_id = parseInt($(this).attr('data-current-id'));
            current_id = current_id + 1;
            $(this).attr('data-current-id', current_id);
            var content_id = '#system_content_add_more table tbody';

            $(content_id+' .vegetable_variety_name').attr('name','items['+current_id+'][vegetable_variety_name]');
            $(content_id+' .vegetable_variety_name').attr('id','vegetable_variety_name_'+current_id);

            $(content_id+' .area_size').attr('name','items['+current_id+'][area_size]');
            $(content_id+' .area_size').attr('id','area_size_'+current_id);

            $(content_id+' .production_total_kg').attr('name','items['+current_id+'][production_total_kg]');
            $(content_id+' .production_total_kg').attr('id','production_total_kg_'+current_id);

            $(content_id+' .cost_total_produced').attr('name','items['+current_id+'][cost_total_produced]');
            $(content_id+' .cost_total_produced').attr('id','cost_total_produced_'+current_id);

            $(content_id+' .production_cost_land_preparation').attr('name','items['+current_id+'][production_cost_land_preparation]');
            $(content_id+' .production_cost_land_preparation').attr('id','production_cost_land_preparation_'+current_id);

            $(content_id+' .production_cost_wages').attr('name','items['+current_id+'][production_cost_wages]');
            $(content_id+' .production_cost_wages').attr('id','production_cost_wages_'+current_id);

            $(content_id+' .production_cost_irrigation').attr('name','items['+current_id+'][production_cost_irrigation]');
            $(content_id+' .production_cost_irrigation').attr('id','production_cost_irrigation_'+current_id);

            $(content_id+' .production_cost_fertilizers').attr('name','items['+current_id+'][production_cost_fertilizers]');
            $(content_id+' .production_cost_fertilizers').attr('id','production_cost_fertilizers_'+current_id);

            $(content_id+' .production_cost_pesticide').attr('name','items['+current_id+'][production_cost_pesticide]');
            $(content_id+' .production_cost_pesticide').attr('id','production_cost_pesticide_'+current_id);

            $(content_id+' .production_cost_others').attr('name','items['+current_id+'][production_cost_others]');
            $(content_id+' .production_cost_others').attr('id','production_cost_others_'+current_id);

            $(content_id+' .cost_total').attr('name','items['+current_id+'][cost_total]');
            $(content_id+' .cost_total').attr('id','cost_total_'+current_id);

            $(content_id+' .net_profit').attr('name','items['+current_id+'][net_profit]');
            $(content_id+' .net_profit').attr('id','net_profit_'+current_id);

            var html = $(content_id).html();
            $("#system_add_more_table_container tbody tr.add_more_tr_last").before(html);
        });

        $(document).on("click", ".system_button_add_delete", function (event) {
            $(this).closest('tr').remove();
        });

        $('.family_member').on('input', function()
        {
            var family_member_total=0;
            $('.family_member').each(function()
            {
                if($(this).val()!='')
                {
                    family_member_total+=parseInt($(this).val())
                }
            });
            $('#family_member_total').val(family_member_total)
        })

        $(document).off("change", "#district_id");
        $(document).on("change", "#district_id", function ()
        {
            $("#upazilla_id").html(get_dropdown_with_select([]));
            $("#union_id").html(get_dropdown_with_select([]));
            var district_id = $(this).val();
            if (district_id > 0)
            {
                //$('#upazilla_id_container').show();
                if (system_upazillas[district_id] !== undefined) {
                    $("#upazilla_id").html(get_dropdown_with_select(system_upazillas[district_id]));
                }
            }
        });
        $(document).off("change", "#upazilla_id");
        $(document).on("change", "#upazilla_id", function ()
        {
            $("#union_id").html(get_dropdown_with_select([]));
            var upazilla_id = $(this).val();
            if (upazilla_id > 0)
            {
                //$('#union_id_container').show();
                if (system_unions[upazilla_id] !== undefined) {
                    $("#union_id").html(get_dropdown_with_select(system_unions[upazilla_id]));
                }
            }
        });
    });


</script>

<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_BACK"),
    'href'=>site_url($CI->controller_url.'/index/list/'.$item['date_visit'])
);
$action_buttons[]=array(
    'type'=>'button',
    'label'=>$CI->lang->line("ACTION_CLEAR"),
    'id'=>'button_action_clear',
    'data-form'=>'#save_form'
);
$CI->load->view('action_buttons',array('action_buttons'=>$action_buttons));
?>
<form id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save');?>" method="post">
    <input type="hidden" id="date_visit" name="item[date_visit]" value="<?php echo $item['date_visit']; ?>" />
    <input type="hidden" id="area_id" name="item[area_id]" value="<?php echo $item['area_id']; ?>" />
    <input type="hidden" id="system_save_new_status" name="system_save_new_status" value="0" />
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
                    <label class=""><a class="external text-danger" data-toggle="collapse" data-target="#collapse3" href="#">+ Location View</a></label>
                </h4>
            </div>
            <div id="collapse3" class="panel-collapse collapse">
                <div class="row show-grid">
                    <div class="col-xs-4">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DIVISION_NAME');?></label>
                    </div>
                    <div class="col-sm-4 col-xs-8">
                        <label class="control-label"><?php echo $item_head['division_name'];?></label>
                    </div>
                </div>
                <div class="row show-grid">
                    <div class="col-xs-4">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ZONE_NAME');?></label>
                    </div>
                    <div class="col-sm-4 col-xs-8">
                        <label class="control-label"><?php echo $item_head['zone_name'];?></label>
                    </div>
                </div>
                <div class="row show-grid">
                    <div class="col-xs-4">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_TERRITORY_NAME');?></label>
                    </div>
                    <div class="col-sm-4 col-xs-8">
                        <label class="control-label"><?php echo $item_head['territory_name'];?></label>
                    </div>
                </div>
                <div class="row show-grid">
                    <div class="col-xs-4">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DISTRICT_NAME');?></label>
                    </div>
                    <div class="col-sm-4 col-xs-8">
                        <label class="control-label"><?php echo $item_head['district_name'];?></label>
                    </div>
                </div>
                <div class="row show-grid">
                    <div class="col-xs-4">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_OUTLET_NAME');?></label>
                    </div>
                    <div class="col-sm-4 col-xs-8">
                        <label class="control-label"><?php echo $item_head['outlet_name'];?></label>
                    </div>
                </div>
                <div class="row show-grid">
                    <div class="col-xs-4">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_AREA_NAME');?></label>
                    </div>
                    <div class="col-sm-4 col-xs-8">
                        <label class="control-label"><?php echo $item_head['area_name'];?></label>
                    </div>
                </div>
                <div class="row show-grid">
                    <div class="col-xs-4">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_AREA_ADDRESS');?></label>
                    </div>
                    <div class="col-sm-4 col-xs-8">
                        <label class="control-label"><?php echo $item_head['area_address'];?></label>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <label class=""><a class="external text-danger" data-toggle="collapse" data-target="#collapse_crop_type" href="#">+ Crop & Type Information</a></label>
                </h4>
            </div>
            <div id="collapse_crop_type" class="panel-collapse collapse">
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
                            $crop_type_name=$serial.'. '.$variety['crop_type_name'].' ('.$variety['crop_name'].')';
                            ?>
                            <div class="col-xs-2">
                                <div class="checkbox">
                                    <label><span class="label label-default" title="<?php echo $crop_type_name;?>"><?php echo $crop_type_name;?></span></label>
                                </div>
                            </div>
                        <?php
                        }
                    }
                    ?>
                </div>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-12">
                <table class="table table-responsive table-bordered">
                    <thead>
                    <tr>
                        <th class="text-center bg-success" colspan="4">
                            Dealer Information
                        </th>
                    </tr>
                    <tr>
                        <th>Dealer Name</th>
                        <th>Previous Activity </th>
                        <th>Description</th>
                        <th>Upload Image (JPG/PNG/GIF) | Max Upload Size: 10MB</th>
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
                            ?>
                            <tr>
                                <td><?php echo $dealer['dealer_name']?></td>
                                <td>
                                    <?php
                                    echo isset($previous_dealers[$dealer['dealer_id']]['description'])?$previous_dealers[$dealer['dealer_id']]['description']:'--';
                                    ?>
                                </td>
                                <td>
                                    <textarea name="dealer_items[<?php echo $dealer['id']?>][description]" class="form-control"><?php echo $dealer['description'] ?></textarea>
                                </td>
                                <td>
                                    <input type="file" id="dealer_file_<?php echo $dealer['id']?>" name="dealer_file_<?php echo $dealer['id']?>" class="browse_button" data-preview-container="#preview_dealer_img_<?php echo $dealer['id']?>" data-preview-width="300">
                                    <div class="preview_dealer_img" id="preview_dealer_img_<?php echo $dealer['id']?>">
                                        <a href="<?php echo $CI->config->item('system_base_url_growing_are_visit').$dealer['image_location']; ?>" class="external" target="_blank">
                                            <img style="max-width: 250px;" src="<?php echo $CI->config->item('system_base_url_growing_are_visit').$dealer['image_location']; ?>" alt="<?php echo $dealer['image_name']; ?>">
                                        </a>
                                    </div>
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
                            Lead Farmer Information
                        </th>
                    </tr>
                    <tr>
                        <th>Lead Farmer Name</th>
                        <th>Previous Activity </th>
                        <th>Description</th>
                        <th>Upload Image (JPG/PNG/GIF) | Max Upload Size: 10MB</th>
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
                            ?>
                            <tr>
                                <td><?php echo $farmer['lead_farmers_name']?></td>
                                <td>
                                    <?php
                                    echo isset($previous_farmers[$farmer['farmer_id']]['description'])?$previous_farmers[$farmer['farmer_id']]['description']:'--';
                                    ?>
                                </td>
                                <td>
                                    <textarea name="farmer_items[<?php echo $farmer['id']?>][description]" class="form-control"><?php echo $farmer['description'] ?></textarea>
                                </td>
                                <td>
                                    <input type="file" id="farmer_file_<?php echo $farmer['id']?>" name="farmer_file_<?php echo $farmer['id']?>" class="browse_button" data-preview-container="#preview_farmer_img_<?php echo $farmer['id']?>" data-preview-width="300" style="float: left">
                                    <div class="preview_farmer_img" id="preview_farmer_img_<?php echo $farmer['id']?>">
                                        <a href="<?php echo $CI->config->item('system_base_url_growing_are_visit').$farmer['image_location']; ?>" class="external" target="_blank">
                                            <img style="max-width: 250px;" src="<?php echo $CI->config->item('system_base_url_growing_are_visit').$farmer['image_location']; ?>" alt="<?php echo $farmer['image_name']; ?>">
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php
                        }
                    }
                    ?>
                    </tbody>
                </table>
            </div>
            <div class="col-xs-12">
                <table class="table table-responsive table-bordered">

                </table>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Other Information </label>
            </div>
            <div class="col-xs-4">
                <textarea name="item[other_info]" class="form-control"><?php echo $item['other_info'] ?></textarea>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Remarks</label>
            </div>
            <div class="col-xs-4">
                <textarea name="item[remarks]" class="form-control"><?php echo $item['remarks'] ?></textarea>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">

            </div>
            <div class="col-sm-4 col-xs-4">
                <div class="action_button pull-right">
                    <button id="button_action_save" type="button" class="btn" data-form="#save_form" data-message-confirm="Are You Sure?">Save</button>
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
        $(":file").filestyle({input: false,buttonText: "<?php echo $CI->lang->line('UPLOAD');?>", buttonName: "btn-danger"});
    });
</script>

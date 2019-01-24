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
/*echo "<pre>";
print_r($previous_farmers);
echo "</pre>";*/

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
                        <label class=""><a class="external text-danger" data-toggle="collapse" data-target="#collapse_previous_visit_<?php echo $previous_visit['id']?>" href="#">+ <?php echo System_helper::display_date($previous_visit['date_visit'])?></a></label>
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
                            if(!(isset($previous_farmers[$previous_visit['id']]) && sizeof($previous_farmers[$previous_visit['id']])>0))
                            {
                                ?>
                                <tr>
                                    <td colspan="21" class="text-center bg-danger text-danger"><strong>There is no lead farmer setup.</strong></td>
                                </tr>
                            <?php
                            }
                            else
                            {
                                foreach($previous_farmers[$previous_visit['id']] as $previous_farmer)
                                {
                                    if($previous_farmer['image_location'])
                                    {
                                        $farmer_img=$CI->config->item('system_base_url_picture').$previous_farmer['image_location'];
                                    }
                                    else
                                    {
                                        $farmer_img=$CI->config->item('system_base_url_picture').'images/no_image.jpg';
                                    }
                                    ?>
                                    <tr>
                                        <td style="width: 200px;"><?php echo $previous_farmer['lead_farmers_name']?></td>
                                        <td style="width: 800px;">
                                            <?php
                                            echo nl2br($previous_farmer['description']);
                                            ?>
                                        </td>
                                        <td>
                                            <a href="<?php echo $farmer_img; ?>" class="external" target="_blank">
                                                <img style="max-width: 150px;" src="<?php echo $farmer_img; ?>" alt="Lead Farmer Visit Picture">
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
                            if(!(isset($previous_dealers[$previous_visit['id']]) && sizeof($previous_dealers[$previous_visit['id']])>0))
                            {
                                ?>
                                <tr>
                                    <td colspan="21" class="text-center bg-danger text-danger"><strong>There is no dealer setup.</strong></td>
                                </tr>
                            <?php
                            }
                            else
                            {
                                foreach($previous_dealers[$previous_visit['id']] as $previous_dealer)
                                {
                                    if($previous_dealer['image_location'])
                                    {
                                        $farmer_img=$CI->config->item('system_base_url_picture').$previous_dealer['image_location'];
                                    }
                                    else
                                    {
                                        $farmer_img=$CI->config->item('system_base_url_picture').'images/no_image.jpg';
                                    }
                                    ?>
                                    <tr>
                                        <td style="width: 200px;"><?php echo $previous_dealer['dealer_name']?></td>
                                        <td style="width: 800px;">
                                            <?php
                                            echo nl2br($previous_dealer['description']);
                                            ?>
                                        </td>
                                        <td>
                                            <a href="<?php echo $farmer_img; ?>" class="external" target="_blank">
                                                <img style="max-width: 150px;" src="<?php echo $farmer_img; ?>" alt="Lead Farmer Visit Picture">
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
                    <label class=""><a class="external text-danger" data-toggle="collapse" data-target="#collapse_previous_visit_<?php echo $item['area_id']?>" href="#">+ <?php echo System_helper::display_date($item['date_visit'])?> (Current Visit)</a></label>
                </h4>
            </div>
            <div id="collapse_previous_visit_<?php echo $item['area_id']?>" class="panel-collapse collapse in">
                <div class="row show-grid">
                    <div class="col-xs-12" style="overflow-x: auto">
                        <br/>
                        <table class="table table-responsive table-bordered ">
                            <thead>
                            <tr>
                                <th class="text-center bg-success" colspan="4">
                                    Lead Farmer Information
                                </th>
                            </tr>
                            <tr>
                                <th>Lead Farmer Name</th>
                                <th>Description</th>
                                <th>Upload Image <small class="text-danger"><i>(Note: Landscape image upload)</i></small></th>
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
                                    $lead_farmer_name=$farmer['lead_farmers_name'];
                                    $lead_farmer_description='';
                                    $farmer_img_path=$CI->config->item('system_base_url_picture').'images/no_image.jpg';
                                    $farmer_img_name=$CI->config->item('system_base_url_picture').'images/no_image.jpg';
                                    if(isset($edit_farmers[$farmer['id']]))
                                    {
                                        $lead_farmer_name=$edit_farmers[$farmer['id']]['lead_farmers_name'];
                                        $lead_farmer_description=$edit_farmers[$farmer['id']]['description'];
                                        if($edit_farmers[$farmer['id']]['image_location'])
                                        {
                                            $farmer_img_path=$CI->config->item('system_base_url_picture').$edit_farmers[$farmer['id']]['image_location'];
                                            $farmer_img_name=$edit_farmers[$farmer['id']]['image_name'];
                                        }
                                    }
                                    ?>
                                    <tr>
                                        <td style="width: 200px;"><?php echo $lead_farmer_name; ?></td>
                                        <td style="width: 800px;">
                                            <textarea name="farmer_items[<?php echo $farmer['id']?>][description]" class="form-control"><?php echo $lead_farmer_description; ?></textarea>
                                        </td>
                                        <td>
                                            <div class="preview_farmer_img" id="preview_farmer_img_<?php echo $farmer['id']?>" style="float: left; margin-right: 10px;">
                                                <a href="<?php echo $farmer_img_path; ?>" class="external" target="_blank">
                                                    <img style="max-width: 150px;" src="<?php echo $farmer_img_path; ?>" alt="<?php echo $farmer_img_name; ?>">
                                                </a>
                                            </div>
                                            <input type="file" id="farmer_file_<?php echo $farmer['id']?>" name="farmer_file_<?php echo $farmer['id']?>" class="browse_button" data-preview-container="#preview_farmer_img_<?php echo $farmer['id']?>" data-preview-width="150" style="float: left" />
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
                                <th>Upload Image <small class="text-danger"><i>(Note: Landscape image upload)</i></small></th>
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
                                    $lead_dealer_name=$dealer['dealer_name'];
                                    $lead_dealer_description='';
                                    $dealer_img_path=$CI->config->item('system_base_url_picture').'images/no_image.jpg';
                                    $dealer_img_name=$CI->config->item('system_base_url_picture').'images/no_image.jpg';
                                    if(isset($edit_dealers[$dealer['id']]))
                                    {
                                        $lead_dealer_name=$edit_dealers[$dealer['id']]['dealer_name'];
                                        $lead_dealer_description=$edit_dealers[$dealer['id']]['description'];
                                        if($edit_dealers[$dealer['id']]['image_location'])
                                        {
                                            $dealer_img_path=$CI->config->item('system_base_url_picture').$edit_dealers[$dealer['id']]['image_location'];
                                            $dealer_img_name=$edit_dealers[$dealer['id']]['image_name'];
                                        }
                                    }
                                    ?>
                                    <tr>
                                        <td style="width: 200px;"><?php echo $lead_dealer_name?></td>
                                        <td style="width: 800px;">
                                            <textarea name="dealer_items[<?php echo $dealer['id']?>][description]" class="form-control"><?php echo $lead_dealer_description ?></textarea>
                                        </td>
                                        <td>
                                            <div class="preview_dealer_img" id="preview_dealer_img_<?php echo $dealer['id']?>" style="float: left; margin-right: 10px;">
                                                <a href="<?php echo $dealer_img_path; ?>" class="external" target="_blank">
                                                    <img style="max-width: 150px;" src="<?php echo $dealer_img_path; ?>" alt="<?php echo $dealer_img_name; ?>" />
                                                </a>
                                            </div>
                                            <input type="file" id="dealer_file_<?php echo $dealer['id']?>" name="dealer_file_<?php echo $dealer['id']?>" class="browse_button" data-preview-container="#preview_dealer_img_<?php echo $dealer['id']?>" data-preview-width="150" />
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
                        <label class="control-label pull-right">Others Activities </label>
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

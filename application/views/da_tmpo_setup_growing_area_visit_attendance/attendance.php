<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_BACK"),
    'href'=>site_url($CI->controller_url)
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
    <input type="hidden" id="id" name="id" value="<?php echo $item_head['id']; ?>" />
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
                            if($dealer['image_location'])
                            {
                                $dealer_img=$CI->config->item('system_base_url_growing_are_visit').$dealer['image_location'];
                            }
                            else
                            {
                                $dealer_img=$CI->config->item('system_base_url_growing_are_visit').'images/no_image.jpg';
                            }
                            ?>
                            <tr>
                                <td><?php echo $dealer['dealer_name']?></td>
                                <td>
                                    <?php
                                    echo isset($previous_dealers[$dealer['dealer_id']]['description'])?$previous_dealers[$dealer['dealer_id']]['description']:'--';
                                    ?>
                                </td>
                                <td>
                                    <?php echo $dealer['description'] ?>
                                </td>
                                <td>
                                    <a href="<?php echo $dealer_img; ?>" class="external" target="_blank">
                                        <img style="max-width: 250px;" src="<?php echo $dealer_img; ?>" alt="<?php echo $dealer['image_name']; ?>">
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
                            if($farmer['image_location'])
                            {
                                $farmer_img=$CI->config->item('system_base_url_growing_are_visit').$farmer['image_location'];
                            }
                            else
                            {
                                $farmer_img=$CI->config->item('system_base_url_growing_are_visit').'images/no_image.jpg';
                            }
                            ?>
                            <tr>
                                <td><?php echo $farmer['lead_farmers_name']?></td>
                                <td>
                                    <?php
                                    echo isset($previous_farmers[$farmer['farmer_id']]['description'])?$previous_farmers[$farmer['farmer_id']]['description']:'--';
                                    ?>
                                </td>
                                <td>
                                    <?php echo $farmer['description'] ?>
                                </td>
                                <td>
                                    <a href="<?php echo $farmer_img; ?>" class="external" target="_blank">
                                        <img style="max-width: 250px;" src="<?php echo $farmer_img; ?>" alt="<?php echo $farmer['image_name']; ?>">
                                    </a>
                                </td>
                            </tr>
                        <?php
                        }
                    }
                    ?>
                    <tr><td colspan="21">&nbsp;</td></tr>
                    <?php
                    if($item_head['other_info'])
                    {
                        ?>
                    <tr>
                        <td><strong>Other Information</strong></td>
                        <td colspan="21"><?php echo $item_head['other_info'] ?></td>
                    </tr>
                    <?php
                    }
                    ?>
                    <?php
                    if($item_head['remarks'])
                    {
                        ?>
                    <tr>
                        <td><strong>Remarks</strong></td>
                        <td colspan="21"><?php echo $item_head['remarks'] ?></td>
                    </tr>
                    <?php
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
                <label class="control-label pull-right">Remarks for attendance</label>
            </div>
            <div class="col-xs-4">
                <textarea name="item[remarks_attendance]" class="form-control"></textarea>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Attendance<span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="status_attendance" class="form-control" name="item[status_attendance]">
                    <option value=""><?php echo $CI->lang->line('SELECT');?></option>
                    <option value="<?php echo $this->config->item('system_status_present')?>"><?php echo $this->config->item('system_status_present')?></option>
                    <option value="<?php echo $this->config->item('system_status_absent')?>"><?php echo $this->config->item('system_status_absent')?></option>
                    <option value="<?php echo $this->config->item('system_status_cl')?>"><?php echo $this->config->item('system_status_cl')?></option>
                </select>
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

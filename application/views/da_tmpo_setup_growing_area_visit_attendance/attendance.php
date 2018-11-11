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
                                        $farmer_img=$CI->config->item('system_base_url_picture').$farmer['image_location'];
                                    }
                                    else
                                    {
                                        $farmer_img=$CI->config->item('system_base_url_picture').'images/no_image.jpg';
                                    }
                                    ?>
                                    <tr>
                                        <td style="width: 200px;"><?php echo $farmer['lead_farmers_name']?></td>
                                        <td style="width: 800px;">
                                            <?php
                                            echo isset($previous_farmers[$previous_visit['id']][$farmer['farmer_id']]['description'])?nl2br($previous_farmers[$previous_visit['id']][$farmer['farmer_id']]['description']):'--';
                                            ?>
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
                                        $dealer_img=$CI->config->item('system_base_url_picture').$dealer['image_location'];
                                    }
                                    else
                                    {
                                        $dealer_img=$CI->config->item('system_base_url_picture').'images/no_image.jpg';
                                    }
                                    ?>
                                    <tr>
                                        <td><?php echo $dealer['dealer_name']?></td>
                                        <td>
                                            <?php
                                            echo isset($previous_dealers[$previous_visit['id']][$dealer['dealer_id']]['description'])?nl2br($previous_dealers[$previous_visit['id']][$dealer['dealer_id']]['description']):'--';
                                            ?>
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
                    <label class=""><a class="external text-danger" data-toggle="collapse" data-target="#collapse_previous_visit_<?php echo $item_head['area_id']?>" href="#">+ <?php echo System_helper::display_date($item_head['date_visit'])?> (Current Visit)</a></label>
                </h4>
            </div>
            <div id="collapse_previous_visit_<?php echo $item_head['area_id']?>" class="panel-collapse collapse in">
                <br/>
                <div class="row show-grid">
                    <div class="col-xs-12">
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
                                <th>Attachment</th>
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
                                        $farmer_img=$CI->config->item('system_base_url_picture').$farmer['image_location'];
                                    }
                                    else
                                    {
                                        $farmer_img=$CI->config->item('system_base_url_picture').'images/no_image.jpg';
                                    }
                                    ?>
                                    <tr>
                                        <td><?php echo $farmer['lead_farmers_name']?></td>
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
                                <th>Attachment</th>
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
                                        $dealer_img=$CI->config->item('system_base_url_picture').$dealer['image_location'];
                                    }
                                    else
                                    {
                                        $dealer_img=$CI->config->item('system_base_url_picture').'images/no_image.jpg';
                                    }
                                    ?>
                                    <tr>
                                        <td><?php echo $dealer['dealer_name']?></td>
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
                            <tr><td colspan="21">&nbsp;</td></tr>
                            <?php
                            if($item_head['other_info'])
                            {
                                ?>
                                <tr>
                                    <td><strong>Others Activities</strong></td>
                                    <td colspan="21"><?php echo nl2br($item_head['other_info']) ?></td>
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
                                    <td colspan="21"><?php echo nl2br($item_head['remarks']) ?></td>
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
                            <option value="<?php echo $CI->config->item('system_status_present')?>"><?php echo $CI->config->item('system_status_present')?></option>
                            <option value="<?php echo $CI->config->item('system_status_absent')?>"><?php echo $CI->config->item('system_status_absent')?></option>
                            <option value="<?php echo $CI->config->item('system_status_cl')?>"><?php echo $CI->config->item('system_status_cl')?></option>
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

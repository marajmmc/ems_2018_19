<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_BACK"),
    'href'=>site_url($CI->controller_url.'/index/list_lead_farmer/'.$item_head['area_id'])
);
$action_buttons[]=array(
    'type'=>'button',
    'label'=>$CI->lang->line("ACTION_CLEAR"),
    'id'=>'button_action_clear',
    'data-form'=>'#save_form'
);
$CI->load->view('action_buttons',array('action_buttons'=>$action_buttons));
?>
<form id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save_lead_farmer');?>" method="post">
    <input type="hidden" id="area_id" name="item[area_id]" value="<?php echo $item_head['area_id']; ?>" />
    <input type="hidden" id="id" name="id" value="<?php //echo $item['id']; ?>" />
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

        <div class="row show-grid">
            <div class="col-xs-12">
                <table class="table table-responsive table-bordered">
                    <thead>
                    <tr>
                        <th class="text-center bg-success" colspan="3">
                            Dealer Information
                        </th>
                    </tr>
                    <tr>
                        <th>Dealer Name</th>
                        <th>Description</th>
                        <th>Upload Image (JPG/PNG/GIF) | Max Upload Size: 10MB</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach($dealers as $dealer)
                    {
                        ?>
                    <tr>
                        <td><?php echo $dealer['dealer_name']?></td>
                        <td>
                            <textarea name="dealers[<?php echo $dealer['id']?>][description]" class="form-control"><?php //echo $dealer['description'] ?></textarea>
                        </td>
                        <td>
                            <input type="file"/>
                        </td>
                    </tr>
                    <?php
                    }
                    ?>
                    </tbody>
                    <thead>
                    <tr>
                        <th class="text-center bg-success" colspan="3">
                            Lead Farmer Information
                        </th>
                    </tr>
                    <tr>
                        <th>Lead Farmer Name</th>
                        <th>Description</th>
                        <th>Upload Image (JPG/PNG/GIF) | Max Upload Size: 10MB</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach($farmers as $farmer)
                    {
                        ?>
                        <tr>
                            <td><?php echo $farmer['name']?></td>
                            <td>
                                <textarea name="farmers[<?php echo $farmer['id']?>][description]" class="form-control"><?php //echo $dealer['description'] ?></textarea>
                            </td>
                            <td>
                                <input type="file"/>
                            </td>
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
                <label class="control-label pull-right">Other Information<span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-xs-4">
                <textarea name="item[address]" class="form-control"><?php echo $item['other_information'] ?></textarea>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Other Information</label>
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
                    <button id="button_action_save" type="button" class="btn" data-form="#save_form" data-message-confirm="Are You Sure HQ to outlet transfer delivery done?">Save</button>
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
    });
</script>

<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_BACK"),
    'href'=>site_url($CI->controller_url.'/index/area_list/'.$item_head['id'])
);
$action_buttons[]=array(
    'type'=>'button',
    'label'=>$CI->lang->line("ACTION_SAVE"),
    'id'=>'button_action_save',
    'data-form'=>'#save_form'
);
$action_buttons[]=array(
    'type'=>'button',
    'label'=>$CI->lang->line("ACTION_SAVE_NEW"),
    'id'=>'button_action_save_new',
    'data-form'=>'#save_form'
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
                <label class="control-label"><?php echo $item_head['outlet'];?></label>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-3">&nbsp;</div>
            <div class="col-sm-4 col-xs-8">
                <table class="table table-bordered table-responsive">
                    <thead>
                    <tr>
                        <th colspan="4" class="bg-success text-center">Weekly Visit Schedule Setup</th>
                    </tr>
                    <tr>
                        <th colspan="2" class="text-center">Odd</th>
                        <th colspan="2" class="text-center">Even</th>
                    </tr>
                    <tr>
                        <th>Day</th>
                        <th>Area</th>
                        <th>Day</th>
                        <th>Area</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    for($day=6; $day<13; $day++)
                    {
                        ?>
                        <tr>
                            <td><?php echo date('l',259200+($day%7)*86400);?></td>
                            <td>
                                <select name="" id="" class="form-control">
                                    <option value="">Select</option>
                                    <?php
                                    foreach($areas as $area)
                                    {
                                        ?>
                                        <option value="<?php echo $area['id']?>"><?php echo $area['name']?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                            </td>
                            <td><?php echo date('l',259200+($day%7)*86400);?></td>
                            <td>
                                <select name="" id="" class="form-control">
                                    <option value="">Select</option>
                                    <?php
                                    foreach($areas as $area)
                                    {
                                        ?>
                                        <option value="<?php echo $area['id']?>"><?php echo $area['name']?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                    </tbody>
                </table>
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

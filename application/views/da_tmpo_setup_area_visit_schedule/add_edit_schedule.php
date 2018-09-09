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
    'label'=>$CI->lang->line("ACTION_SAVE"),
    'id'=>'button_action_save',
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
<form id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save_schedule');?>" method="post">
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
                        <label class="control-label"><?php echo $item_head['outlet'];?></label>
                    </div>
                </div>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-3">&nbsp;</div>
            <div class="col-sm-4 col-xs-8">
                <table class="table table-bordered table-responsive">
                    <thead>
                    <tr>
                        <th colspan="3" class="bg-success text-center">Weekly Visit Schedule Setup</th>
                    </tr>
                    <tr>
                        <th>Day</th>
                        <th>Odd</th>
                        <th>Even</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    for($i=1;$i<8;$i++)
                    //for($day_key=0; $day_key<7; $day_key++)
                    {
                        if($i==7)
                        {
                            $day_key=0;
                        }
                        else
                        {
                            $day_key=$i;
                        }
                        ?>
                        <tr>
                            <td><?php echo date('l',($day_key+3)*86400);?> </td>
                            <td>
                                <input type="hidden" name="items[<?php echo $day_key;?>][id]" id="" value="<?php echo isset($items[$day_key]['id'])?$items[$day_key]['id']:0;?>" />
                                <select name="items[<?php echo $day_key;?>][area_id_odd]" id="" class="form-control">
                                    <option value="">Select</option>
                                    <?php
                                    foreach($areas as $area)
                                    {
                                        ?>
                                        <option value="<?php echo $area['id']?>" <?php if(isset($items[$day_key]['area_id_odd']) && $items[$day_key]['area_id_odd']==$area['id']){echo "selected='selected'";}?>><?php echo $area['name']?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                            </td>
                            <td>
                                <select name="items[<?php echo $day_key;?>][area_id_even]" id="" class="form-control">
                                    <option value="">Select</option>
                                    <?php
                                    foreach($areas as $area)
                                    {
                                        ?>
                                        <option value="<?php echo $area['id']?>" <?php if(isset($items[$day_key]['area_id_even']) && $items[$day_key]['area_id_even']==$area['id']){echo "selected='selected'";}?>><?php echo $area['name']?></option>
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

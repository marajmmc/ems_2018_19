<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI =& get_instance();

$action_buttons = array();
$action_buttons[] = array(
    'label' => $CI->lang->line("ACTION_BACK"),
    'href' => site_url($CI->controller_url)
);
if (isset($CI->permissions['action4']) && ($CI->permissions['action4'] == 1))
{
    $action_buttons[] = array(
        'type' => 'button',
        'label' => $CI->lang->line("ACTION_PRINT"),
        'onClick' => "window.print()"
    );
}
$CI->load->view('action_buttons', array('action_buttons' => $action_buttons));
?>
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
                <label class=""><a class="external text-danger" data-toggle="collapse" data-target="#collapse_basic_info" href="#">+ Field Visit Setup Information</a></label>
            </h4>
        </div>
        <div id="collapse_basic_info" class="panel-collapse collapse">
            <table class="table table-bordered table-responsive system_table_details_view">
                <tbody>
                <tr>
                    <td class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_YEAR');?></label></td>
                    <td class="header_value"><label class="control-label"><?php echo $item['year'];?></label></td>
                    <td class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DIVISION_NAME');?></label></td>
                    <td class="header_value"><label class="control-label"><?php echo $item['division_name'];?></label></td>
                </tr>
                <tr>
                    <td class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_SEASON');?></label></td>
                    <td class="header_value"><label class="control-label"><?php echo $item['season'];?></label></td>
                    <td class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ZONE_NAME');?></label></td>
                    <td class="header_value"><label class="control-label"><?php echo $item['zone_name'];?></label></td>
                </tr>
                <tr>
                    <td class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_NAME');?></label></td>
                    <td class="header_value"><label class="control-label"><?php echo $item['crop_name'];?></label></td>
                    <td class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_TERRITORY_NAME');?></label></td>
                    <td class="header_value"><label class="control-label"><?php echo $item['territory_name'];?></label></td>
                </tr>
                <tr>
                    <td class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_TYPE_NAME');?></label></td>
                    <td class="header_value"><label class="control-label"><?php echo $item['crop_type_name'];?></label></td>
                    <td class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DISTRICT_NAME');?></label></td>
                    <td class="header_value"><label class="control-label"><?php echo $item['district_name'];?></label></td>
                </tr>
                <tr>
                    <td class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_VARIETY_NAME');?></label></td>
                    <td class="header_value">
                        <?php
                        foreach($previous_varieties as $variety)
                        {
                            ?>
                            <div class="">
                                <label><?php  echo $variety['variety_name'].' ('.$variety['whose'].')';?></label>
                            </div>
                        <?php
                        }
                        ?>
                    </td>
                    <td colspan="2">&nbsp;</td>
                </tr>
                <tr>
                    <td class="widget-header header_caption"><label class="control-label pull-right">Farmer's Name</label></td>
                    <td class="header_value"><label class="control-label"><?php echo $item['name'];?></label></td>
                    <td class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_UPAZILLA_NAME');?></label></td>
                    <td class="header_value"><label class="control-label"><?php echo $item['upazilla_name'];?></label></td>
                </tr>
                <tr>
                    <td class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ADDRESS');?></label></td>
                    <td class="header_value"><label class="control-label"><?php echo $item['address'];?></label></td>
                    <td colspan="2">&nbsp;</td>
                </tr>
                <tr>
                    <td class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CONTACT_NO');?></label></td>
                    <td class="header_value"><label class="control-label"><?php echo $item['contact_no'];?></label></td>
                    <td colspan="2">&nbsp;</td>
                </tr>
                <tr>
                    <td class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_SOWING');?></label></td>
                    <td class="header_value"><label class="control-label"><?php echo System_helper::display_date($item['date_sowing']);?></label></td>
                    <td colspan="2">&nbsp;</td>
                </tr>
                <tr>
                    <td class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_TRANSPLANT');?></label></td>
                    <td class="header_value"><label class="control-label"><?php echo System_helper::display_date($item['date_transplant']);?></label></td>
                    <td colspan="2">&nbsp;</td>
                </tr>
                <tr>
                    <td class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_NUM_VISITS');?></label></td>
                    <td class="header_value"><label class="control-label"><?php echo $item['num_visits'];?></label></td>
                    <td colspan="2">&nbsp;</td>
                </tr>
                <tr>
                    <td class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_INTERVAL');?></label></td>
                    <td class="header_value"><label class="control-label"><?php echo $item['interval'];?></label></td>
                    <td colspan="2">&nbsp;</td>
                </tr>

                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="panel-group" id="accordion">
<div class="panel panel-default">
    <div class="panel-heading">
        <h4 class="panel-title">
            <a class="accordion-toggle external" data-toggle="collapse"  data-target="#collapse_visits_picture" href="#">Visit Picture and remarks</a>
        </h4>
    </div>
    <div id="collapse_visits_picture" class="panel-collapse collapse in">
        <?php
        for($i=1;$i<=$item['num_visits'];$i++)
        {
            ?>
            <div class="row show-grid">
                <div class="col-xs-4">
                    <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE');?></label>
                </div>
                <div class="col-xs-4">
                    <label class="form-control" style="background-color: #F5F5F5;"><?php echo System_helper::display_date($item['date_sowing']+24*3600*$i*$item['interval']); ?></label>
                </div>
            </div>
            <?php
            if(isset($visits_picture[$i]))
            {
                ?>
                <div style="overflow-x: auto;" class="row show-grid">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th style="min-width: 150px;"><?php echo $CI->lang->line('LABEL_VARIETY_NAME'); ?></th>
                            <th style="min-width: 250px;">day - <?php echo $i;?> Plot picture</th>
                            <th style="min-width: 250px;">day - <?php echo $i;?> Plant Picture</th>
                            <th style="min-width: 150px;"><?php echo $this->lang->line('LABEL_REMARKS');?></th>
                            <th style="min-width: 150px;"><?php echo $this->lang->line('LABEL_FEEDBACK');?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        foreach($previous_varieties as $variety)
                        {
                            ?>
                            <tr>
                                <td><?php echo $variety['variety_name']; ?></td>
                                <td>
                                    <?php
                                    $image_plot_location='images/no_image.jpg';
                                    if(isset($visits_picture[$i][$variety['variety_id']]['image_plot_location'])&&strlen($visits_picture[$i][$variety['variety_id']]['image_plot_location'])>0)
                                    {
                                        $image_plot_location=$visits_picture[$i][$variety['variety_id']]['image_plot_location'];
                                    }
                                    ?>
                                    <img style="max-width: 250px;" src="<?php echo $CI->config->item('system_base_url_ft_field_visit').$image_plot_location; ?>">
                                </td>
                                <td>
                                    <?php
                                    $image_plant_location='images/no_image.jpg';
                                    if(isset($visits_picture[$i][$variety['variety_id']]['image_plant_location'])&&strlen($visits_picture[$i][$variety['variety_id']]['image_plant_location'])>0)
                                    {
                                        $image_plant_location=$visits_picture[$i][$variety['variety_id']]['image_plant_location'];
                                    }
                                    ?>
                                    <img style="max-width: 250px;" src="<?php echo $CI->config->item('system_base_url_ft_field_visit').$image_plant_location; ?>">
                                </td>
                                <td>
                                    <?php
                                    $text='';
                                    if(isset($visits_picture[$i][$variety['variety_id']]))
                                    {
                                        $text.='<b>Entry By</b>:'.$users[$visits_picture[$i][$variety['variety_id']]['user_created']]['name'];
                                        $text.='<br><b>Entry Time</b>:'.System_helper::display_date_time($visits_picture[$i][$variety['variety_id']]['date_created']);
                                        $text.='<br><b>Remarks</b>:<br>'.nl2br($visits_picture[$i][$variety['variety_id']]['remarks']);
                                    }
                                    echo $text;
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    $text='';
                                    if((isset($visits_picture[$i][$variety['variety_id']]['user_feedback']))&&(($visits_picture[$i][$variety['variety_id']]['user_feedback'])>0))
                                    {
                                        $text.='<b>Entry By</b>:'.$users[$visits_picture[$i][$variety['variety_id']]['user_feedback']]['name'];
                                        $text.='<br><b>Entry Time</b>:'.System_helper::display_date_time($visits_picture[$i][$variety['variety_id']]['date_feedback']);
                                        $text.='<br><b>Feedback</b>:<br>'.nl2br($visits_picture[$i][$variety['variety_id']]['feedback']);
                                    }
                                    else
                                    {
                                        $text=$CI->lang->line('LABEL_FEEDBACK_NOT_GIVEN');
                                    }
                                    echo $text;
                                    ?>
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            <?php
            }
            else
            {
                ?>
                <div class="row show-grid">
                    <div class="col-xs-4">

                    </div>
                    <div class="col-xs-4">
                        <label class="control-label">Visit Not Done Yet</label>
                    </div>
                </div>
            <?php
            }
        }
        ?>
    </div>
</div>
<div class="panel panel-default">
    <div class="panel-heading">
        <h4 class="panel-title">
            <a class="accordion-toggle external" data-toggle="collapse"  data-target="#collapse_fruit_picture" href="#">Fruit Picture and remarks</a>
        </h4>
    </div>
    <div id="collapse_fruit_picture" class="panel-collapse collapse">
        <?php
        foreach($fruits_picture_headers as $headers)
        {
            if(isset($fruits_picture[$headers['id']]))
            {
                ?>
                <div class="row show-grid">
                    <div class="col-xs-4">
                    </div>
                    <div class="col-xs-4">
                        <label class="control-label"><?php echo $headers['name'];?></label>
                    </div>
                </div>
                <div style="overflow-x: auto;" class="row show-grid">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th style="min-width: 150px;"><?php echo $CI->lang->line('LABEL_VARIETY_NAME'); ?></th>
                            <th style="min-width: 250px;">Picture</th>
                            <th style="min-width: 150px;"><?php echo $this->lang->line('LABEL_REMARKS');?></th>
                            <th style="min-width: 150px;"><?php echo $this->lang->line('LABEL_FEEDBACK');?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        foreach($previous_varieties as $variety)
                        {
                            ?>
                            <tr>
                                <td><?php echo $variety['variety_name']; ?></td>
                                <td>
                                    <?php
                                    $image_location='images/no_image.jpg';
                                    if(isset($fruits_picture[$headers['id']][$variety['variety_id']]['image_location'])&&strlen($fruits_picture[$headers['id']][$variety['variety_id']]['image_location'])>0)
                                    {
                                        $image_location=$fruits_picture[$headers['id']][$variety['variety_id']]['image_location'];
                                    }
                                    ?>
                                    <img style="max-width: 250px;" src="<?php echo $CI->config->item('system_base_url_ft_field_visit').$image_location; ?>">
                                </td>
                                <td>
                                    <?php
                                    $text='';
                                    if(isset($fruits_picture[$headers['id']][$variety['variety_id']]))
                                    {
                                        $text.='<b>Entry By</b>:'.$users[$fruits_picture[$headers['id']][$variety['variety_id']]['user_created']]['name'];
                                        $text.='<br><b>Entry Time</b>:'.System_helper::display_date_time($fruits_picture[$headers['id']][$variety['variety_id']]['date_created']);
                                        $text.='<br><b>Remarks</b>:<br>'.nl2br($fruits_picture[$headers['id']][$variety['variety_id']]['remarks']);
                                    }
                                    echo $text;
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    $text='';
                                    if((isset($fruits_picture[$headers['id']][$variety['variety_id']]['user_feedback']))&&(($fruits_picture[$headers['id']][$variety['variety_id']]['user_feedback'])>0))
                                    {
                                        $text.='<b>Entry By</b>:'.$users[$fruits_picture[$headers['id']][$variety['variety_id']]['user_feedback']]['name'];
                                        $text.='<br><b>Entry Time</b>:'.System_helper::display_date_time($fruits_picture[$headers['id']][$variety['variety_id']]['date_feedback']);
                                        $text.='<br><b>Feedback</b>:<br>'.nl2br($fruits_picture[$headers['id']][$variety['variety_id']]['feedback']);
                                    }
                                    else
                                    {
                                        $text=$CI->lang->line('LABEL_FEEDBACK_NOT_GIVEN');
                                    }
                                    echo $text;
                                    ?>
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            <?php
            }
            else
            {
                ?>
                <div class="row show-grid">
                    <div class="col-xs-4">
                        <label class="control-label pull-right"><?php echo $headers['name'];?></label>
                    </div>
                    <div class="col-xs-4">
                        <label class="control-label">Visit Not Done Yet</label>
                    </div>
                </div>
            <?php
            }
        }
        ?>
    </div>
</div>
<div class="panel panel-default">
    <div class="panel-heading">
        <h4 class="panel-title">
            <a class="accordion-toggle external" data-toggle="collapse"  data-target="#collapse_disease_picture" href="#">Disease Picture and remarks</a>
        </h4>
    </div>
    <div id="collapse_disease_picture" class="panel-collapse collapse">
        <?php
        if(sizeof($disease_picture)>0)
        {
            ?>
            <div id="disease_container">
                <div style="overflow-x: auto;" class="row show-grid">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th style="min-width: 150px;"><?php echo $CI->lang->line('LABEL_VARIETY_NAME'); ?></th>
                            <th style="min-width: 250px;">Picture</th>
                            <th style="min-width: 150px;"><?php echo $this->lang->line('LABEL_REMARKS');?></th>
                            <th style="min-width: 150px;"><?php echo $this->lang->line('LABEL_FEEDBACK');?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        foreach($disease_picture as $index=>$disease_info)
                        {
                            ?>
                            <tr>
                                <td>
                                    <?php echo $previous_varieties[$disease_info['variety_id']]['variety_name']; ?>
                                </td>
                                <td>
                                    <?php
                                    $image_location='images/no_image.jpg';
                                    if(strlen($disease_info['image_location'])>0)
                                    {
                                        $image_location=$disease_info['image_location'];
                                    }
                                    ?>
                                    <img style="max-width: 250px;" src="<?php echo $CI->config->item('system_base_url_ft_field_visit').$image_location; ?>">
                                </td>
                                <td>
                                    <?php
                                    $text='';
                                    {
                                        $text.='<b>Entry By</b>:'.$users[$disease_info['user_created']]['name'];
                                        $text.='<br><b>Entry Time</b>:'.System_helper::display_date_time($disease_info['date_created']);
                                        $text.='<br><b>Remarks</b>:<br>'.nl2br($disease_info['remarks']);
                                    }
                                    echo $text;
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    $text='';
                                    if((isset($disease_info['user_feedback']))&&(($disease_info['user_feedback'])>0))
                                    {
                                        $text.='<b>Entry By</b>:'.$users[$disease_info['user_feedback']]['name'];
                                        $text.='<br><b>Entry Time</b>:'.System_helper::display_date_time($disease_info['date_feedback']);
                                        $text.='<br><b>Feedback</b>:<br>'.nl2br($disease_info['feedback']);
                                    }
                                    else
                                    {
                                        $text=$CI->lang->line('LABEL_FEEDBACK_NOT_GIVEN');
                                    }
                                    echo $text;
                                    ?>
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php
        }
        else
        {
            ?>
            <div class="row show-grid">
                <div class="col-xs-4">

                </div>
                <div class="col-xs-4">
                    <label class="control-label">No Disease Found Yet</label>
                </div>
            </div>
        <?php
        }
        ?>
    </div>
</div>
</div>
<div class="clearfix"></div>
<script type="text/javascript">
    jQuery(document).ready(function()
    {
        system_preset({controller:'<?php echo $CI->router->class; ?>'});
    });
</script>
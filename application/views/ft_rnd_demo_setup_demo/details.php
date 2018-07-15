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
    <div class="col-md-12">
        <table class="table table-bordered table-responsive system_table_details_view">
            <tbody>
            <tr>
                <td class="widget-header header_caption"><label class="control-label pull-right">Setup Created By</label></td>
                <td class="header_value"><label class="control-label"><?php echo $users[$item['user_created']]['name']; ?></label></td>
                <td class="widget-header header_caption"><label class="control-label pull-right">Setup Created Time</label></td>
                <td class="header_value"><label class="control-label"><?php echo System_helper::display_date_time($item['date_created']);?></label></td>
            </tr>
            <?php if($item['date_updated']){?>
                <tr>
                    <td class="widget-header header_caption"><label class="control-label pull-right">Setup Updated By</label></td>
                    <td class="header_value"><label class="control-label"><?php echo $users[$item['user_updated']]['name']; ?></label></td>
                    <td class="widget-header header_caption"><label class="control-label pull-right">Setup Update Time</label></td>
                    <td class="header_value"><label class="control-label"><?php echo System_helper::display_date_time($item['date_updated']);?></label></td>
                </tr>
            <?php } ?>
            <tr>
                <td class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_YEAR');?></label></td>
                <td class="header_value"><label class="control-label"><?php echo $item['year'];?></label></td>
                <td class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ADDRESS');?></label></td>
                <td class="header_value"><label class="control-label"><?php echo System_helper::display_date_time($item['date_created']);?></label></td>
            </tr>
            <tr>
                <td class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_SEASON');?></label></td>
                <td class="header_value"><label class="control-label"><?php echo $item['season'];?></label></td>
                <td class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CONTACT_NO');?></label></td>
                <td class="header_value"><label class="control-label"><?php echo $item['contact_no'];?></label></td>
            </tr>
            <tr>
                <td class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_NAME');?></label></td>
                <td class="header_value"><label class="control-label"><?php echo $item['crop_name'];?></label></td>
                <td class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_SOWING');?></label></td>
                <td class="header_value"><label class="control-label"><?php echo System_helper::display_date($item['date_sowing']);?></label></td>
            </tr>
            <tr>
                <td class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_TYPE_NAME');?></label></td>
                <td class="header_value"><label class="control-label"><?php echo $item['crop_type_name'];?></label></td>
                <td class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_TRANSPLANT');?></label></td>
                <td class="header_value"><label class="control-label"><?php echo System_helper::display_date($item['date_transplant']);?></label></td>
            </tr>
            <tr>
                <td class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_VARIETY_NAME');?></label></td>
                <td class="header_value">
                    <label class="control-label">
                        <?php
                        foreach($varieties as $variety)
                        {
                            if(isset($previous_varieties[$variety['value']]))
                            {
                                ?>
                                <div class="">
                                    <label><?php  echo $variety['text'].' ('.$variety['whose'].')';?></label>
                                </div>
                            <?php
                            }
                        }
                        ?>
                    </label>
                </td>
                <td class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_NUM_VISITS');?></label></td>
                <td class="header_value"><label class="control-label"><?php echo $item['num_visits'];?></label></td>
            </tr>
            <tr>
                <td class="widget-header header_caption"><label class="control-label pull-right">PRI's Name</label></td>
                <td class="header_value"><label class="control-label"><?php echo $item['name'];?></label></td>
                <td class="widget-header header_caption"><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_INTERVAL');?></label></td>
                <td class="header_value"><label class="control-label"><?php echo $item['interval'];?></label></td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
<div class="clearfix"></div>


<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();

$action_buttons = array();
$action_buttons[] = array
(
    'label' => $CI->lang->line("ACTION_BACK"),
    'href' => site_url($CI->controller_url)
);
$action_buttons[] = array
(
    'type' => 'button',
    'label' => $CI->lang->line("ACTION_SAVE"),
    'id' => 'button_action_save',
    'data-form' => '#save_form'
);

$CI->load->view('action_buttons', array('action_buttons' => $action_buttons));
?>

<form class="form_valid" id="save_form" action="<?php echo site_url($CI->controller_url . '/index/save_date'); ?>" method="post">
    <input type="hidden" id="id" name="id" value="<?php echo $item['id']; ?>"/>

    <div class="row widget">
        <div class="widget-header">
            <div class="title">
                <?php echo $title; ?>
            </div>
            <div class="clearfix"></div>
        </div>

        <?php echo $CI->load->view("info_basic", '', true); ?>

        <div class="row show-grid">
            <div class="col-xs-3"> &nbsp; </div>
            <div class="col-xs-6">
                <table class="table table-bordered">
                    <tr>
                        <th style="width:160px"> &nbsp; </th>
                        <th style="text-align:center"><?php echo $CI->lang->line('LABEL_VARIETY1_NAME'); ?></th>
                        <?php if ($item['variety2_id'] > 0)
                        {
                            ?>
                            <th style="text-align:center"><?php echo $CI->lang->line('LABEL_VARIETY2_NAME'); ?></th>
                        <?php
                        }
                        ?>
                    </tr>
                    <tr>
                        <td>
                            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_VARIETY_NAME'); ?> &nbsp;</label>
                        </td>
                        <td><?php echo $item['variety1_name']; ?></td>
                        <?php if ($item['variety2_id'] > 0)
                        {
                            ?>
                            <td><?php echo $item['variety2_name']; ?></td>
                        <?php
                        }
                        ?>
                    </tr>
                    <tr>
                        <td>
                            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_SOWING'); ?> &nbsp;</label>
                        </td>
                        <td><?php echo System_helper::display_date($item['date_sowing_variety1']); ?></td>
                        <?php if ($item['variety2_id'] > 0)
                        {
                            ?>
                            <td><?php echo System_helper::display_date($item['date_sowing_variety2']); ?></td>
                        <?php
                        }
                        ?>
                    </tr>
                    <tr>
                        <td>
                            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_TRANSPLANTING'); ?>
                                <span style="color:#FF0000">*</span></label>
                        </td>
                        <td>
                            <div class="form-group">
                                <div class='input-group date'>
                                    <input type="text" name="item[date_transplanting_variety1]" id="date_transplanting_variety1" class="form-control datepicker" value="<?php echo System_helper::display_date($item['date_transplanting_variety1']); ?>" readonly/>
                                    <span class="input-group-addon">
                                        <i class="glyphicon glyphicon-calendar"></i>
                                    </span>
                                </div>
                            </div>
                        </td>
                        <?php if ($item['variety2_id'] > 0)
                        {
                            ?>
                            <td>
                                <div class="form-group">
                                    <div class='input-group date'>
                                        <input type="text" name="item[date_transplanting_variety2]" id="date_transplanting_variety2" class="form-control datepicker" value="<?php echo System_helper::display_date($item['date_transplanting_variety2']); ?>" readonly/>
                                        <span class="input-group-addon">
                                            <i class="glyphicon glyphicon-calendar"></i>
                                        </span>
                                    </div>
                                </div>
                            </td>
                        <?php
                        }
                        ?>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="clearfix"></div>

</form>

<script type="text/javascript">
    $(document).ready(function () {
        system_off_events(); // Triggers

        $(".datepicker").datepicker({dateFormat: display_date_format});
        $('.input-group-addon').click(function(){
            $(this).siblings('input.datepicker').focus();
        });
    });
</script>

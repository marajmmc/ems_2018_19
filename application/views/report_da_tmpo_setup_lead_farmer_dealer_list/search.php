<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();
$action_buttons=array();
$action_buttons[]=array
(
    'label'=>$CI->lang->line("ACTION_REFRESH"),
    'href'=>site_url($CI->controller_url.'/index')
);
$CI->load->view('action_buttons',array('action_buttons'=>$action_buttons));
?>
<form class="form_valid" id="save_form" action="<?php echo site_url($CI->controller_url.'/index/list');?>" method="post">
    <div class="row widget">
        <div class="widget-header">
            <div class="title">
                <?php echo $title; ?>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="row show-grid">
            <div style="" class="row show-grid" id="outlet_id_container">
                <div class="col-xs-4">
                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_OUTLET_NAME');?></label>
                </div>
                <div class="col-sm-4 col-xs-8">
                    <?php
                    if(sizeof($outlets)===1)
                    {
                        ?>
                        <label class="control-label pull-right"><?php echo $outlets[0]['outlet_name'];?></label>
                    <?php
                    }
                    else
                    {
                        ?>
                        <select id="outlet_id" name="report[outlet_id]" class="form-control">
                            <option value=""><?php echo $CI->lang->line('SELECT');?></option>
                            <?php
                            foreach($outlets as $outlet)
                            {?>
                                <option value="<?php echo $outlet['id']?>"><?php echo $outlet['outlet_name'];?></option>
                            <?php
                            }
                            ?>
                        </select>
                    <?php
                    }
                    ?>
                </div>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-5">
                &nbsp;
            </div>
            <div class="col-xs-7">
                <div class="action_button">
                    <button id="button_action_report" type="button" class="btn" data-form="#save_form"><?php echo $CI->lang->line("ACTION_REPORT"); ?></button>
                </div>
            </div>
        </div>
    </div>

    <div class="clearfix"></div>
</form>

<div id="system_report_container">

</div>

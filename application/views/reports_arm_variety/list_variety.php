<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();
?>
<form class="form_valid" id="report_form" action="<?php echo site_url($CI->controller_url.'/index/list');?>" method="post">
    <div class="row widget">
        <div style="" class="row show-grid">
            <div class="widget-header">
                <div class="title">
                    ARM Variety
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="col-xs-12 text-center">
                <div class="checkbox">
                    <label><input type="checkbox" id="select_all_arm">Select All</label>
                </div>
            </div>
            <?php
            foreach($arm_varieties as $variety)
            {
                ?>
                <div class="col-xs-4">
                    <div class="checkbox">
                        <label><input type="checkbox" class="setup_arm" name="variety_ids[]" value="<?php echo $variety['variety_id']; ?>"><?php echo $variety['variety_name']; ?></label>
                    </div>
                </div>
            <?php
            }
            ?>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">

            </div>
            <div class="col-xs-4">
                <div class="action_button">
                    <button id="button_action_report" type="button" class="btn" data-form="#report_form"><?php echo $CI->lang->line("ACTION_REPORT"); ?></button>
                </div>

            </div>
            <div class="col-xs-4">

            </div>
        </div>
    </div>
    <div class="clearfix"></div>
</form>

<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();

$action_buttons = array();
$action_buttons[] = array
(
    'label' => $CI->lang->line("ACTION_BACK"),
    'href' => site_url($CI->controller_url . '/index/list_image/' . $id)
);
if (isset($CI->permissions['action2']) && ($CI->permissions['action2'] == 1))
{
    $action_buttons[] = array
    (
        'type' => 'button',
        'label' => $CI->lang->line("ACTION_SAVE"),
        'id' => 'button_action_save',
        'data-form' => '#save_form'
    );
}
$action_buttons[] = array(
    'type' => 'button',
    'label' => $CI->lang->line("ACTION_CLEAR"),
    'id' => 'button_action_clear',
    'data-form' => '#save_form'
);
$CI->load->view("action_buttons", array('action_buttons' => $action_buttons));

$image_base_path = $CI->config->item('system_base_url_picture');
$image_style = 'max-height:200px';
$no_image_path = 'images/no_image.jpg';
?>

<form class="form_valid" id="save_form" action="<?php echo site_url($CI->controller_url . '/index/save_image'); ?>" method="post">
    <input type="hidden" id="id" name="id" value="<?php echo $item['id']; ?>"/>

    <div class="row widget">
        <div class="widget-header">
            <div class="title">
                <?php echo $title; ?>
            </div>
            <div class="clearfix"></div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"> Select Image File <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-xs-4">
                <div id="image_demonstration">
                    <a href="<?php echo $CI->config->item('system_base_url_picture') . 'images/no_image.jpg'; ?>" target="_blank" class="external blob">
                        <img src="<?php echo $CI->config->item('system_base_url_picture') . 'images/no_image.jpg'; ?>" style="max-height:200px" alt="Picture Missing"/>
                    </a>
                </div>
            </div>
            <div class="col-xs-4">
                <input type="file" class="browse_button" data-preview-container="#image_demonstration" name="image_demonstration" />
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_REMARKS'); ?> &nbsp;</label>
            </div>
            <div class="col-xs-4">
                <textarea class="form-control" name="item[remarks]"></textarea>
            </div>
        </div>

        <div class="clearfix"></div>
    </div>
</form>

<script type="text/javascript">
    jQuery(document).ready(function ($) {
        system_off_events(); // Triggers
        system_preset({controller: '<?php echo $CI->router->class; ?>'});
        $(".browse_button").filestyle({input: false, icon: false, buttonText: "Upload", buttonName: "btn-primary"});
    });
</script>

<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();

$action_buttons = array();
if (isset($CI->permissions['action1']) && ($CI->permissions['action1'] == 1))
{
    $action_buttons[] = array(
        'label' => $CI->lang->line("ACTION_NEW"),
        'href' => site_url($CI->controller_url . '/index/add')
    );
}
if (isset($CI->permissions['action2']) && ($CI->permissions['action2'] == 1))
{
    $action_buttons[] = array(
        'type' => 'button',
        'label' => $CI->lang->line("ACTION_EDIT"),
        'class' => 'button_jqx_action',
        'data-action-link' => site_url($CI->controller_url . '/index/edit')
    );
}
if (isset($CI->permissions['action0']) && ($CI->permissions['action0'] == 1))
{
    $action_buttons[] = array(
        'type' => 'button',
        'label' => $CI->lang->line("ACTION_DETAILS"),
        'class' => 'button_jqx_action',
        'data-action-link' => site_url($CI->controller_url . '/index/details')
    );
}
if (isset($CI->permissions['action4']) && ($CI->permissions['action4'] == 1))
{
    $action_buttons[] = array(
        'type' => 'button',
        'label' => $CI->lang->line("ACTION_PRINT"),
        'class' => 'button_action_download',
        'data-title' => "Print",
        'data-print' => true
    );
}
if (isset($CI->permissions['action5']) && ($CI->permissions['action5'] == 1))
{
    $action_buttons[] = array(
        'type' => 'button',
        'label' => $CI->lang->line("ACTION_DOWNLOAD"),
        'class' => 'button_action_download',
        'data-title' => "Download"
    );
}
if (isset($CI->permissions['action6']) && ($CI->permissions['action6'] == 1))
{
    $action_buttons[] = array
    (
        'label' => 'Preference',
        'href' => site_url($CI->controller_url . '/index/set_preference')
    );
}
$action_buttons[] = array(
    'label' => $CI->lang->line("ACTION_REFRESH"),
    'href' => site_url($CI->controller_url . '/index/list')

);
$CI->load->view('action_buttons', array('action_buttons' => $action_buttons));
?>
<div class="row widget">
    <div class="widget-header">
        <div class="title">
            <?php echo $title; ?>
        </div>
        <div class="clearfix"></div>
    </div>
    <?php
    if (isset($CI->permissions['action6']) && ($CI->permissions['action6'] == 1))
    {
        $CI->load->view('preference', array('system_preference_items' => $system_preference_items));
    }
    ?>
    <div class="col-xs-12" id="system_jqx_container">

    </div>
</div>
<div class="clearfix"></div>
<script type="text/javascript">
    $(document).ready(function () {
        system_off_events(); // Triggers

        var url = "<?php echo site_url($CI->controller_url.'/index/get_items'); ?>";
        // prepare the data
        var source =
        {
            dataType: "json",
            dataFields: [
                <?php
                foreach($system_preference_items as $key => $value)
                {
                    if($key=='id')
                    {
                    ?>
                        { name: '<?php echo $key; ?>', type: 'integer' },
                    <?php
                    }
                    else
                    {
                    ?>
                        { name: '<?php echo $key; ?>', type: 'string' },
                    <?php
                    }
                }
                ?>
            ],
            id: 'id',
            type: 'POST',
            url: url
        };
        var tooltiprenderer = function (element) {
            $(element).jqxTooltip({position: 'mouse', content: $(element).text() });
        };
        var dataAdapter = new $.jqx.dataAdapter(source);
        // create jqxgrid.
        $("#system_jqx_container").jqxGrid(
            {
                width: '100%',
                source: dataAdapter,
                pageable: true,
                filterable: true,
                sortable: true,
                showfilterrow: true,
                columnsresize: true,
                pagesize: 50,
                pagesizeoptions: ['50', '100', '200', '300', '500', '1000', '5000'],
                selectionmode: 'singlerow',
                altrows: true,
                height: '350px',
                enablebrowserselection: true,
                columnsreorder: true,
                columns: [
                    { text: '<?php echo $CI->lang->line('LABEL_ID'); ?>', dataField: 'id', width: '50', pinned: true, cellsalign: 'right', rendered: tooltiprenderer, hidden: <?php echo $system_preference_items['id']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_FARMER_NAME'); ?>', dataField: 'farmer_name', width: '150', pinned: true, rendered: tooltiprenderer, hidden: <?php echo $system_preference_items['farmer_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_FATHER_HUSBAND_NAME'); ?>', dataField: 'father_husband_name', pinned: true , width: '150', rendered: tooltiprenderer, hidden: <?php echo $system_preference_items['father_husband_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_MOBILE_NO'); ?>', dataField: 'mobile_no', width: '100', pinned: true, rendered: tooltiprenderer, hidden: <?php echo $system_preference_items['mobile_no']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_DISTRICT_NAME'); ?>', dataField: 'district_name', filtertype: 'list', width: '100', rendered: tooltiprenderer, hidden: <?php echo $system_preference_items['district_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_UPAZILLA_NAME'); ?>', dataField: 'upazilla_name', filtertype: 'list', width: '100', rendered: tooltiprenderer, hidden: <?php echo $system_preference_items['upazilla_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_UNION_NAME'); ?>', dataField: 'union_name', filtertype: 'list', width: '100', rendered: tooltiprenderer, hidden: <?php echo $system_preference_items['union_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_CULTIVATED_AREA_VEGETABLES'); ?>', dataField: 'cultivated_area_vegetables', filtertype: 'list', width: '50', rendered: tooltiprenderer, hidden: <?php echo $system_preference_items['cultivated_area_vegetables']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_DATE_CREATED'); ?>', dataField: 'date_created', width: '200', rendered: tooltiprenderer, hidden: <?php echo $system_preference_items['date_created']?0:1;?>}
                    <?php
                    $user = User_helper::get_user();
                    if($user->user_group == $CI->config->item('USER_GROUP_SUPER')): ?>
                        , { text: '<?php echo $CI->lang->line('LABEL_USER_CREATED'); ?>', dataField: 'user_created', width: '120', rendered: tooltiprenderer, hidden: <?php echo $system_preference_items['user_created']?0:1;?>}
                    <?php endif; ?>
                ]
            });
    });
</script>
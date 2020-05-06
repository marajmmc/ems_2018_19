<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();

$action_buttons = array();
if (isset($CI->permissions['action0']) && ($CI->permissions['action0'] == 1))
{
    $action_buttons[] = array(
        'label' => 'Pending List',
        'href' => site_url($CI->controller_url)
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
        'href' => site_url($CI->controller_url . '/index/set_preference_list_all')
    );
}
$action_buttons[] = array(
    'label' => $CI->lang->line("ACTION_REFRESH"),
    'href' => site_url($CI->controller_url . '/index/list_all')
);
$action_buttons[] = array(
    'type' => 'button',
    'label' => $CI->lang->line("ACTION_LOAD_MORE"),
    'id' => 'button_jqx_load_more'
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

        var url = "<?php echo site_url($CI->controller_url.'/index/get_items_all'); ?>";
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
                    ?> { name: '<?php echo $key; ?>', type: 'number' },
                <?php
                    }
                    else
                    {
                    ?> { name: '<?php echo $key; ?>', type: 'string' },
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
        var tooltiprenderer2 = function (element) {
            $(element).jqxTooltip({position: 'mouse', content: "No. of " + $(element).text() });
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
                    { text: '<?php echo $CI->lang->line('LABEL_ID'); ?>', dataField: 'id', pinned: true, width: '50', cellsalign: 'right', hidden: <?php echo $system_preference_items['id']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_NO_OF_IMAGES'); ?>', dataField: 'no_of_images', pinned: true, width: '60', rendered: tooltiprenderer2, filtertype: 'none', cellsalign: 'right', hidden: <?php echo $system_preference_items['no_of_images']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_NO_OF_VIDEOS'); ?>', dataField: 'no_of_videos', pinned: true, width: '60', rendered: tooltiprenderer2, filtertype: 'none', cellsalign: 'right', hidden: <?php echo $system_preference_items['no_of_videos']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_YEAR'); ?>', dataField: 'year', width: '80', rendered: tooltiprenderer, filtertype: 'list', hidden: <?php echo $system_preference_items['year']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_SEASON'); ?>', dataField: 'season', width: '80', rendered: tooltiprenderer, filtertype: 'list', hidden: <?php echo $system_preference_items['season']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_UNION_NAME'); ?>', dataField: 'union_name', width: '180', rendered: tooltiprenderer, hidden: <?php echo $system_preference_items['union_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_FARMER_NAME'); ?>', dataField: 'farmer_name', width: '200', rendered: tooltiprenderer, hidden: <?php echo $system_preference_items['farmer_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_CROP_NAME'); ?>', dataField: 'crop_name', width: '120', rendered: tooltiprenderer, filtertype: 'list', hidden: <?php echo $system_preference_items['crop_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_CROP_TYPE'); ?>', dataField: 'crop_type_name', width: '120', rendered: tooltiprenderer, hidden: <?php echo $system_preference_items['crop_type_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_VARIETY1_NAME'); ?>', dataField: 'variety1_name', width: '120', rendered: tooltiprenderer, hidden: <?php echo $system_preference_items['variety1_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_VARIETY2_NAME'); ?>', dataField: 'variety2_name', width: '120', rendered: tooltiprenderer, hidden: <?php echo $system_preference_items['variety2_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_DATE_SOWING_VARIETY1'); ?>', dataField: 'date_sowing_variety1', width: '120', rendered: tooltiprenderer, hidden: <?php echo $system_preference_items['date_sowing_variety1']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_DATE_SOWING_VARIETY2'); ?>', dataField: 'date_sowing_variety2', width: '120', rendered: tooltiprenderer, hidden: <?php echo $system_preference_items['date_sowing_variety2']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_DATE_TRANSPLANTING_VARIETY1'); ?>', dataField: 'date_transplanting_variety1', width: '120', rendered: tooltiprenderer, hidden: <?php echo $system_preference_items['date_transplanting_variety1']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_DATE_TRANSPLANTING_VARIETY2'); ?>', dataField: 'date_transplanting_variety2', width: '120', rendered: tooltiprenderer, hidden: <?php echo $system_preference_items['date_transplanting_variety2']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_DATE_ACTUAL_EVALUATION'); ?>', dataField: 'date_actual_evaluation', width: '120', rendered: tooltiprenderer, hidden: <?php echo $system_preference_items['date_actual_evaluation']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_STATUS'); ?>', dataField: 'status', width: '80', rendered: tooltiprenderer, filtertype: 'list', hidden: <?php echo $system_preference_items['status']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_STATUS_FORWARD'); ?>', dataField: 'status_forward', width: '120', rendered: tooltiprenderer, filtertype: 'list', hidden: <?php echo $system_preference_items['status_forward']?0:1;?>}
                ]
            });
    });
</script>

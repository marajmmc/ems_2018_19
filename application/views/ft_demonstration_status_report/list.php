<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();

$action_buttons = array();
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
        'href' => site_url($CI->controller_url . '/index/set_preference_search_list')
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
    $(document).ready(function ($) {
        $(document).off("click", ".pop_up");
        $(document).on("click", ".pop_up", function (event) {
            event.preventDefault();
            var left = ((($(window).width() - 330) / 2) + $(window).scrollLeft());
            var top = ((($(window).height() - 330) / 2) + $(window).scrollTop());
            $("#popup_window").jqxWindow({position: { x: left, y: top }});

            var row = $(this).attr('data-item-no');
            var id = $("#system_jqx_container").jqxGrid('getrowdata', row).id;
            $.ajax({
                url: "<?php echo site_url($CI->controller_url.'/index/details') ?>",
                type: 'POST',
                datatype: "JSON",
                data: {
                    html_container_id: '#popup_content',
                    id: id
                },
                success: function (data, status) {

                },
                error: function (xhr, desc, err) {
                    console.log("error");
                }
            });
            $("#popup_window").jqxWindow('open');
        });

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
            url: url,
            data: {item: <?php echo json_encode($item); ?> }
        };
        var cellsrenderer_btn = function (row, column, value, defaultHtml, columnSettings, record) {
            var element = $(defaultHtml);
            element.css({'margin': '0px', 'width': '100%', 'height': '100%', padding: '5px'});
            if (column == 'details_button') {
                element.html('');
                if (record.id != undefined && record.id != '') {
                    element.html('<div><button class="btn btn-sm btn-primary pop_up" data-item-no="' + row + '">Details</button></div>');
                }
            }
            return element[0].outerHTML;
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
                height: '350px',
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
                rowsheight: 40,
                enablebrowserselection: true,
                columnsreorder: true,
                columns: [
                    { text: '<?php echo $CI->lang->line('LABEL_ID'); ?>', dataField: 'id', pinned: true, width: '50', cellsalign: 'right', hidden: <?php echo $system_preference_items['id']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_NO_OF_IMAGES'); ?>', dataField: 'no_of_images', pinned: true, width: '60', rendered: tooltiprenderer2, filtertype: 'none', cellsalign: 'right', hidden: <?php echo $system_preference_items['no_of_images']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_NO_OF_VIDEOS'); ?>', dataField: 'no_of_videos', pinned: true, width: '60', rendered: tooltiprenderer2, filtertype: 'none', cellsalign: 'right', hidden: <?php echo $system_preference_items['no_of_videos']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_YEAR'); ?>', dataField: 'year', width: '80', rendered: tooltiprenderer, filtertype: 'list', hidden: <?php echo $system_preference_items['year']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_SEASON'); ?>', dataField: 'season', width: '80', rendered: tooltiprenderer, filtertype: 'list', hidden: <?php echo $system_preference_items['season']?0:1;?>},

                    { text: '<?php echo $CI->lang->line('LABEL_DIVISION_NAME'); ?>', dataField: 'division_name', width: '100', rendered: tooltiprenderer, filtertype: 'list', hidden: <?php echo $system_preference_items['division_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_ZONE_NAME'); ?>', dataField: 'zone_name', width: '100', rendered: tooltiprenderer, filtertype: 'list', hidden: <?php echo $system_preference_items['zone_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_TERRITORY_NAME'); ?>', dataField: 'territory_name', width: '100', rendered: tooltiprenderer, filtertype: 'list', hidden: <?php echo $system_preference_items['territory_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_DISTRICT_NAME'); ?>', dataField: 'district_name', width: '180', rendered: tooltiprenderer, filtertype: 'list', hidden: <?php echo $system_preference_items['district_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_OUTLET_NAME'); ?>', dataField: 'outlet_name', width: '180', rendered: tooltiprenderer, filtertype: 'list', hidden: <?php echo $system_preference_items['outlet_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_GROWING_AREA'); ?>', dataField: 'growing_area', width: '150', rendered: tooltiprenderer, hidden: <?php echo $system_preference_items['growing_area']?0:1;?>},

                    { text: '<?php echo $CI->lang->line('LABEL_CROP_NAME'); ?>', dataField: 'crop_name', width: '120', rendered: tooltiprenderer, filtertype: 'list', hidden: <?php echo $system_preference_items['crop_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_LEAD_FARMER_NAME'); ?>', dataField: 'lead_farmer_name', width: '200', rendered: tooltiprenderer, hidden: <?php echo $system_preference_items['lead_farmer_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_DATE_SOWING_VARIETY1'); ?>', dataField: 'date_sowing_variety1', width: '120', rendered: tooltiprenderer, hidden: <?php echo $system_preference_items['date_sowing_variety1']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_DATE_SOWING_VARIETY2'); ?>', dataField: 'date_sowing_variety2', width: '120', rendered: tooltiprenderer, hidden: <?php echo $system_preference_items['date_sowing_variety2']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_DATE_TRANSPLANTING_VARIETY1'); ?>', dataField: 'date_transplanting_variety1', width: '120', rendered: tooltiprenderer, hidden: <?php echo $system_preference_items['date_transplanting_variety1']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_DATE_TRANSPLANTING_VARIETY2'); ?>', dataField: 'date_transplanting_variety2', width: '120', rendered: tooltiprenderer, hidden: <?php echo $system_preference_items['date_transplanting_variety2']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_DATE_EXPECTED_EVALUATION'); ?>', dataField: 'date_expected_evaluation', width: '120', rendered: tooltiprenderer, hidden: <?php echo $system_preference_items['date_expected_evaluation']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_DATE_ACTUAL_EVALUATION'); ?>', dataField: 'date_actual_evaluation', width: '120', rendered: tooltiprenderer, hidden: <?php echo $system_preference_items['date_actual_evaluation']?0:1;?>},

                    { text: '<?php echo $CI->lang->line('LABEL_ZSC_EVALUATION'); ?>', dataField: 'zsc_evaluation', width: '100', rendered: tooltiprenderer, filtertype: 'list', hidden: <?php echo $system_preference_items['zsc_evaluation']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_ZSC_STATUS'); ?>', dataField: 'zsc_status', width: '100', rendered: tooltiprenderer, filtertype: 'list', hidden: <?php echo $system_preference_items['zsc_status']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_DETAILS_BUTTON'); ?>',dataField: 'details_button', width: '85', cellsrenderer: cellsrenderer_btn, rendered: tooltiprenderer, filtertype: 'none', hidden: <?php echo $system_preference_items['details_button']?0:1;?> }
                ]
            });
    });
</script>

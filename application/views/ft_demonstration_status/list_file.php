<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();

$action_buttons = array();
$action_buttons[] = array(
    'label' => $CI->lang->line("ACTION_BACK"),
    'href' => site_url($CI->controller_url)
);

if ($file_type == $CI->config->item('system_file_type_image'))
{
    $action_buttons[] = array(
        'label' => 'Upload ' . $file_type,
        'href' => site_url($CI->controller_url . '/index/add_image/' . $id)
    );
    $action_buttons[] = array(
        'type' => 'button',
        'label' => $CI->lang->line('ACTION_EDIT'),
        'class' => 'button_jqx_action',
        'data-action-link' => site_url($CI->controller_url . '/index/edit_image')
    );
    $action_buttons[] = array(
        'label' => $CI->lang->line("ACTION_REFRESH"),
        'href' => site_url($CI->controller_url . '/index/list_image/' . $id)
    );
}
else
{
    $action_buttons[] = array(
        'label' => 'Upload ' . $file_type,
        'href' => site_url($CI->controller_url . '/index/add_video/' . $id)
    );
    $action_buttons[] = array(
        'type' => 'button',
        'label' => $CI->lang->line('ACTION_EDIT'),
        'class' => 'button_jqx_action',
        'data-action-link' => site_url($CI->controller_url . '/index/edit_video')
    );
    $action_buttons[] = array(
        'label' => $CI->lang->line("ACTION_REFRESH"),
        'href' => site_url($CI->controller_url . '/index/list_video/' . $id)
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

    <?php echo $CI->load->view("info_basic", '', true); ?>

    <?php
    /*if (isset($CI->permissions['action6']) && ($CI->permissions['action6'] == 1))
    {
        $CI->load->view('preference', array('system_preference_items' => $system_preference_items));
    }*/
    ?>

    <div class="col-xs-12" id="system_jqx_container">

    </div>
</div>
<div class="clearfix"></div>

<script type="text/javascript">
    $(document).ready(function () {
        system_off_events(); // Triggers

        var url = "<?php echo site_url($CI->controller_url.'/index/'. strtolower('get_items_'.$file_type)).'/'.$id; ?>";
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
        var cellsrenderer = function (row, column, value, defaultHtml, columnSettings, record) {
            var element = $(defaultHtml);
            element.css({'margin': '0px', 'width': '100%', 'height': '100%', padding: '5px'});
            return element[0].outerHTML;
        };
        var dataAdapter = new $.jqx.dataAdapter(source);
        // create jqxgrid.
        $("#system_jqx_container").jqxGrid(
            {
                width: '100%',
                height: '500px',
                rowsheight: 250,
                source: dataAdapter,
                pageable: true,
                filterable: true,
                sortable: false,
                showfilterrow: true,
                columnsresize: true,
                columnsreorder: true,
                pagesize: 50,
                pagesizeoptions: ['50', '100', '200', '300', '500', '1000', '5000'],
                selectionmode: 'singlerow',
                altrows: true,
                enablebrowserselection: true,
                columngroups: [
                    { text: '<b><?php echo $CI->lang->line('LABEL_VARIETY1_NAME'); ?></b>', align: 'center', name: 'variety1' },
                    { text: '<b><?php echo $CI->lang->line('LABEL_VARIETY2_NAME'); ?></b>', align: 'center', name: 'variety2' }
                ],
                columns: [
                    { text: '<b><?php echo $CI->lang->line('LABEL_ID'); ?></b>', dataField: 'id', pinned: true, width: '50', cellsrenderer: cellsrenderer, cellsalign: 'right', hidden: <?php echo $system_preference_items['id']?0:1;?>},

                    /* For Variety Variety1 ( ARM )*/
                    { columngroup: 'variety1', text: '<?php echo $file_type; ?>', dataField: 'file_html_variety1', width: '250', filtertype: 'none', cellsrenderer: cellsrenderer, rendered: tooltiprenderer, hidden: <?php echo $system_preference_items['file_html_variety1']?0:1;?>},
                    { columngroup: 'variety1', text: '<?php echo $CI->lang->line('LABEL_REMARKS'); ?>', dataField: 'remarks_variety1', cellsrenderer: cellsrenderer, rendered: tooltiprenderer, hidden: <?php echo $system_preference_items['remarks_variety1']?0:1;?>},

                    /* For Variety Variety1 ( Competitor )*/
                    { columngroup: 'variety2', text: '<?php echo $file_type; ?>', dataField: 'file_html_variety2', width: '250', filtertype: 'none', cellsrenderer: cellsrenderer, rendered: tooltiprenderer, hidden: <?php echo $system_preference_items['file_html_variety2']?0:1;?>},
                    { columngroup: 'variety2', text: '<?php echo $CI->lang->line('LABEL_REMARKS'); ?>', dataField: 'remarks_variety2', cellsrenderer: cellsrenderer, rendered: tooltiprenderer, hidden: <?php echo $system_preference_items['remarks_variety2']?0:1;?>}
                ]
            });
    });
</script>

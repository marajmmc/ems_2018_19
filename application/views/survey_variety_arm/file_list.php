<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_BACK"),
    'href'=>site_url($CI->controller_url)
);
if($file_type==$this->config->item('system_file_type_image'))
{
    if(isset($CI->permissions['action1']) && ($CI->permissions['action1']==1))
    {
        $action_buttons[]=array(
            'label'=>$CI->lang->line("ACTION_NEW"),
            'href'=>site_url($CI->controller_url.'/index/add_edit_image/'.$item['id'])
        );
    }
    if(isset($CI->permissions['action2']) && ($CI->permissions['action2']==1))
    {
        $action_buttons[]=array(
            'type'=>'button',
            'label'=>$CI->lang->line('ACTION_EDIT'),
            'class'=>'button_jqx_action',
            'data-action-link'=>site_url($CI->controller_url.'/index/add_edit_image/'.$item['id'])
        );
    }
}
else
{
    if(isset($CI->permissions['action1']) && ($CI->permissions['action1']==1))
    {
        $action_buttons[]=array(
            'label'=>$CI->lang->line("ACTION_NEW"),
            'href'=>site_url($CI->controller_url.'/index/add_edit_video/'.$item['id'])
        );
    }
    if(isset($CI->permissions['action2']) && ($CI->permissions['action2']==1))
    {
        $action_buttons[]=array(
            'type'=>'button',
            'label'=>$CI->lang->line('ACTION_EDIT'),
            'class'=>'button_jqx_action',
            'data-action-link'=>site_url($CI->controller_url.'/index/add_edit_video/'.$item['id'])
        );
    }
}
if(isset($CI->permissions['action4']) && ($CI->permissions['action4']==1))
{
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_PRINT"),
        'class'=>'button_action_download',
        'data-title'=>"Print",
        'data-print'=>true
    );
}
if(isset($CI->permissions['action5']) && ($CI->permissions['action5']==1))
{
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_DOWNLOAD"),
        'class'=>'button_action_download',
        'data-title'=>"Download"
    );
}
if($file_type==$this->config->item('system_file_type_image'))
{
    $action_buttons[]=array(
        'label'=>$CI->lang->line("ACTION_REFRESH"),
        'href'=>site_url($CI->controller_url.'/index/list_image/'.$item['id'])
    );
}
else
{
    $action_buttons[]=array(
        'label'=>$CI->lang->line("ACTION_REFRESH"),
        'href'=>site_url($CI->controller_url.'/index/list_video/'.$item['id'])
    );
}

$CI->load->view('action_buttons',array('action_buttons'=>$action_buttons));
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
                <label class=""><a class="external text-danger" data-toggle="collapse" data-target="#collapse_variety_info" href="#">+ Variety Info</a></label>
            </h4>
        </div>
        <div id="collapse_variety_info" class="panel-collapse collapse">
            <div class="row show-grid">
                <div class="col-xs-4">
                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_NAME');?></label>
                </div>
                <div class="col-sm-4 col-xs-8">
                    <label class="control-label"><?php echo $item['crop_name'];?></label>
                </div>
            </div>

            <div class="row show-grid">
                <div class="col-xs-4">
                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_TYPE_NAME');?></label>
                </div>
                <div class="col-sm-4 col-xs-8">
                    <label class="control-label"><?php echo $item['crop_type_name'];?></label>
                </div>
            </div>
            <div class="row show-grid">
                <div class="col-xs-4">
                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_NAME');?></label>
                </div>
                <div class="col-sm-4 col-xs-8">
                    <label class="control-label"><?php echo $item['name'];?></label>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xs-12" id="system_jqx_container">

    </div>
</div>
<div class="clearfix"></div>
<script type="text/javascript">
    $(document).ready(function ()
    {
        system_preset({controller:'<?php echo $CI->router->class; ?>'});
        var url = "<?php echo site_url($CI->controller_url.'/index/get_files');?>";
        // prepare the data
        var source =
        {
            dataType: "json",
            dataFields: [
                { name: 'id', type: 'int' },
                <?php
                foreach($system_preference_items as $key => $value){ ?>
                { name: '<?php echo $key; ?>', type: 'string' },
                <?php } ?>
            ],
            id: 'id',
            type: 'POST',
            url: url,
            data:{id:<?php echo $item['id']; ?>,file_type:'<?php echo $file_type; ?>'}
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
                pagesize:50,
                pagesizeoptions: ['20', '50', '100', '200','300','500'],
                selectionmode: 'singlerow',
                altrows: true,
                height: '350px',
                enablebrowserselection:true,
                columnsreorder: true,
                columns: [
                    { text: '<?php echo $CI->lang->line('LABEL_ID'); ?>', dataField: 'id',width:'50',cellsalign: 'right',rendered:tooltiprenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_REMARKS'); ?>', dataField: 'remarks',width:'350',rendered:tooltiprenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_STATUS'); ?>', dataField: 'status',width:'150',filtertype: 'list',rendered:tooltiprenderer}
                ]
            });
    });
</script>

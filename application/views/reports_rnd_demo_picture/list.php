<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();

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
$CI->load->view('action_buttons',array('action_buttons'=>$action_buttons));

?>


<div class="row widget">
    <div class="widget-header">
        <div class="title">
            <?php echo $title; ?>
        </div>
        <div class="clearfix"></div>
    </div>
    <?php
    if(isset($CI->permissions['action6'])&&($CI->permissions['action6']==1))
    {

        ?>
        <div class="col-xs-2 "><div class="checkbox"><label><input type="checkbox" class="system_jqx_column" value="year_season" checked><span class="">Time Info</span></label></div></div>
        <div class="col-xs-2 "><div class="checkbox"><label><input type="checkbox" class="system_jqx_column" value="crop_info" checked><span class="">Crop Info</span></label></div></div>
        <div class="col-xs-2 "><div class="checkbox"><label><input type="checkbox" class="system_jqx_column" id="visit_images" checked><span class="">Visit Images</span></label></div></div>
        <div class="col-xs-2 "><div class="checkbox"><label><input type="checkbox" class="system_jqx_column" id="fruit_images" checked><span class="">Fruit Images</span></label></div></div>
        <div class="col-xs-2 "><div class="checkbox"><label><input type="checkbox" class="system_jqx_column" id="disease_images" checked><span class="">Disease Images</span></label></div></div>
    <?php
    }
    ?>
    <div class="col-xs-12" id="system_jqx_container">

    </div>
</div>
<div class="clearfix"></div>
<script type="text/javascript">
    $(document).ready(function ()
    {
        $(document).off("click", "#fruit_images");
        $(document).off("click", "#visit_images");
        $(document).off("click", "#disease_images");
        $(document).on("click", "#fruit_images", function(event)
        {
            var jqxgrid_id='#system_jqx_container';
            $(jqxgrid_id).jqxGrid('beginupdate');
            if($(this).is(':checked'))
            {
                <?php
                    foreach($fruits_picture_headers as $headers)
                        {?>$(jqxgrid_id).jqxGrid('showcolumn', '<?php echo 'fruit_pictures_'.$headers['id'];?>');
                <?php
            }
        ?>
            }
            else
            {
                <?php
                    foreach($fruits_picture_headers as $headers)
                        {?>$(jqxgrid_id).jqxGrid('hidecolumn', '<?php echo 'fruit_pictures_'.$headers['id'];?>');
                <?php
                    }
            ?>
            }
            $(jqxgrid_id).jqxGrid('endupdate');

        });
        $(document).on("click", "#visit_images", function(event)
        {
            var jqxgrid_id='#system_jqx_container';
            $(jqxgrid_id).jqxGrid('beginupdate');
            if($(this).is(':checked'))
            {
                <?php
                    for($i=1;$i<=$max_visits;$i++)
                        {?>$(jqxgrid_id).jqxGrid('showcolumn', '<?php echo 'visit_pictures_'.$i;?>');
                <?php
            }
        ?>
            }
            else
            {
                <?php
                    for($i=1;$i<=$max_visits;$i++)
                        {?>$(jqxgrid_id).jqxGrid('hidecolumn', '<?php echo 'visit_pictures_'.$i;?>');
                <?php
                    }
            ?>
            }
            $(jqxgrid_id).jqxGrid('endupdate');

        });
        $(document).on("click", "#disease_images", function(event)
        {
            var jqxgrid_id='#system_jqx_container';
            $(jqxgrid_id).jqxGrid('beginupdate');
            if($(this).is(':checked'))
            {
                <?php
                    for($i=0;$i<$max_diseases;$i++)
                        {?>$(jqxgrid_id).jqxGrid('showcolumn', '<?php echo 'disease_pictures_'.$i;?>');
                <?php
            }
        ?>
            }
            else
            {
                <?php
                    for($i=0;$i<$max_diseases;$i++)
                        {?>$(jqxgrid_id).jqxGrid('hidecolumn', '<?php echo 'disease_pictures_'.$i;?>');
                <?php
                    }
            ?>
            }
            $(jqxgrid_id).jqxGrid('endupdate');

        });

        var url = "<?php echo site_url($CI->controller_url.'/index/get_items');?>";
        // prepare the data
        var source =
        {
            dataType: "json",
            dataFields: [
                { name: 'id', type: 'int' },
                { name: 'year_season', type: 'string' },
                { name: 'crop_info', type: 'string' },
                    <?php
                        for($i=1;$i<=$max_visits;$i++)
                        {
                        ?>{ name: '<?php echo 'visit_pictures_'.$i;?>', type: 'string' },
                    <?php
                    }
                    foreach($fruits_picture_headers as $headers)
                    {?>{ name: '<?php echo 'fruit_pictures_'.$headers['id'];?>', type: 'string' },
                    <?php
                        }
                        for($i=0;$i<$max_diseases;$i++)
                        {
                        ?>{ name: '<?php echo 'disease_pictures_'.$i;?>', type: 'string' },
                <?php
                }
            ?>

                { name: 'details', type: 'string' }
            ],
            id: 'id',
            url: url,
            type: 'POST',
            data:JSON.parse('<?php echo json_encode($options);?>')
        };
        var cellsrenderer = function(row, column, value, defaultHtml, columnSettings, record)
        {
            var element = $(defaultHtml);
            element.css({'margin': '0px','width': '100%', 'height': '100%',padding:'5px'});
            if(column.substr(0,6)=='image_')
            {
                var key=column.substr(6);
                if(record['details']!==undefined)
                {
                    element.html(record['details'][key]['image_url']);
                }

            }


            return element[0].outerHTML;
        };
        var tooltiprenderer = function (element) {
            $(element).jqxTooltip({position: 'mouse', content: $(element).text() });
        };
        var dataAdapter = new $.jqx.dataAdapter(source);
        // create jqxgrid.
        $("#system_jqx_container").jqxGrid(
            {
                width: '100%',
                height:'350px',
                source: dataAdapter,
                columnsresize: true,
                columnsreorder: true,
                altrows: true,
                rowsheight: 133,
                columns: [
                    {
                        text: '<?php echo $CI->lang->line('LABEL_SL_NO'); ?>',datafield: '',pinned:true,width:'50', columntype: 'number',cellsalign: 'right',sortable:false,filterable:false,
                        cellsrenderer: function(row, column, value, defaultHtml, columnSettings, record)
                        {
                            var element = $(defaultHtml);
                            element.html(value+1);
                            return element[0].outerHTML;
                        }
                    },
                    { text: 'Time Info', dataField: 'year_season',width: '150',cellsrenderer: cellsrenderer,pinned:true,rendered: tooltiprenderer},
                    { text: 'Crop Info', dataField: 'crop_info',width: '150',cellsrenderer: cellsrenderer,pinned:true,rendered: tooltiprenderer},
                        <?php
                            for($i=1;$i<=$max_visits;$i++)
                            {?>{ columngroup: 'visit_images',text: '<?php echo $i;?>', dataField: '<?php echo 'visit_pictures_'.$i;?>',align:'center',cellsalign: 'right',width:'143',cellsrenderer: cellsrenderer,rendered: tooltiprenderer},
                    <?php
                        }
                    ?>,
                        <?php
                            foreach($fruits_picture_headers as $headers)
                            {?>{ columngroup: 'fruit_images',text: '<?php echo $headers['name'];?>', dataField: '<?php echo 'fruit_pictures_'.$headers['id'];?>',align:'center',cellsalign: 'right',width:'143',cellsrenderer: cellsrenderer,rendered: tooltiprenderer},
                    <?php
                        }
                    ?>,
                        <?php
                            for($i=0;$i<$max_diseases;$i++)
                            {?>{ columngroup: 'disease_images',text: '<?php echo $i+1;?>', dataField: '<?php echo 'disease_pictures_'.$i;?>',align:'center',cellsalign: 'right',width:'143',cellsrenderer: cellsrenderer,rendered: tooltiprenderer},
                    <?php
                        }
                    ?>
                ],
                columngroups:
                    [
                        { text: 'Visit Images', align: 'center', name: 'visit_images' },
                        { text: 'Fruit Images', align: 'center', name: 'fruit_images' },
                        { text: 'Disease Images', align: 'center', name: 'disease_images' }
                    ]
            });
    });
</script>
<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();

//pr($items, 0);
$count = sizeof($items);
?>

<div class="row widget">
    <div class="widget-header">
        <div class="title">Edit History</div>
        <div class="clearfix"></div>
    </div>

    <div class="row show-grid">
        <div class="col-xs-12">
            <div class="panel panel-default">
                <?php foreach ($items as $key => $item)
                {
                    ?>

                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <label><a class="external text-danger" data-toggle="collapse" data-target="#collapse<?php echo $key; ?>" href="#">
                                    + Revision:<?php echo ($count-$item['revision']+1).' ('.$item['revision'].')'; ?>
                            </a></label>
                        </h4>
                    </div>

                    <div id="collapse<?php echo $key; ?>" class="panel-collapse collapse">
                        <div class="row widget" style="margin:0; padding:20px 0; border:none">

                            <div class="row show-grid">
                                <div class="col-xs-4">
                                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_PROPOSAL'); ?> :</label>
                                </div>
                                <div class="col-sm-4 col-xs-8">
                                    <label class="control-label"><?php echo $item['date_proposal']; ?></label>
                                </div>
                            </div>

                        </div>
                    </div>

                <?php } ?>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
</div>


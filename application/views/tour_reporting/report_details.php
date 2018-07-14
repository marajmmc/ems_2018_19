<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();

?>
<style> .normal{font-weight:normal !important} </style>
<div class="row widget">
    <div class="widget-header">
        <div class="title">
            <?php echo 'Tour Purpose :: ' . $item['purpose']; ?>
        </div>
        <div class="clearfix"></div>
    </div>

    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right" style="text-align:right">Report:<br/>(Description)</label>
        </div>
        <div class="col-xs-8">
            <label class="normal"><?php echo $item['report_description']; ?></label>
        </div>
    </div>

    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right">Recommendation:</label>
        </div>
        <div class="col-xs-8">
            <label class="normal"><?php echo $item['recommendation']; ?></label>
        </div>
    </div>

    <?php if ($item['name'])
    {
        ?>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Contact Person:</label>
            </div>
            <div class="col-xs-8">
                <label class="normal"><?php echo $item['name']; ?></label>
            </div>
        </div>
    <?php
    }
    if ($item['contact_no'])
    {
        ?>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Contact Person:</label>
            </div>
            <div class="col-xs-8">
                <label class="normal"><?php echo $item['contact_no']; ?></label>
            </div>
        </div>
    <?php
    }
    if ($item['profession'])
    {
        ?>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Profession:</label>
            </div>
            <div class="col-xs-8">
                <label class="normal"><?php echo $item['profession']; ?></label>
            </div>
        </div>
    <?php
    }
    if ($item['discussion'])
    {
        ?>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Discussion:</label>
            </div>
            <div class="col-xs-8">
                <label class="normal"><?php echo $item['discussion']; ?></label>
            </div>
        </div>
    <?php } ?>
</div>
<div class="clearfix"></div>
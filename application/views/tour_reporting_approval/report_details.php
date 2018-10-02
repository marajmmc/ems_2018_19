<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();
?>
<style>
    .normal{font-weight:normal !important}
    .blob img{width:250px}
    .blob {
        display:inline-block;
        padding:3px;
        border: 3px solid #8c8c8c
    }
    .blob:hover{border:3px solid #3693CF}
</style>
<div class="row widget">
    <div class="widget-header">
        <div class="title">
            <?php echo $title.' :: ' . $item['purpose']; ?>
        </div>
        <div class="clearfix"></div>
    </div>

    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right" style="text-align:right">Reporting Date:</label>
        </div>
        <div class="col-xs-8">
            <label class="normal"><?php echo System_helper::display_date($item['date_reporting']); ?></label>
        </div>
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
    <?php
    }
    $img_src = $this->config->item('system_base_url_picture') . $item['image_location'];
    ?>
    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right">Picture:</label>
        </div>
        <div class="col-xs-8">
            <a href="<?php echo $img_src; ?>" target="_blank" class="external blob"><img src="<?php echo $img_src; ?>" alt="Image Missing" /></a>
        </div>
    </div>
</div>
<div class="clearfix"></div>
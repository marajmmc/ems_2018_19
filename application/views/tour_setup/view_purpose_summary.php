<div class="row show-grid">
    <div class="col-xs-<?php echo $col_1; ?>">
        <label class="control-label pull-right">Purpose(s):</label>
    </div>
    <div class="col-xs-<?php echo $col_2; ?> purpose-list">
        <table class="table table-bordered table-striped table-hover">
            <thead>
                <tr>
                    <th><?php $slno_label; ?></th>
                    <th>Purpose</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($items)
                {
                    $serial = 0;
                    foreach ($items as $row)
                    {
                    ?>
                        <tr>
                            <td><?php echo ++$serial; ?></td>
                            <td><?php echo $row['purpose']; ?></td>
                        </tr>
                    <?php
                    }
                }
                else
                {   ?>
                    <tr>
                        <td colspan="2"> Tour Purpose has Not been Setup</td>
                    </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div class="houseDescription newsWin" style="display:none;">
    <div class="closeBtn"></div>

    <div class="scrollContent">

        <h3><?php echo $this->get('name')?></h3>
        <div class="date"><?php echo $this->get('date_show')?></div>

        <div class="newsText">
            <?php echo $this->get('content')?>
        </div>
    </div>
</div>
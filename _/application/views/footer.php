<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<div class="overlay_news" style="z-index:60000;display:none;"></div>
<div class="footer">
    <div class="footContainer">
        <a href="<?php echo $this->get('base_url')?>" class="logo"></a>
				<div class="footer-right-holder">	
					<div class="phone-num">
						
					</div>
					<?if($this->uri->segment(2) == "alpiyka" || $this->uri->segment(2) == "horse"){?>
					<div class="link-holder">
						<a href="/towns/<?= $this->uri->segment(2) == "alpiyka" ? "horse" : "alpiyka" ?>/city" class="link-to-another">смотреть другой городок</a>
					</div>
					<?}else{?>
					<div class="news">
	            <div class="newsContainer">
	                <?php if(count($this->get('news'))){?>
	                    <?php foreach($this->get('news') as $n){?>
	                        <div class="news_link" id="news_<?php echo $n['id']?>"><span class="date"><?php echo strftime('%b %d',strtotime($n['date_show']))?></span><b><?php echo $n['name']?></b><?php echo $n['description']?> </div>
	                    <?php }?>
	                <?php }?>
	            </div>

	            <div class="next" <?php if(count($this->get('news')) < 2){?>style="display:none"<?php }?>></div>
	            <div class="prew" style="display:none" ></div>
	        </div>
					<?}?>
				</div>
				
    </div>
</div>

</body>
</html>
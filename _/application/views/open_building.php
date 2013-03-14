<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div class="houseCompleteDescription" style="z-index:60010;display:none;">
  <div class="closeBtn" title="Закрыть окно [Кнопка ESC]"></div>
  <div class="hiddenBlock">
      <h1><?php echo $this->get('building.name')?></h1>
      <div class="menu">
					<?php if($this->get('building.swf') || $this->get('building.swf_embed')){?>
              <a href="<?php echo $this->get('url')."/tour3D"?>" id="nav_tour3D" class="menuItem jslink">3D Тур</a>
          <?php }?>
					
          <a href="<?php echo $this->get('url')."/description"?>"  id="nav_description" class="menuItem active jslink">описание проекта</a>

          <?php if(count($this->get('building.plans'))){?>
              <a href="<?php echo $this->get('url')."/floor0"?>"  id="nav_plan1" class="menuItem jslink">поэтажный план</a>
          <?php }?>

          
      </div>

      <div class="content scrollable" id="singleHouse">
          <div class="items">

              <?php if($this->get('building.swf')){?>
                  <div class="contentItem tour3D" id="section_tour3D">
                      <object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" width="860" height="300" id="3D" align="middle">
                          <param name="allowScriptAccess" value="sameDomain" />
                          <param name="movie" value="/user_files/building_swf/<?php echo $this->get('building.swf')?>" /><param name="quality" value="high" /><param name="wmode" value="opaque"><param name="bgcolor" value="#000000" />
                          <embed src="/user_files/building_swf/<?php echo $this->get('building.swf')?>" wmode="opaque" quality="high" bgcolor="#000000" width="860" height="300" name="3d" align="middle" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
                      </object>
                  </div>
              <?php }elseif($this->get('building.swf_embed')){?>
									<div class="contentItem tour3D" id="section_tour3D">
										<div class="text"><div id="panorama<?php echo $this->get('building.id')?>" style="width:860px; height:330px; margin: 0px auto;"></div><!--iframe src="<?php echo $this->get('building.swf_embed')?>" width="860" height="330" style="border:none;" /--></div>
										<script type="text/javascript" charset="utf-8">
											embedpano({xml:"id=<?php echo $this->get('building.swf_embed')?>",target:"panorama<?php echo $this->get('building.id')?>", width:"860px", height:"330px"});
										</script>
									</div>
							<?php }?>
              <div class="contentItem description" id="section_description">
                  <div class="text">
                      <h3>Описание проекта</h3>
                      <?php echo $this->get('building.content')?>
                  </div>
                  <div class="photos">
                      <?php if(is_array($this->get('building.images')) && count($this->get('building.images'))){?>
                          <div class="photo"><img src="/user_files/building_additional_images/middle_<?php echo $this->get('building.images.0.img')?>"  /></div>
                      <?}else{?>
                          <?php if(is_array($this->get('building.place_images')) && count($this->get('building.place_images'))){?>
                              <div class="photo"><img src="/user_files/building_place_images/middle_<?php echo $this->get('building.place_images.0.img')?>"  /></div>
                          <?php }?>
                      <?php }?>

                      <?php if(is_array($this->get('building.place_images')) && count($this->get('building.place_images'))){?>
                          <div id="place_img_container" style="display:none">
                              <?php for($i=0;$i < count($this->get('building.place_images'));$i++){?>
                                  <img id="pi_<?php echo $i?>" src="/user_files/building_place_images/middle_<?php echo $this->get('building.place_images.'.$i.'.img')?>" />
                              <?php }?>
                          </div>
                      <?php }?>
                                            
                       <?php if(is_array($this->get('building.place_images')) && count($this->get('building.place_images'))){?>
                          <div class="listing place_img">
                              <div id="place_img_0" class="active"></div>

                              <?php for($i=1;$i < count($this->get('building.place_images'));$i++){?>
                                  <div id="place_img_<?php echo $i?>"></div>
                              <?php }?>
                          </div>
                      <?}?>

                  </div>
              </div>

              <?php if(count($this->get('building.plans'))){?>
                  <?php foreach($this->get('building.plans') as $i => $bp){?>
                      <div class="contentItem description floor<?php echo $i?>" id="section_floor<?php echo $i?>">
                          <div class="text">
                              <h3><?php echo $bp['name']?></h3>
                              <?php echo nl2br($bp['description'])?>
                          </div>
                          <div class="photos">
                              <div class="photo_floor">
                                  <img src="/user_files/building_plans/middle_<?php echo $bp['img']?>"  />
                                  <div class="zoom"></div>
                              </div>
                              <div class="menu">
                                  <?php foreach($this->get('building.plans') as $j => $bp_links){?>
                                      <a href="<?php echo $this->get('url')."/floor".$j?>" class="menuItem <?php if($this->get('section') == $j)?>active plan<?php echo $j?>menu jslink"><?php echo $bp_links['name']?></a>
                                  <?php }?>
                              </div>

                          </div>
                      </div>
                  <?php }?>
              <?php }?>

          </div><!-- ///// items -->
      </div><!-- ///// scrollable -->

      <!-- "previous page" action -->
      <a class="prev browse left"></a>

      <!-- "next page" action -->
      <a class="next browse right"></a>

  </div>
</div>
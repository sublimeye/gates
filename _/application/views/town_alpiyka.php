<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

<?php $this->load->view('header')?>

<script src="/js/jquery.tools.min.js"></script>
<script src="/js/jquery-ui-1.10.1.custom.min.js"></script>
<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?sensor=true"></script>
    
<script type="text/javascript" src="/js/history.js"></script>
<script type="text/javascript" src="/js/raphael.js"></script>
<script type="text/javascript" src="/js/images.js"></script>


<script>
    window.map_coordinate = "<?php echo $this->get('map_code')?>";
    
    $(document).ready(function()
    {
        var places = $('#json_data').val();
        var zi = new window.Images();
        zi.Init('map_cont','map');

        places = jQuery.parseJSON(places);

        for(var i=0;i<places.length;i++)
        {
            zi.createShape(places[i].params,places[i]);
        }
    });
    
</script>

<div class="links" style="display:none">
    <?php foreach($this->get('links') as $i => $l){?>
        <a class="jslink" id="l<?php echo $i?>_<?php echo $l['id']?>" href="<?php echo $l['href']?>"><?php echo $l['name']?></a>
    <?php }?>
</div>
<h1 style="display:none"><?php echo $this->get('content_name')?></h1>
<div class="content" style="display:none">
   <?php echo $this->get('description')?>
</div>

<div class="outerWrapper">
	<div class="ui-windrose ui-windrose-main js-zoomable g-transform-origin-top-left"></div>

	<div class="wrapper">


			<div class="workArea konik js-workarea" id="city_block" data-active-zone="92,180,1160,750"><!-- альпийка -->

					<div class="allLabels">

							<div id="map">
							</div>
					</div>

					<div class="overlay" style="z-index:60000;display:none;"></div>
			</div>

			<div class="workArea konik noneDisplay" id="infrastructure_block">

					<div class="viewscreen-fixed-wrapper">
							<div class="infrastructureBlock">
									<div class="menu">
											<div id="block_content_one" class="menuItem active">Служба безопасности и сервиса</div>
											<!--div id="block_content_two" class="menuItem">Служба сервиса</div-->
									</div>

									<div class="blockContent one">
											<ul>
												<li>КПП на въезде</li>
												<li>Круглосуточная охрана и патрулирование территории</li>
												<li>Вывоз мусора, сезонные уборка листьев и снега</li>
												<li>Систематическое техническое обслуживание и профилактический ремонт сетей городка</li>
												<li>Обслуживание системы уличного освещения, зон общего пользования</li>
											</ul>
									</div>
									<div class="blockContent two" style="display: none"></div>
							</div>
					</div>

					<div class="allLabels">

							<div class="label bigF beach">
								 Пляж
								 <div class="tail"></div>
							</div>

							<div class="label bigF kpp">
								 КПП
								 <div class="tail"></div>
							</div>

							<div class="label bigF lake">
								 Родниковое озеро
								 <div class="tail"></div>
							</div>

							<div class="label bigF tenis">
									Теннисные корты
								 <div class="tail"></div>
							</div>

							<div class="label bigF playGround">
									Детская площадка
								 <div class="tail"></div>
							</div>

					</div>
			</div>

			<div id="map_block" class="noneDisplay">
				<div id="canvas_holder">
					<div id="map_canvas"></div>
				</div>
			</div>
	</div>
</div>

<div class="houseDescription">
    <div class="closeBtn" title="Закрыть окно [Кнопка ESC]"></div>

    <h3></h3>
    <div class="footage"> <b></b> м<sup>2</sup></div>

    <div class="photo">
    </div>

    <div class="interiorStatus furnished"></div>

    <div class="shortDesc">
    </div>

    <div class="gallery">
    </div>

    <div class="moreBtn">Подробнее</div>
</div>

<textarea style="display:none" id="json_data"><?php echo $this->get('building_places') ?></textarea>

        <div class="cityMenu">
              <a id="link_city" href="<?php echo $this->get('base_url')."towns/alpiyka/city"?>" class="menuItem active jslink">Город</a>
              <a id="link_infrastructure" href="<?php echo $this->get('base_url')."towns/alpiyka/infrastructure"?>" class="menuItem jslink">Инфраструктура</a>
              <a id="link_map" href="<?php echo $this->get('base_url')."towns/alpiyka/map"?>" class="menuItem jslink">Где находится</a>

              <div class="menuFooter"></div>
          </div>
 
<?php $this->load->view('footer')?>

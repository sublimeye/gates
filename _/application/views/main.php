<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

<?php $this->load->view('header')?>

<div class="outerWrapper">
					<div class="ui-windrose ui-windrose-main js-zoomable g-transform-origin-top-left"></div>

	<div class="wrapper">
			<div class="workArea main js-workarea" data-active-zone="500,140,1464,868">

					<div class="viewscreen-fixed-wrapper viewscreen-fixed-wrapper-relative_top">
						<div class="ui-pin pin-bar-beacon">
							<span class="pin-icon pin-icon_horizontal pin-icon_food_horizontal"></span>
							<div class="pin-title"><div class="pin-title-text">Ресторан <br/>&laquo;Маячек&raquo;</div></div>
						</div>

						<div class="ui-pin pin-odessa ui-pin_visible">
							<span class="pin-icon pin-icon_horizontal pin-icon_city_horizontal"></span>
							<div class="pin-title"><div class="pin-title-text pin-title-text_big">Одесса</div></div>
						</div>
					</div>

					<div class="allLabels">
<!-- shadows above towns -->
						<a href="<?php echo $this->get('base_url').'towns/alpiyka/city'?>" class="ui-shaded-link-alpiyka cityAlpiyka" data-make-visible=".js-road-to-alpiyka" data-hover=".pin-alpiyka"></a>
						<div class="ui-shaded ui-shaded-alpiyka"></div>

						<a href="<?php echo $this->get('base_url').'towns/horse/city'?>" class="ui-shaded-link-konyk cityKonik" data-make-visible=".js-road-to-konyk" data-hover=".pin-konyk"></a>
						<div class="ui-shaded ui-shaded-konyk"></div>
<!-- // shadows above towns -->




						<div class="ui-road-highlight ui-road-highlight-alpiyka js-road-to-alpiyka">
							<div class="ui-road-distance">7 км</div>
						</div>

						<div class="ui-road-highlight ui-road-highlight-konyk js-road-to-konyk">
							<div class="ui-road-distance">0.2 км</div>
						</div>

						<div class="ui-pin pin-kyiv ui-pin_visible">
							<span class="pin-icon pin-icon_city"></span>
							<div class="pin-title"><div class="pin-title-text pin-title-text_big">Киев</div></div>
						</div>

						<div class="ui-pin ui-pin_visible pin-konyk" data-make-visible=".js-road-to-konyk">
							<span class="pin-icon pin-icon_town" data-hover=".ui-shaded-konyk"><a href="<?php echo $this->get('base_url').'towns/horse/city'?>" class="ui-pin-link cityKonik"></a></span>
							<div class="pin-title"><div class="pin-title-text pin-title-text_big">Конык</div></div>
						</div>


						<div class="ui-pin ui-pin_visible pin-alpiyka" data-make-visible=".js-road-to-alpiyka">
							<span class="pin-icon pin-icon_town"  data-hover=".ui-shaded-alpiyka"><a href="<?php echo $this->get('base_url').'towns/alpiyka/city'?>" class="ui-pin-link cityAlpiyka"></a></span>
							<div class="pin-title"><div class="pin-title-text pin-title-text_big">Альпийка</div></div>
						</div>



						<div class="ui-pin pin-dynamo">
							<span class="pin-icon pin-icon_football"></span>
							<div class="pin-title"><div class="pin-title-text pin-title-text_double">Футбольные поля <br/>&laquo;Динамо&raquo;</div></div>
						</div>

						<div class="ui-pin pin-megamarket">
							<span class="pin-icon pin-icon_store"></span>
							<div class="pin-title"><div class="pin-title-text">Мегамаркет</div></div>
						</div>

						<div class="ui-pin pin-dnipro-road">
							<span class="pin-icon pin-icon_road"></span>
							<div class="pin-title"><div class="pin-title-text">Днепровское шоссе</div></div>
						</div>

						<div class="ui-pin pin-cp" data-make-visible=".js-road-to-konyk, .js-road-to-alpiyka">
							<span class="pin-icon pin-icon_cp"></span>
							<div class="pin-title"><div class="pin-title-text">КП</div></div>
						</div>

						<div class="ui-pin pin-capital-road">
							<span class="pin-icon pin-icon_road"></span>
							<div class="pin-title"><div class="pin-title-text">Столичное шоссе</div></div>
						</div>

						<div class="ui-pin pin-blue-lake">
							<span class="pin-icon pin-icon_water"></span>
							<div class="pin-title"><div class="pin-title-text">Голубое озеро</div></div>
						</div>

						<div class="ui-pin pin-jukov-reservation">
							<span class="pin-icon pin-icon_reservation"></span>
							<div class="pin-title"><div class="pin-title-text pin-title-text_double">Заповедник <br/>&laquo;Жуков остров&raquo;</div></div>
						</div>

						<div class="ui-pin pin-river-konyk">
							<span class="pin-icon pin-icon_water"></span>
							<div class="pin-title"><div class="pin-title-text">р. Конык</div></div>
						</div>

						<div class="ui-pin pin-domoshpere">
							<span class="pin-icon pin-icon_shopping"></span>
							<div class="pin-title"><div class="pin-title-text">ТЦ &laquo;Домосфера&raquo;</div></div>
						</div>

					</div>
			</div>

	</div><!-- wrapper -->

<!--horizontal-->

					<div class="js-fixed-labels"></div>

</div><!-- outer wrapper -->

<?php $this->load->view('footer')?>

<script>
$(function(){
	$('.ui-road-distance').draggable();
});
</script>

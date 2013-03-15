<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

<?php $this->load->view('header')?>

<div class="outerWrapper">
					<div class="fixLabels">
<!--
							<div class="label bigF obuh1"> Конча-Заспа <div class="line"></div> </div> <div class="label bigF obuh2"> Обухов <div class="line"></div> </div> <div class="label bigF odessa"> Одесса <div class="line"></div> </div> <div class="label bigF cKiev"> Центр Киева <div class="line"></div> <div class="kms">7 км</div> </div> <div class="label bigF cDomosfera"> Домосфера <div class="line"></div> <div class="kms">900 м</div> </div>
-->
					</div>

	<div class="wrapper">
			<div class="workArea main js-workarea" data-active-zone="500,140,1464,868">

					<div class="allLabels">
<!-- shadows above towns -->
						<div class="ui-shaded ui-shaded-alpiyka">
							<a href="<?php echo $this->get('base_url').'towns/alpiyka/city'?>" class="ui-pin-link cityAlpiyka"></a>
						</div>

						<div class="ui-shaded ui-shaded-konyk">
							<a href="<?php echo $this->get('base_url').'towns/horse/city'?>" class="ui-pin-link cityKonik"></a>
						</div>
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
							<span class="pin-icon pin-icon_town"><a href="<?php echo $this->get('base_url').'towns/horse/city'?>" class="ui-pin-link cityKonik"></a></span>
							<div class="pin-title"><div class="pin-title-text pin-title-text_big">Конык</div></div>
						</div>

						<div class="ui-pin ui-pin_visible pin-alpiyka" data-make-visible=".js-road-to-alpiyka">
							<span class="pin-icon pin-icon_town"><a href="<?php echo $this->get('base_url').'towns/alpiyka/city'?>" class="ui-pin-link cityAlpiyka"></a></span>
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

<!--
							<a href="<?php echo $this->get('base_url').'towns/horse/city'?>" class="label bigF cityKonik"> <img src="/img/logoKonik.png" width="79" height="58" alt="" /> <div class="tail"></div> </a> <a href="<?php echo $this->get('base_url').'towns/alpiyka/city'?>" class="label bigF cityAlpiyka"> <img src="/img/logoAlpiyka.png" width="88" height="51" alt="" /> <div class="tail"></div> </a> <div class="label bigF dinamo"> База “Динамо” <div class="tail"></div> </div> <div class="label bigF konikRiver"> Река “Конык” <div class="tail"></div> </div> <div class="label bigF zhOstrov"> Заповедник <br />“Жуков остров” <div class="tail"></div> </div> <div class="label bigF azsShell"> АЗС “Shell” <div class="tail"></div> </div> <div class="label bigF bLake"> Голубое озеро <div class="tail"></div> </div>
-->

					</div>
			</div>

	</div><!-- wrapper -->

<!--horizontal-->

					<div class="js-fixed-labels">
						<div class="ui-pin pin-bar-beacon">
							<span class="pin-icon pin-icon_horizontal pin-icon_food_horizontal"></span>
							<div class="pin-title"><div class="pin-title-text">Ресторан <br/>&laquo;Маячек&raquo;</div></div>
						</div>

						<div class="ui-pin pin-odessa ui-pin_visible">
							<span class="pin-icon pin-icon_horizontal pin-icon_city_horizontal"></span>
							<div class="pin-title"><div class="pin-title-text pin-title-text_big">Одесса</div></div>
						</div>
					</div>

</div><!-- outer wrapper -->

<?php $this->load->view('footer')?>

<script>
$(function(){
	$('.aui-shaded').draggable();
});
</script>

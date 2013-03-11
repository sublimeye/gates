<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

<?php $this->load->view('header')?>

<div class="wrapper">
    <div class="workArea main">
        <div class="fixLabels">
            <div class="label bigF obuh1">
                Конча-Заспа
               <div class="line"></div>
            </div>

            <div class="label bigF obuh2">
               Обухов
               <div class="line"></div>
            </div>

            <div class="label bigF odessa">
               Одесса
               <div class="line"></div>
            </div>

            <div class="label bigF cKiev">
               Центр Киева
               <div class="line"></div>
               <div class="kms">7 км</div>
            </div>
					 <div class="label bigF cDomosfera">
               Домосфера
               <div class="line"></div>
               <div class="kms">900 м</div>
            </div>
        </div>

        <div class="allLabels">

            <a href="<?php echo $this->get('base_url').'towns/horse/city'?>" class="label bigF cityKonik">
               <img src="/img/logoKonik.png" width="79" height="58" alt="" />
               <div class="tail"></div>
            </a>


            <a href="<?php echo $this->get('base_url').'towns/alpiyka/city'?>" class="label bigF cityAlpiyka">
               <img src="/img/logoAlpiyka.png" width="88" height="51" alt="" />
               <div class="tail"></div>
            </a>

           <div class="label bigF dinamo">
               База “Динамо”
               <div class="tail"></div>
            </div>

            <div class="label bigF konikRiver">
               Река “Конык”
               <div class="tail"></div>
            </div>

            <div class="label bigF zhOstrov">
               Заповедник <br />“Жуков остров”
               <div class="tail"></div>
            </div>

						<div class="label bigF azsShell">
               АЗС “Shell”
               <div class="tail"></div>
            </div>
						
						<div class="label bigF bLake">
               Голубое озеро
               <div class="tail"></div>
            </div>
        </div>
    </div>

</div>

<?php $this->load->view('footer')?>
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<!-- Строка навигации -->
 <div class="navstr"><b>Дома на карте</b></div>

 <!-- Поиск, кнопы и т.д. -->
 <div class="findpane">
 <table width="100%" height="26" cellspacing="0" class="findes">
   <tr>
 <td>
 </td>
 <td width="109">
 <!-- Кнопки функциональные -->
     <div class="funcbuts">
     </div>
 </td>
   </tr>
 </table>
 </div>


 </div>
 </div>

<div class="contdiv listTemplate" id="contentdiv">

<table width="100%" cellspacing="0" class="listtable">

  <tr>
    <td width="2"></td>

   <th title="Имя">
        <?php if($this->get('sort') != 'name'){?>
            <a href="/<?php echo $this->get('url')?>/sort/name/type/asc">Имя</a>
		<?php }else{?>
	        <a class="sort_arrow_<?php echo $this->get('sort_type')?>" href="/<?php echo $this->get('url')?>/sort/name/type/<?php echo $this->get('sort_type_next')?>">Имя</a>
		<?}?>
   </th>

   <th title="ID">
        <?php if($this->get('sort') != 'id'){?>
            <a href="/<?php echo $this->get('url')?>/sort/id/type/asc">ID</a>
		<?php }else{?>
	        <a class="sort_arrow_<?php echo $this->get('sort_type')?>" href="/<?php echo $this->get('url')?>/sort/id/type/<?php echo $this->get('sort_type_next')?>">ID</a>
		<?}?>
   </th>

  </tr>

  <?php foreach($this->get('towns') as $n){?>
      <tr>
        <td class="tw"></td>

        <td title="Имя" valign="middle" class="listtr" align="left">
           <b><a href="/backend/building_place/edit/id/<?php echo $n['id']?>" title="Редактировать"><?php echo $n['name']?></a></b>
        </td>

        <td title="ID" valign="middle" class="listtr" align="left">
            <b><?php echo $n['id']?></b>
        </td>

      </tr>
  <?php }?>

</table>

</div>

</body>

</html>
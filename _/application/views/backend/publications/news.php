<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<!-- Строка навигации -->
 <div class="navstr"><b>Новости</b></div>

 <form method='post' action="/backend/publications/delete">

 <!-- Поиск, кнопы и т.д. -->
 <div class="findpane">
 <table width="100%" height="26" cellspacing="0" class="findes">
   <tr>
 <td>
 </td>
 <td width="109">
 <!-- Кнопки функциональные -->
     <div class="funcbuts">
         <div class="but" style="left: 0px;" id="add"><a href="/backend/publications/add"><img src="/img/backend/but_add.gif" border="0" vspace="9"></a></div>
         <div class="but" style="left: 37px;" id="delete"><input class="delete_btn" type="submit" value="" /></div>
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

  <th title="URL">
        <?php if($this->get('sort') != 'url_alias'){?>
            <a href="/<?php echo $this->get('url')?>/sort/url_alias/type/asc">URL</a>
		<?php }else{?>
	        <a class="sort_arrow_<?php echo $this->get('sort_type')?>" href="/<?php echo $this->get('url')?>/sort/url_alias/type/<?php echo $this->get('sort_type_next')?>">URL</a>
		<?}?>
   </th>

  <td width="2" class="tw"></td>
   <td align="center" valign="middle" width="35" class="whitebg">&nbsp;</td>
  </tr>

  <?php foreach($this->get('news') as $n){?>
      <tr>
        <td class="tw"></td>

        <td title="Имя" valign="middle" class="listtr" align="left">
           <b><a href="/backend/publications/edit/id/<?php echo $n['id']?>" title="Редактировать"><?php echo $n['name']?></a></b>
        </td>

        <td title="ID" valign="middle" class="listtr" align="left">
            <b><?php echo $n['id']?></b>
        </td>

        <td title="URL" valign="middle" class="listtr" align="left">
            <?php echo "/".$n['url_alias']?>
        </td>

        <td class="tw"></td>
        <td align="right" valign="middle" class="whitebg" width="35">
            <label for="ch_<?php echo $n['id']?>" title="Выбрать">&nbsp;</label>
            <input name="items[]" id="ch_<?php echo $n['id']?>" type="checkbox" value="<?php echo $n['id']?>" class="crirHiddenJS" />
        </td>
      </tr>
  <?php }?>

</table>

 </form>

</div>

</body>

</html>
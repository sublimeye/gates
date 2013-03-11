<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<!-- Строка навигации -->
<div class="navstr"><b>Страницы</b></div>

 <div class="findpane">
        <table width="100%" height="26" cellspacing="0" class="findes">
          <tr>
            <td>&nbsp;</td>
            <td width="109">
<!-- Кнопки функциональные -->
                <div class="funcbuts">
                    <div class="but" style="left: -37px;" ><img url="/backend/pages/add" butt="sub_cat_btn_add" src="/img/backend/but_catAdd.gif" border="0" vspace="9" title="Добавить страницу"></div>
                    <div class="but" style="left: 0px;" ><img url="/backend/pages/edit" butt="sub_cat_btn_edit" src="/img/backend/but_catEdit.gif" border="0" vspace="9" title="Редактировать страницу"></div>
                    <div class="but" style="left: 37px;" ><img url="/backend/pages/delete" butt="sub_cat_btn_delete" src="/img/backend/but_catDelete.gif" border="0" vspace="9" title="Удалить страницу"></div>
                </div>
            </td>
          </tr>
        </table>
 </div>

</div>
</div>
<div class="contdiv listTemplate" id="contentdiv">

<div id="tree_ul">
<?=$this->get('items')?>
</div>
	
</div>


<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<!-- Строка навигации -->
<div class="navstr">
    <div class="navstr"><b><a href="/<?php echo $this->get('url')?>">Дома</a></b>: Добавление/Редактирование
    </div>
</div>

<?php if($this->get('action') == 'edit'){?>
    <div class="tabs">
        <?php if($this->get('active_tab') == 0){?><div class="active">Информация</div><?php }else{?><a href="/backend/buildings/edit/id/<?php echo $this->get('id')?>/tab/0">Информация</a><?php }?>
        <?php if($this->get('active_tab') == 1){?><div class="active">Поэтажный план</div><?php }else{?><a href="/backend/buildings/edit/id/<?php echo $this->get('id')?>/tab/1">Поэтажный план</a><?php }?>
        <?php if($this->get('active_tab') == 2){?><div class="active">Галерея фотографий</div><?php }else{?><a href="/backend/buildings/edit/id/<?php echo $this->get('id')?>/tab/2">Галерея фотографий</a><?php }?>
    </div>
<?}?>


</div>
</div>

<form enctype="multipart/form-data" method="post" action="/<?php echo $this->get('url_action')?>" >
<div class="findpane findpaneDinamo">
        <table width="100%" height="26" cellspacing="0" class="findes">
          <tr>
            <td>&nbsp;</td>
            <td width="109">
<!-- Кнопки функциональные -->
                <div class="funcbuts">
                    <div class="but" style="left: 37px;" id="save"><input class="save_btn" type="submit" value="" /></div>
                </div>
            </td>
          </tr>
        </table>
    </div>

<div class="contdiv listTemplate <?php if($this->get('action') == "edit"){?>withTabs<?php }?>" id="contentdiv">
    <div class="contdiv inTab">

    <div align="right" class="itemlabel">
        Поэтажный план
        <input type="button" id="add_floor" value="Добавить этаж" class="buttybut" />
    </div>
    <div class="itemCont">
    <?php if(!is_array($this->get('building_plans_items')) || !count($this->get('building_plans_items'))){?>
        <div class="item floor" is_save="false">
            <input type="hidden" name="ids[]"  value="0">

            <div align="right" class="itemlabel">
                Название
            </div>
            <div valign="top" class="item itemNested">
                <input name="name[]">
            </div><br />

            <div align="right" class="itemlabel">
                Описание
            </div>
            <div class="item itemNested">
              <textarea name="description[]" rows="5"></textarea>
            </div> <br />

            <div align="right" class="itemlabel">
                Картинка
            </div>
            <div class="item">
                <input type="file" name="img[]" />
            </div><br />

            <div align="right" class="itemlabel">
                Сортировка
            </div>
            <div class="item itemNested">
                <input class="smallInput" name="sort_order[]"  value="0">
            </div><br />
        </div>
    <?php }else{?>
        <?php foreach($this->get('building_plans_items') as $k => $bpi){?>
            <div class="item floor">
                <input type="hidden" name="ids[]"  value="<?php echo $bpi['id']?>">

                <div align="right" class="itemlabel">
                    Название
                </div>
                <div valign="top" class="item itemNested">
                    <input name="name[]"  value="<?php echo $bpi['name']?>">
                </div><br />

                <div align="right" class="itemlabel">
                    Описание
                </div>
                <div class="item itemNested">
                    <textarea name="description[]" rows="5"><?php echo $bpi['description']?></textarea>
                </div> <br />

                <div align="right" class="itemlabel">
                    Картинка
                </div>
                <div class="item">
                    <input type="file" name="img[]" />
                    <?php if($bpi['img'] != ''){?>
                        <img width="200" src="/user_files/building_plans/<?php echo $bpi['img']?>"><br>
                    <?}?>
                </div><br />

                <div align="right" class="itemlabel">
                    Сортировка
                </div>
                <div class="item itemNested">
                        <input class="smallInput" name="sort_order[]"  value="<?php echo $bpi['sort_order']?>">
                </div><br />
                <a class="delete_link" href="/backend/buildings/delete_building_plan/id/<?php echo $bpi['id']?>" class="delete">Удалить</a>
            </div>
        <?php }?>
    <?php }?>
    </div>
    </div>
    </div>

 </form>

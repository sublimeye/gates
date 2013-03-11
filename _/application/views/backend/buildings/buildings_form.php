<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>
<script type="text/javascript" src="/js/backend/ckeditor/ckeditor.js"></script>

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

<form enctype="multipart/form-data" method="post" action="/<?php echo $this->get('url_action')?>" >
<div class="findpane">
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

</div>
</div>

<div class="contdiv listTemplate <?php if($this->get('action') == "edit"){?>withTabs<?php }?>" id="contentdiv">
    <div class="contdiv inTab">

        <div align="right" class="itemlabel">
            Название
        </div>
        <div class="item itemNested">
        <input name="name"  value="<?php echo $this->get('name')?>">
        </div><br />

        <div align="right" class="itemlabel">
            URL Alias
        </div>
        <div class="item itemNested">
            <input name="url_alias"  value="<?php echo $this->get('url_alias')?>">
        </div>
        <br/>

        <div align="right" class="itemlabel">
            Городок
        </div>
        <div valign="top" class="item">
            <select name="town_id" size="1">
                <?php foreach($this->get('towns') as $c){?>
                    <option value="<?=$c['id']?>" <?if($c['id'] == $this->get('town_id')){?> selected="selected"<?}?> ><?=$c['name']?></option>
                <?php }?>
            </select>
        </div>
        <br>

        <div align="right" class="itemlabel">
            Площадь
        </div>
        <div class="item itemNested">
        <input class="smallInput" name="square"  value="<?php echo $this->get('square')?>"> м<sup>2</sup>
        </div><br />

        <div align="right" class="itemlabel">
            Краткое описание
        </div>
        <div class="item itemNested">
        <textarea name="description" rows="5"><?php echo $this->get('description')?></textarea>
        </div> <br />

        <div align="right" class="itemlabel">
            Контент
        </div>
        <div class="item itemNested">
            <textarea id="content" rows="10" name="content"><?php echo $this->get('content')?></textarea>
        </div>
        <br/>

        <div align="right" class="itemlabel">
            Title
        </div>
        <div valign="top" class="item">
            <input name="seo_title" value="<?=htmlspecialchars($this->get('seo_title'))?>">
        </div>
        <br>

        <div align="right" class="itemlabel">
            Keywords
        </div>
        <div valign="top" class="item">
            <input name="seo_keywords" value="<?=htmlspecialchars($this->get('seo_keywords'))?>">
        </div>
        <br>

        <div align="right" class="itemlabel">
            Description
        </div>
        <div valign="top" class="item">
            <textarea name="seo_description" rows="4"><?=$this->get('seo_description')?></textarea>
        </div>
        <br>

        <div align="right" class="itemlabel">
            Заглавная картинка
        </div>
        <div class="item">
            <input type="file" name="img" />
            <?php if($this->get('img') != ''){?>
                <img width="200" src="/user_files/building/<?=$this->get('img')?>"><br>
                <a href="/backend/buildings/file_delete/item/<?php echo $this->get('id')?>/name/img" class="pseudolink pldelete">Удалить</a>
            <?}?>
        </div><br />

         <div align="right" class="itemlabel">
            3D тур
        </div>
        <div class="item">
            <input type="file" name="swf" />
            <?php if($this->get('swf') != ''){?>
                <a class="pseudolink pldelete" target="_blank" href="/user_files/building_swf/<?=$this->get('swf')?>">Просмотр</a>&nbsp;
                <a href="/backend/buildings/file_delete/item/<?php echo $this->get('id')?>/name/swf" class="pseudolink pldelete">Удалить</a>
            <?}?>
        </div><br />
				
				<div align="right" class="itemlabel">
            3D тур ID <br />
						для eye.cityi.com.ua
        </div>
        <div class="item">
            <!--textarea name="swf_embed" rows="6"><?=$this->get('swf_embed')?></textarea-->
						<input type="text" name="swf_embed" value="<?=$this->get('swf_embed')?>" id="swf_embed" />
        </div><br />
    </div>
    </div>
</div>
 </form>

</body>

</html>
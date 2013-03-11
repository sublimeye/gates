<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>
<script type="text/javascript" src="/js/backend/ckeditor/ckeditor.js"></script>

<!-- Строка навигации -->
<div class="navstr">
    <div class="navstr"><b><a href="/<?php echo $this->get('url')?>">Страницы</a></b>: Добавление/Редактирование
    </div>
</div>

<form method="post" action="/<?php echo $this->get('url_action')?>" >
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

<div class="contdiv listTemplate" id="contentdiv">

    <div class="contdiv inTab">
        <div align="right" class="itemlabel">
            Имя
        </div>
        <div class="item itemNested">
            <input name="name" value="<?php echo $this->get('name')?>">
        </div>
        <br/>

        <div align="right" class="itemlabel">
            Родительская страница
        </div>
        <div valign="top" class="item">
            <select name="parent_id" size="1">
                <option value="0">--/</option>
                <?php foreach($this->get('pages_list') as $c){?>
                    <option value="<?=$c['id']?>" <?if($c['id'] == $this->get('selected_id')){?> selected="selected"<?}?> ><?=$c['name']?></option>
                <?php }?>
            </select>
        </div>
        <br>

        <div align="right" class="itemlabel">
            URL Alias
        </div>
        <div class="item itemNested">
            <input name="url_alias"  value="<?php echo $this->get('url_alias')?>">
        </div>
        <br/>

        <div align="right" class="itemlabel">
            Краткое описание
        </div>
        <div class="item itemNested">
            <textarea name="description" rows="7"><?php echo $this->get('description')?></textarea>
        </div>
        <br/>

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
            Позиция
        </div>
        <div valign="top" class="item">
                <input name="sort_order" value="<?=$this->get('sort_order')?>">
        </div>
        <br>

        <div align="right" class="itemlabel">
            Отображать в меню
        </div>
        <div valign="top" class="item">
            <label for="CH_2" title="Выбрать">Да/Нет</label>
            <input name="isMenu" id="CH_2" type="checkbox" <?if($this->get('isMenu') == STATUS_ENABLED || is_null($this->get('isMenu'))){?>checked="checked"<?}?> value="<?=STATUS_ENABLED?>" class="crirHiddenJS" />
        </div>
         <br>
        <div align="right" class="itemlabel">
            Включен
        </div>
        <div valign="top" class="item">
            <label for="CH_1" title="Выбрать">Включен/Выключен</label>
            <input name="enabled" id="CH_1" type="checkbox" <?if($this->get('enabled') == STATUS_ENABLED || is_null($this->get('enabled'))){?>checked="checked"<?}?> value="<?=STATUS_ENABLED?>" class="crirHiddenJS" />
        </div>

    </div>
</div>
 </form>

</body>

</html>
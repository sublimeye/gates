<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>
<script type="text/javascript" src="/js/backend/ckeditor/ckeditor.js"></script>

<!-- Строка навигации -->
<div class="navstr">
    <div class="navstr"><b><a href="/<?php echo $this->get('url')?>">Городки</a></b>: Редактирование
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
            Контент
        </div>
        <div class="item itemNested">
            <textarea id="content" rows="10" name="description"><?php echo $this->get('description')?></textarea>
        </div>
        <br/>

        <div align="right" class="itemlabel">
            GPS координаты
        </div>
        <div valign="top" class="item">
            <textarea name="map_code" rows="4"><?=$this->get('map_code')?></textarea>
        </div>
        <br>

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

    </div>
</div>
 </form>

</body>

</html>
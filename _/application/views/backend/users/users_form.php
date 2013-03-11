<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>
<!-- Строка навигации -->
<div class="navstr">
    <div class="navstr"><b><a href="/<?php echo $this->get('url')?>">Пользователи</a></b>: Добавление/Редактирование
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
            E-mail
        </div>
        <div class="item itemNested">
            <input  name="email" autocomplete="off" value="<?php echo $this->get('email')?>">
        </div>
        <br/>

        <div align="right" class="itemlabel">
            Пароль
        </div>
        <div class="item itemNested">
            <input type="password" name="password" autocomplete="off" value="">
        </div>
        <br/>

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
<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>
<div class="footdiv" id="footer">
    <!-- Листалка -->
    <?if (!$this->get('simple')) { ?>
    <div class="scroll">
        <table cellpadding="0" width="100%">
            <tr>
                <td width="30%">Всего: <?=$this->get('total_items')?></td>
                <?
                if ($this->get('all_page') > 0) {
                    echo '<td width="40%" align="center" class="scrolltd">';

                    if ($this->get('active_page') >= 1) {
                        if ($this->get('active_page') > 1) {
                            echo '<a class="prew" href="/' . $this->get('url') . '/page/' . ($this->get('active_page') - 1) . $this->get('par') . '">&laquo;</a>';
                        }
                        else
                        {
                            echo '<a class="prew" href="/' . $this->get('url') . $this->get('par') . '">&laquo;</a>';
                        }
                    }
                    else
                    {
                        echo '<span class="prew">&laquo</span>';
                    }

                    if ($this->get('all_page') > 12) {
                        $begin_range = $this->get('active_page') - 5;
                        $end_range = $this->get('active_page') + 4;

                        if ($begin_range < 3)
                            $begin_range = 1;

                        if ($end_range < 9)
                            $end_range = 9;

                        if ($this->get('all_page') - $this->get('active_page') < 6)
                            $end_range = $this->get('all_page');
                    }
                    else
                    {
                        $begin_range = 1;
                        $end_range = $this->get('all_page');
                    }

                    echo ($this->get('active_page') > 0)
                            ? '<a href="/' . $this->get('url') . $this->get('par') . '">1</a>' : '<span>1</span>';

                    if ($begin_range > 1) {
                        echo '<a href="/' . $this->get('url') . '/page/2' . $this->get('par') . '">2</a><span>&hellip;</span>';
                    }

                    for ($i = $begin_range; $i <= $end_range; $i++)
                    {
                        $page_out = $i + 1;
                        echo ($i != $this->get('active_page'))
                                ? '<a href="/' . $this->get('url') . "/page/" . $i . $this->get('par') . '">' . $page_out . '</a>'
                                : '<span class="current">' . $page_out . '</span>';
                    }

                    if ($end_range < $this->get('all_page')) {
                        echo '<span>&hellip;</span><a href="/' . $this->get('url') . "/page/" . ($this->get('all_page') - 1) . $this->get('par') . '">' . $this->get('all_page') . '</a>';
                        echo '<a href="/' . $this->get('url') . "/page/" . $this->get('all_page') . $this->get('par') . '">' . ($this->get('all_page') + 1) . '</a>';
                    }

                    if ($this->get('active_page') != $this->get('all_page')) {
                        echo '<a class="next" href="/' . $this->get('url') . "/page/" . ($this->get('active_page') + 1) . $this->get('par') . '">&raquo;</a>';
                    }
                    else
                    {
                        echo '<span class="next">&raquo;</span>';
                    }

                    echo "</td>";
                }
                ?>
                <td width="30%" align="right">
                    <form method="post" id="limit_form" action="/backend/authorization/set_limit">
                        Показывать по <input class="scrollinp" id="limit_input" name="limit"
                                             value="<?=$this->get('limit')?>" maxlength="3"/> на странице&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    </form>
                </td>
            </tr>
        </table>
    </div>

    <div class="subservice">
        <form method="post" action="/backend/authorization/set_language">
            <select name="language" size="1">
                <option value="1" <?if ($this->get('language') == 1) { ?>selected="selected"<? }?> >Русский</option>
                <option value="2" <?if ($this->get('language') == 2) { ?>selected="selected"<? }?> >English</option>
            </select>
            <input type="submit" value="Перейти"/>
        </form>
    </div>
    <?}?>
</div>
</div>

<?$errors = $this->session->flashdata('errors');
if (!empty($errors)) {
    ?>
<div class="overlay" id="modal_win_overlay"></div>
<div class="modalWin error" id="modal_win_container">
    <h2>Ошибка</h2>
    <? echo $errors?>

    <div class="modalButton">
        <input type="button" id="close_modal_win_btn" value="Ok"/>
    </div>
</div>
<? } ?>

<?$warnings = $this->session->flashdata('warnings');
if (!empty($warnings)) {
    ?>
<div class="overlay" id="modal_win_overlay"></div>
<div class="modalWin warning" id="modal_win_container">
    <h2>Предупреждение</h2>
    <?=$warnings?>
    <div class="modalButton">
        <input type="button" id="close_modal_win_btn" value="Ok"/>
    </div>
</div>
<? } ?>

</body>
</html>
<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>
<html>
<head>

    <title>Панель администрирования</title>
    <META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
    <LINK REL="StyleSheet" HREF="/css/backend/all.css" TYPE="text/css">

</head>
<body>

<div id="enterPage">

    <div id="Splash">
        <table cellspacing="0" cellpadding="7" border="0">
            <tbody>
            <tr>
                <td align="center" colspan="2"><!--<img alt="" src="/img/backend/logo-saver.png">--><br/><br/></td>
            </tr>
            <form class="go" action="/backend/authorization/login" method="post">

                <tr class="footergo">
                    <td align="right">Логин:&nbsp;</td>
                    <td><input type="text" class="in" name="login"></td>
                </tr>
                <tr class="footergo">
                    <td align="right">Пароль:&nbsp;</td>
                    <td><input type="password" class="in" name="password"></td>

                </tr>
                <tr class="footergo">
                    <td>&nbsp;</td>
                    <td><input type="submit" class="go" value="Войти" name="dologin">
                    </td>
                </tr>
                <tr>
                    <td colspan="2"><?php echo $this->session->flashdata('errors')?></td>
                </tr>
            </form>
            </tbody>
        </table>


    </div>
</div>

</body>

</html>

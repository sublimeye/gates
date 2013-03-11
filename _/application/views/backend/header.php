<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>
<html>
<head>
    <title>Панель администрирования</title>
    <META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
    <link rel="StyleSheet" href="/css/backend/all.css" TYPE="text/css">
    <link href="/js/ui/jquery-ui.css" rel="stylesheet" TYPE="text/css">

    <script language="javascript" type="text/javascript" src="/js/jquery.js"></script>
    <script language="JavaScript" type="text/javascript" src="/js/backend/crir.js"></script>
    <script language="JavaScript" type="text/javascript" src="/js/backend/all.js"></script>

</head>
<body>
<div id="main_container">
    <div class="headdiv">
        <div class="headbg">

            <table width="100%" cellspacing="0">
                <tr>
                    <td class="logoT"><!--<img src="/img/backend/logo-saver.png" width="140" alt=""/>-->
                    </td>

                    <td id="main_menu">

                        <div class="menuitem<?php if($this->get('active_section') == 'users') echo "_active"?>">
                            <a href="/backend/users/get_list"><img src="/img/backend/ico1.png" width="44" height="44" class="menuico"/><br>Пользователи</a>
                        </div>

                        <div class="menuitem<?php if($this->get('active_section') == 'pages') echo "_active"?>">
                            <a href="/backend/pages/get_list"><img src="/img/backend/ico2.png" width="44" height="44" class="menuico"/><br>Страницы</a>
                        </div>

                        <div class="menuitem<?php if($this->get('active_section') == 'news') echo "_active"?>">
                            <a href="/backend/publications/get_list"><img src="/img/backend/ico3.png" width="44" height="44" class="menuico"/><br>Новости</a>
                        </div>

                        <div class="menuitem<?php if($this->get('active_section') == 'towns') echo "_active"?>">
                            <a href="/backend/towns/get_list"><img src="/img/backend/ico6.png" width="44" height="44" class="menuico"/><br>Городки</a>
                        </div>

                        <div class="menuitem<?php if($this->get('active_section') == 'buildings') echo "_active"?>">
                            <a href="/backend/buildings/get_list"><img src="/img/backend/ico4.png" width="44" height="44" class="menuico"/><br>Дома</a>
                        </div>

                        <div class="menuitem">
                            <a href="/backend/building_place/get_list"><img src="/img/backend/ico5.png" width="44" height="44" class="menuico"/><br>Дома на карте</a>
                        </div>
                    </td>

                    <td align="right">
                        <div class="menuitemoff"><a href="/backend/authorization/logout"><img src="/img/backend/ico7.png" class="menuico"/><br>Выйти</a>
                        </div>
                    </td>
                </tr>
            </table>
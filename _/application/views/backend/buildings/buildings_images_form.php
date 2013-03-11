<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>
<script type="text/javascript" src="/js/backend/highslide/highslide-with-gallery.js"></script>
<link rel="stylesheet" type="text/css" href="/js/backend/highslide/highslide.css" />

<script type="text/javascript">
    hs.graphicsDir = '/js/backend/highslide/graphics/';
    hs.align = 'center';
    hs.transitions = ['expand', 'crossfade'];
    hs.outlineType = 'rounded-white';
    hs.fadeInOut = true;
    hs.dimmingOpacity = 0.8;
    hs.zIndexCounter = 200000;


    hs.addSlideshow({
        interval: 5000,
        repeat: false,
        useControls: true,
        fixedControls: 'fit',
        overlayOptions: {
            opacity: 0.75,
            position: 'bottom center',
            hideOnMouseOut: true
        }
    });

</script>

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
        Добавление фото
    </div>
    <div class="item">
        <form action="/<?php echo $this->get('url_action')?>" method="post" enctype="multipart/form-data" >
            <input name="img" type="file" style="width: 220px"/>
            <input class="buttybut" style="width: 200px" type="submit" value="Добавить" />
    </div><br />

    <div align="right" class="itemlabel">
        Фото
    </div>
    <div class="item itemNested">
          <?php foreach($this->get('building_images_items') as $bii){?>
            <div class="fotodiv">
                <a class="highslide" onclick="return hs.expand(this)" href="/user_files/building_additional_images/<?php echo $bii['img']?>"><img height="80" align="middle" border="0" src="/user_files/building_additional_images/<?php echo $bii['img']?>"></a>
                <a href="/backend/buildings/delete_building_image/id/<?php echo $bii['id']?>" class="delete">Удалить</a>
            </div>
          <?php }?>
    </div><br />

    </div>
    </div>
</div>
 </form>

</body>

</html>
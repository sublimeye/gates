<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<script type="text/javascript" src="/js/backend/raphael.js"></script>
<script type="text/javascript" src="/js/backend/zones_image.js"></script>

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

<script>
    $(document).ready(function()
    {
        $('#map').load(function()
        {
            var map_cont = $('#map_cont');
            var map = $('#map');
            var form_column = $('#form_column_general');
            var screen_width = $(document).width();
            var delta = $(map).width() + $(form_column).width() + 20;
            var places = $('#json_data').val();

            if(screen_width > delta)
            {
                $(map_cont).css('width',$(map).width() + 20);
                $(form_column).css('left',$(map_cont).width());
                $('#form_column_images').css('left',$(map_cont).width());
            }
            else
            {
                $(map_cont).css('width',screen_width - $(form_column).width() - 20);
                $(form_column).css('left',$(map_cont).width());
                $('#form_column_images').css('left',$(map_cont).width());
            }

            var zi = new window.Zones_image();
            zi.Init('map_cont','map');

            places = jQuery.parseJSON(places);

            for(var i=0;i<places.length;i++)
            {
                zi.createShape(places[i].params,false,places[i]);
            }

            zi.subscribe('ondelete',function(shape)
            {
                if(shape.attr != undefined)
                {
                    if(shape.is_confirm_delete == undefined)
                    {
                        $('div.overlay').css('display','block');
                        $('div.warning').css('display','block');

                        return false;
                    }
                }

                return true;
            });

            zi.subscribe('onselect',function(index,shape)
            {
                if(shape.attr != undefined)
                {
                    $('#form_column_general').find(':input').each(function()
                    {
                        if(shape.attr.building_id != undefined && $(this).attr('name') == 'building_id')
                        {
                            $(this).find('option[value="'+ shape.attr.building_id +'"]').attr("selected", "selected");

                        }

                        if(shape.attr.enabled != undefined && $(this).attr('name') == 'enabled')
                        {
                            if(shape.attr.enabled == 1)
                            {
                                $(this).prev().attr('class','checkbox_checked');
                                $(this).attr('checked', true);
                            }
                            else
                            {
                                $(this).prev().attr('class','checkbox_unchecked');
                                $(this).removeAttr('checked');
                            }
                        }

                        if(shape.attr.availability != undefined && $(this).attr('name') == 'availability')
                        {
                            $(this).find('option[value="'+ shape.attr.availability +'"]').attr("selected", "selected");

                        }

                        if(shape.attr.state != undefined && $(this).attr('name') == 'state')
                        {
                            $(this).find('option[value="'+ shape.attr.state +'"]').attr("selected", "selected");

                        }

                        if(shape.attr.interior != undefined && $(this).attr('name') == 'interior')
                        {
                            $(this).find('option[value="'+ shape.attr.interior +'"]').attr("selected", "selected");

                        }
                    });

                    if(shape.attr.id != undefined)
                    {
                        $('div.tabs').find('div:hidden').css('display','inline');
                    }
                }
                else
                {
                    $('#form_column_general').find(':input').each(function()
                    {
                        if($(this).attr('type') == 'checkbox')
                        {
                            $(this).prev().attr('class','checkbox_checked');
                            $(this).attr('checked', true);
                        }
                        else
                        {
                            $(this).find('option[value="0"]').attr("selected", "selected");
                        }
                    });
                }

                $('#form_column_images').css('display','none');
                $('#form_column_general').css('display','block');

                $.cookie("active_shape_id",shape.attr.id);
                $.cookie("active_shape_tab",'head_tab_general');
            });

            zi.subscribe('ondeselect',function()
            {
                $('#form_column_general').css('display','none');
                $('#form_column_images').css('display','none');

                $.cookie("active_shape_id",null);
                $.cookie("active_shape_tab",null);
            });

            var active_shape_id = $.cookie("active_shape_id");
            var active_tab_id = $.cookie("active_shape_tab");

            if(active_shape_id != undefined)
            {
                var shape = zi.findShapeByAttr('id',active_shape_id);

                if(shape != undefined)
                {
                    zi.setActive(shape);
                }
                else
                {
                    $.cookie("active_shape_id",null);
                }
            }

            $('#save_general').click(function()
            {
                var shape = zi.getActive();

                if(shape != undefined)
                {
                    var values = {};

                    $('#form_column_general').find(':input').each(function()
                    {
                        if($(this).attr('name') != undefined)
                        {
                            if($(this).attr('type') == 'checkbox')
                            {
                                values[$(this).attr('name')] = ($(this).is(':checked')) ? 1 : 0;
                            }
                            else
                            {
                                values[$(this).attr('name')] = $(this).val();
                            }
                        }
                    });

                    if(values.building_id == 0)
                    {
                        $('#form_column_general').find(':input[name="building_id"]').attr('class','error');
                        return false;
                    }

                    values['points'] = shape.points;

                    if(shape.attr != undefined && shape.attr.id != undefined)
                    {
                        var url = "/backend/building_place/action_update";
                        values['id'] = shape.attr.id;
                    }
                    else
                    {
                        var url = "/backend/building_place/action_add";
                    }

                    $.ajax(
                    {
                        url			 : url,
                        type		 : 'post',
                        dataType     : "json",
                        async		 : false,
                        data		 : values,
                        success	     : function (data, textStatus)
                        {
                           if(data > 0)
                           {
                               shape.attr = values;
                               shape.attr.id = data;
                           }
                        }
                    });
                }
            })

            $('#no_btn').click(function()
            {
                $('div.overlay').css('display','none');
                $('div.warning').css('display','none');
            });

            $('#yes_btn').click(function()
            {
                var shape = zi.getActive();

                if(shape != undefined && shape.attr != undefined)
                {
                    $.ajax(
                    {
                        url			 : '/backend/building_place/action_delete',
                        type		 : 'post',
                        dataType     : "json",
                        async		 : false,
                        data		 : {'id' : shape.attr.id},
                        success	     : function (data, textStatus)
                        {
                           if(data == true)
                           {
                               $('div.overlay').css('display','none');
                               $('div.warning').css('display','none');

                              shape.is_confirm_delete = true;
                              zi.remove_shape();

                              $.cookie("active_shape_id",null);
                              $.cookie("active_shape_tab",null);
                           }
                        }
                    });
                }
            });

            $('#head_tab_images').click(function()
            {
                var shape = zi.getActive();

                if(shape != undefined)
                {
                    $('#image_cont').children('div.fotodiv').each(function()
                    {
                       if($(this).attr('id') != 'clone_image_cont')
                       {
                           $(this).remove();
                       }
                    });
                    console.log(shape);
                    if(shape.attr != undefined && shape.attr.images != undefined && shape.attr.images.length)
                    {
                        var clone_img_cont = $('#clone_image_cont');

                        for(var i=0;i<shape.attr.images.length;i++)
                        {
                           var new_image = $(clone_img_cont).clone();

                           $(new_image).removeAttr('id');
                           $(new_image).css('display','block');

                           $(new_image).find('a.highslide').attr('href',"/user_files/building_place_images/" + shape.attr.images[i]['img']);
                           $(new_image).find('img').attr('src',"/user_files/building_place_images/" + shape.attr.images[i]['img']);
                           $(new_image).find('a.delete').attr('href',$(new_image).find('a.delete').attr('href') + shape.attr.images[i]['id']);

                           $(new_image).appendTo($('#image_cont'));
                        }
                    }

                    $('#form_column_general').css('display','none');
                    $('#form_column_images').css('display','block');

                    $.cookie("active_shape_tab",'head_tab_images');
                }
            });

            $('#head_tab_general').click(function()
            {
                $('#form_column_images').css('display','none');
                $('#form_column_general').css('display','block');

                $.cookie("active_shape_tab",'head_tab_general');
            });

            $('#image_upload_form').submit(function()
            {
               var shape = zi.getActive();

               if(zi != undefined && shape.attr != undefined && shape.attr.id != undefined)
               {
                   $('#building_place_id').val(shape.attr.id);
               }
               else
               {
                   return false;
               }
            });

            if(active_tab_id != undefined)
            {
                $('#' + active_tab_id).trigger('click');
            }
        });
    });
</script>

<textarea style="display:none" id="json_data"><?php echo $this->get('building_places') ?></textarea>
<!-- Строка навигации -->
<div class="navstr">
    <div class="navstr"><b><a href="/<?php echo $this->get('url')?>">Дома на карте</a></b>: Добавление/Редактирование
    </div>
</div>

<div class="findpane">
        <table width="100%" height="26" cellspacing="0" class="findes">
          <tr>
            <td>
            </td>
            <td width="109">
<!-- Кнопки функциональные -->

            </td>
          </tr>
        </table>
    </div>

</div>
</div>

<div class="listTemplate" id="contentdiv">
<div class="contdiv hPlaces">

            <div class="map" id="map_cont">
              <img id="map" style="display:none" src="/user_files/town/<?php echo $this->get('map_img')?>"  alt="" />
             </div>

             <div id="form_column_images"  class="rightCollParams" style="display: none;">
                <div class="tabs">
                    <div id="head_tab_general">Информация</div>
                    <div class="active">Галерея</div>
                </div>

                <form id="image_upload_form" enctype="multipart/form-data" method="post" action="/<?php echo $this->get('url_action_add_image')?>" >
                <input id="building_place_id" type="hidden" name="building_place_id" value="0" />
                <div align="right" class="itemlabel">
                    Добавление фото
                </div>
                <div class="item">
                    <input name="img" type="file"  style="width: 220px"/><input class="buttybut" style="width: 200px" type="submit" name="" value="Добавить" />

                </div><br />

                <div align="right" class="itemlabel">
                    Фото
                </div>
                <div id="image_cont" class="item itemNested" style="height: 220px; overflow: auto">
                      <div id="clone_image_cont" style="display:none" class="fotodiv">
                        <a class="highslide" onclick="return hs.expand(this)" href=""><img style="width:105px" align="middle" border="0" src=""></a>
                        <a href="/<?php echo $this->get('url_action_delete_image')?>/id/" class="delete">Удалить</a>
                      </div>
                </div><br />
                </form>
             </div>

             <div id="form_column_general" style="display:none" class="rightCollParams" >
                <div class="tabs">
                    <div class="active">Информация</div>
                    <div id="head_tab_images" style="display:none">Галерея</div>
                </div>

                <div align="right" class="itemlabel">
                    Дом
                </div>
                <div class="item itemNested">
                        <select name="building_id" size="1">
                          <option value="0">Выберите дом...</option>
                          <?php if(is_array($this->get('buildings.items'))){?>
                                <?php foreach($this->get('buildings.items') as $bi){?>
                                    <option value="<?php echo $bi['id']?>"><?php echo $bi['name']?></option>
                                <?}?>
                          <?}?>
                        </select>
                </div><br />

                <div align="right" class="itemlabel">
                    Публикация
                </div>
                <div class="item itemNested">
                        <label title="Выбрать" for="CH_1" class="checkbox_checked">Да</label>
                        <input type="checkbox" class="crirHiddenJS" checked="checked" value="1" id="CH_1" name="enabled">
                </div><br />

                    <div align="right" class="itemlabel">
                        Состояние
                    </div>
                    <div class="item itemNested">
                            <select name="state" size="1">
                              <option value="0">Строится</option>
                              <option value="1">Продается земля</option>
                              <option value="2">Продается</option>
                              <option value="3">Продается меблированым</option>
                            </select>
                    </div> <br />

                    <input id="save_general" type="button" value="Сохранить" class="save" />

										<br /><br />
										<div align="right" class="itemlabel">
                        Работа с картой:
                    </div>
                    <div class="item itemNested">
											<p>Замыкания области при создании: <br />
											двойной клик на последней точке</p>
	
											<p>Сброс выделения активной области: <br />
											клик правой кнопкой миши на пустом месте</p>
											
											<p>Удалить область: <br />
											Ctrl + Shift + Click на выделенной области</p>
											
											<p>Удалить точку: <br />
											Ctrl + Shift + Click на выделенной точке</p>
											
											
										</div> <br />
              </div>
</div>
</div>
 </form>

<div style="display:none" class="overlay"></div>
<div style="display:none" class="modalWin warning">
    <h3>Удаление области</h3>
    Вы действительно желаете удалить выбранную область?
    <div class="modalButton">
        <input id="yes_btn" type="button" value="Ok" />
        <input id="no_btn" type="button" value="Отмена" />
    </div>
</div>

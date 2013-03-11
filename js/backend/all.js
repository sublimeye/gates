$(document).ready(function()
{
    if($('#content').length > 0)
    {
        CKEDITOR.replace('content');
    }

    $("#close_modal_win_btn").click(function()
    {
        $("#modal_win_overlay").css('display','none');
        $("#modal_win_container").css('display','none');
    });

    $("span[node=tree_node]").click(function(e)
    {
        e.stopPropagation();

        var sel_node = $('#tree_ul').find(".liselected");

        if(sel_node.length > 0)
        {
            $(sel_node).removeClass('liselected');
        }

        $(this).addClass('liselected');
    });

    $("span[node=tree_node]").dblclick(function(e)
    {
        e.stopPropagation();

        if($(this).hasClass('liclose') || $(this).hasClass('liopen'))
        {
            var sel_node = $('#tree_ul').find(".liselected");

            if(sel_node.length > 0)
            {
                $(sel_node).removeClass('liselected');
            }

            if($(this).hasClass('liclose'))
            {
                $(this).addClass('liselected');
                $(this).removeClass('liclose');
                $(this).addClass('liopen');
            }
            else
            {
                $(this).addClass('liselected');
                $(this).removeClass('liopen');
                $(this).addClass('liclose');
            }
        }
    });

    $("img[butt=sub_cat_btn_add]").click(function()
    {
        var sel_node = $('#tree_ul').find(".liselected");

        if(sel_node.length > 0)
        {
            var parent_li = $(sel_node).parent("li");

            if(parent_li.length > 0)
            {
                window.location.href = $(this).attr('url') + "/id/" + $(parent_li).attr('id');
            }
        }
        else
            window.location.href = $(this).attr('url');
    });

    $("img[butt=sub_cat_btn_edit]").click(function()
    {
        var sel_node = $('#tree_ul').find(".liselected");

        if(sel_node.length > 0)
        {
            var parent_li = $(sel_node).parent("li");

            if(parent_li.length > 0)
            {
                window.location.href = $(this).attr('url') + "/id/" + $(parent_li).attr('id');
            }
        }
    });

    $("img[butt=sub_cat_btn_delete]").click(function()
    {
        var sel_node = $('#tree_ul').find(".liselected");

        if(sel_node.length > 0)
        {
            var parent_li = $(sel_node).parent("li");
            if(parent_li.length > 0)
            {
                window.location.href = $(this).attr('url') + "/id/" + $(parent_li).attr('id');
            }
        }
    });

    $('#add_floor').click(function()
    {
        var is_add = $('div[is_save=false]');

        if(!$(is_add).length)
        {
            var items = $('div.floor');

            if($(items).length)
            {
                var new_item = $(items).get(0);
                new_item = $(new_item).clone();

                $(new_item).attr('is_save','false');

                $(new_item).find(':input').each(function(){
                    $(this).val('');
                });

                if($(new_item).find('img').length)
                {
                    $(new_item).find('img').remove();
                }

                if($(new_item).find('a.delete_link').length)
                {
                    $(new_item).find('a.delete_link').remove();
                }

                $(new_item).appendTo($('div.itemCont'));
                $('html, body').animate({
                         scrollTop: $(new_item).offset().top
                     }, 500);
            }
        }
        else
        {
            $('html, body').animate({
                         scrollTop: $(is_add).offset().top
                     }, 500);

            $(is_add).fadeOut(300).fadeIn(300).fadeOut(300).fadeIn(300);

        }
    });
})


 


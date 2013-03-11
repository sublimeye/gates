
Zones_image = function()
{
    var img_id = null;
    var shapes = [];
    var point_fill_color = "#ff00ae";
    var point_fill_color_over ="#ff94dd";
    var shape_fill_color = "#e294ff";
    var shape_fill_color_over = "#ff94dd";
    var shape_opacity = 0.5;
    var shape_opacity_over = 0.7;
    var r = null;
    var image_handler = null;
    var active_shape = -1;
    var self = null;
    var onselect = null;
    var ondeselect = null;
    var ondelete = null;

    this.Init = function(holder,i_id)
    {
        img_id = i_id;
        self = this;

        r = Raphael(holder,$('#' + img_id).width(),$("#" + img_id).height());
        image_handler = r.image($('#' + img_id).attr('src'), 0, 0,$('#' + img_id).width(), $('#' + img_id).height());

        image_handler.click(click_image);
        image_handler.mousemove(mouse_move_image);

        $(image_handler.node).bind("contextmenu", function()
        {
            if(active_shape >=0)
            {
                if(!shapes[active_shape].is_new)
                {
                    for(var i=0;i<shapes[active_shape].circles.length;i++)
                    {
                       shapes[active_shape].circles[i].remove();
                    }

                    shapes[active_shape].circles = [];
                    active_shape = -1;
                }
                else
                {
                    self.remove_shape();
                }

                if(ondeselect != null)
                {
                    ondeselect();
                }
            }

            return false;
        });
    }

    this.subscribe = function(evt,handler)
    {
        if(evt == 'onselect')
        {
            onselect = handler;
        }

        if(evt == 'ondeselect')
        {
            ondeselect = handler;
        }

        if(evt == 'ondelete')
        {
            ondelete = handler;
        }
    }

    this.getActive = function()
    {
        if(active_shape >=0)
        {
            return shapes[active_shape];
        }

        return undefined;
    }

    this.findShapeByAttr = function(attr_name,attr_value)
    {
        for(var i=0;i<shapes.length;i++)
        {
            if(shapes[i].attr != undefined)
            {
                if(shapes[i].attr[attr_name] != undefined && shapes[i].attr[attr_name] == attr_value)
                {
                    return shapes[i].shape;
                }
            }
        }
    }

    this.setActive = function(shape)
    {
        if(shape != undefined)
        {
            setShapeActive(shape);
        }
    }

    var click_image = function(e)
    {
        if(active_shape == -1)
        {
            var new_index = self.createShape([[e.layerX,e.layerY]],true);
            active_shape = new_index-1;

            var circle = r.circle(e.layerX,e.layerY,5);
            circle.attr("fill",point_fill_color);
            circle.array_index = 0;

            shapes[active_shape].circles.push(circle);
        }
        else
        {
            var circle = r.circle(e.layerX,e.layerY,5);
            circle.attr("fill",point_fill_color);
            circle.array_index = shapes[active_shape].circles.length;

            if(shapes[active_shape].circles.length > 2)
            {
                var n = getNeighborPoint(shapes[active_shape].points,e.layerX,e.layerY);
            }

            shapes[active_shape].circles.push(circle);
            shapes[active_shape].points.push([e.layerX,e.layerY]);

            if(shapes[active_shape].circles.length > 3 && !shapes[active_shape].is_new)
            {
                shapes[active_shape].circles = restructuringArray(shapes[active_shape].circles,n);
                shapes[active_shape].points = restructuringArray(shapes[active_shape].points,n);
            }

            if(!shapes[active_shape].is_new)
            {
                circle.hover(circle_over_handler,circle_out_handler);
                circle.drag(point_drag,point_drag_start,point_drag_end);
                circle.click(remove_circle);
            }
            else
            {
                circle.click(click_point);
            }

            redraw();
        }
    }

    var click_point = function(e)
    {
        if(active_shape >=0)
        {
            if(shapes[active_shape].is_new)
            {
               shapes[active_shape].is_new = false;

               for(var i=0;i<shapes[active_shape].circles.length;i++)
               {
                   shapes[active_shape].circles[i].hover(circle_over_handler,circle_out_handler);
                   shapes[active_shape].circles[i].drag(point_drag,point_drag_start,point_drag_end);
                   shapes[active_shape].circles[i].click(remove_circle);
               }

               shapes[active_shape].shape.mouseover(shape_over_handler);
               shapes[active_shape].shape.mouseout(shape_out_handler);
               shapes[active_shape].shape.click(click_shape);
               shapes[active_shape].shape.drag(shape_drag,shape_drag_start,shape_drag_end);

               shapes[active_shape].shape.attr(
               {
                  "fill" : shape_fill_color,
                  "fill-opacity" : shape_opacity
               });

               redraw();

               if(onselect != null)
               {
                 onselect(active_shape,shapes[active_shape]);
               }
            }
        }
    }

    var mouse_move_image = function(e)
    {
        if(active_shape >=0)
        {
            if(shapes[active_shape].is_new)
            {
                var path = pointToPath(shapes[active_shape].points,true);
                var x = (shapes[active_shape].points[shapes[active_shape].points.length -1][0] < e.layerX) ? (e.layerX - 3) : (e.layerX + 3);
                var y = (shapes[active_shape].points[shapes[active_shape].points.length -1][1] < e.layerY) ? (e.layerY - 3) : (e.layerY + 3);

                path+=" L " + x + " " + y;

                shapes[active_shape].shape.attr('path',path);
            }
        }
    }

    var circle_over_handler = function()
    {
        this.attr("fill",point_fill_color_over);
    }

    var circle_out_handler = function()
    {
        this.attr("fill",point_fill_color);
    }

    var remove_circle = function(e)
    {
        if(e.shiftKey && e.ctrlKey)
        {
            if(active_shape >= 0)
            {
                shapes[active_shape].points.splice(this.array_index,1);
                shapes[active_shape].circles.splice(this.array_index,1);

                for(var i=0;i<shapes[active_shape].points.length;i++)
                {
                   shapes[active_shape].points[i].array_index = i;
                }

                for(var i=0;i<shapes[active_shape].circles.length;i++)
                {
                   shapes[active_shape].circles[i].array_index = i;
                }

                this.remove();

                if(shapes[active_shape].points.length == 0)
                {
                    self.remove_shape();
                    return;
                }

                redraw();
            }
        }
    }

    var click_shape = function(e)
    {
        if(e.shiftKey && e.ctrlKey)
        {
            self.remove_shape(e);
            return;
        }

        setShapeActive(this);
    }

    var setShapeActive = function(shape)
    {
        if(shape.array_index != active_shape)
        {
            if(active_shape >= 0)
            {
                for(var i=0;i<shapes[active_shape].circles.length;i++)
                {
                   shapes[active_shape].circles[i].remove();
                }

                shapes[active_shape].circles = [];
                active_shape = shape.array_index;

                for(var i=0;i<shapes[active_shape].points.length;i++)
                {
                   var circle = r.circle(shapes[active_shape].points[i][0],shapes[active_shape].points[i][1],5);

                   circle.attr("fill",point_fill_color);
                   circle.array_index = i;

                   shapes[active_shape].circles.push(circle);

                   circle.hover(circle_over_handler,circle_out_handler);
                   circle.drag(point_drag,point_drag_start,point_drag_end);
                   circle.click(remove_circle);
                }
            }
            else
            {
                active_shape = shape.array_index;

                for(var i=0;i<shapes[active_shape].points.length;i++)
                {
                   var circle = r.circle(shapes[active_shape].points[i][0],shapes[active_shape].points[i][1],5);

                   circle.attr("fill",point_fill_color);
                   circle.array_index = i;

                   shapes[active_shape].circles.push(circle);

                   circle.hover(circle_over_handler,circle_out_handler);
                   circle.drag(point_drag,point_drag_start,point_drag_end);
                   circle.click(remove_circle);
                }
            }

            if(onselect != null)
            {
                onselect(active_shape,shapes[active_shape]);
            }
        }
    }

    this.remove_shape = function()
    {
        if(active_shape != -1)
        {
           if(ondelete != undefined)
           {
               var result = ondelete(shapes[active_shape]);

               if(!result)
               {
                   return false;
               }
           }
            
           for(var i=0;i<shapes[active_shape].circles.length;i++)
           {
               shapes[active_shape].circles[i].remove();
           }

           shapes[active_shape].shape.remove();
           shapes.splice(active_shape,1);
           active_shape = -1;

           for(var i=0;i<shapes.length;i++)
           {
               shapes[i].shape.array_index = i;
           }

           if(ondeselect != undefined)
           {
               ondeselect();
           }
        }
    }

    var shape_drag = function(dx,dy)
    {
        if(active_shape != -1)
        {
           for(var i=0;i<shapes[active_shape].circles.length;i++)
           {
               var offsetX = parseInt(shapes[active_shape].circles[i].start_cx) + dx;
               var offsetY = parseInt(shapes[active_shape].circles[i].start_cy) + dy;

               shapes[active_shape].circles[i].attr('cx',offsetX);
               shapes[active_shape].circles[i].attr('cy',offsetY);

               shapes[active_shape].points[i][0] = offsetX;
               shapes[active_shape].points[i][1] = offsetY;
           }

           redraw();
        }
    }

    var shape_drag_start = function()
    {
        if(active_shape != -1)
        {
            this.unmouseover(shape_over_handler);
            this.unmouseout(shape_out_handler);

            for(var i=0;i<shapes[active_shape].circles.length;i++)
            {
               shapes[active_shape].circles[i].start_cx = shapes[active_shape].circles[i].attr('cx');
               shapes[active_shape].circles[i].start_cy = shapes[active_shape].circles[i].attr('cy');
            }
        }
    }

    var shape_drag_end = function()
    {
        if(active_shape != -1)
        {
           this.mouseover(shape_over_handler);
           this.mouseout(shape_out_handler);

           for(var i=0;i<shapes[active_shape].circles.length;i++)
           {
               shapes[active_shape].circles[i].start_cx = shapes[active_shape].circles[i].attr('cx');
               shapes[active_shape].circles[i].start_cy = shapes[active_shape].circles[i].attr('cy');
           }
        }
    }

    var redraw = function()
    {
        if(active_shape >= 0)
        {
            var is_new = shapes[active_shape].is_new;
            shapes[active_shape].shape.attr('path',pointToPath(shapes[active_shape].points,is_new));
        }
    }


    this.createShape = function(points,isNew,attr)
    {
        var path = pointToPath(points,isNew);
        var shape = r.path(path);
        var is_new = (isNew == undefined) ? false : isNew;

        if(!is_new)
        {
            shape.attr( {
                "stroke" : '#ff00ae',
                "stroke-linejoin": "round",
                'stroke-dasharray' : ". ",
                'stroke-width' : 3,
                "fill" : shape_fill_color,
                "fill-opacity" : shape_opacity
            });
        }
        else
        {
            shape.attr( {
                "stroke" : '#ff00ae',
                "stroke-linejoin": "round",
                'stroke-dasharray' : ". ",
                'stroke-width' : 3
            });
        }

        shape.array_index = shapes.length;

        if(!is_new)
        {
            shape.mouseover(shape_over_handler);
            shape.mouseout(shape_out_handler);
            shape.click(click_shape);
            shape.drag(shape_drag,shape_drag_start,shape_drag_end);
        }

        shapes.push({
          'points' : points,
          'circles' : [],
          'shape' : shape,
          'path' : path,
          'is_new' : is_new,
          'attr' : attr
        });

        return shapes.length;
    }

    var shape_over_handler = function()
    {
        this.attr(
        {
            "fill-opacity" :shape_opacity_over,
            "fill" : shape_fill_color_over
        });
    }

    var shape_out_handler = function()
    {
        this.attr(
        {
            "fill-opacity" : shape_opacity,
            "fill" : shape_fill_color
        });
    }

    var pointToPath = function(points,isNew)
    {
        var path_string = "";

        if(points.length > 0)
        {
            for(var i=0;i<points.length;i++)
            {
               if(i == 0)
               {
                   path_string = "M " + points[i][0] + " " + points[i][1];
               }

               path_string+= " L " + points[i][0] + " " + points[i][1];
            }

            if(!isNew)
                path_string+=" Z";
        }

        return path_string;
    }

    var point_drag_start = function()
    {
        if(active_shape != -1)
        {
            shapes[active_shape].shape.unmouseover(shape_over_handler);
            shapes[active_shape].shape.unmouseout(shape_out_handler);

            this.unhover(circle_over_handler,circle_out_handler);
        }
    }

    var point_drag_end = function()
    {
        if(active_shape != -1)
        {
            shapes[active_shape].shape.mouseover(shape_over_handler);
            shapes[active_shape].shape.mouseout(shape_out_handler);

            this.hover(circle_over_handler,circle_out_handler);
        }
    }

    var point_drag = function(dx,dy,x,y)
    {
       if(active_shape != -1)
       {
           x= x + $('#map_cont').scrollLeft();
           y= y + $('#map_cont').scrollTop();
           
           this.attr('cx',(x -3));
           this.attr('cy',(y -140));

           shapes[active_shape].points[this.array_index][0] = x - 3;
           shapes[active_shape].points[this.array_index][1] = y - 140;

           redraw();
       }
    }

    var getNeighborPoint = function(points,x,y)
    {
       var find_index = -1;
       var index = -1;

       var distance = 0;
       var minDistance = 1000000;

       for(var i=0;i<points.length;i++)
       {
            distance = Math.sqrt((Math.pow(points[i][0]-x,2) + Math.pow(points[i][1] - y,2)));

            if(distance < minDistance)
            {
                minDistance = distance;
                find_index = i;
                index = i;
            }
       }

       if(find_index > 0 && find_index < points.length -1)
       {
           var distance_prev = Math.sqrt(Math.pow(points[index -1][0] - x,2) + Math.pow(points[index -1][1] - y,2));
           var distance_next = Math.sqrt(Math.pow(points[index +1][0] - x,2) + Math.pow(points[index +1][1] - y,2));

           if(distance_prev < distance_next)
            index--;
       }

       if(find_index == 0)
       {
           var distance_prev = Math.sqrt(Math.pow(points[points.length -1][0] - x,2) + Math.pow(points[points.length -1][1] - y,2));
           var distance_next = Math.sqrt(Math.pow(points[index +1][0] - x,2) + Math.pow(points[index +1][1] - y,2));

           if(distance_prev < distance_next)
            index = points.length -1;
       }

       if(find_index == points.length -1)
       {
           var distance_prev = Math.sqrt(Math.pow(points[points.length -2][0] - x,2) + Math.pow(points[points.length -2][1] - y,2));
           var distance_next = Math.sqrt(Math.pow(points[0][0] - x,2) + Math.pow(points[0][1] - y,2));

           if(distance_prev < distance_next)
            index = points.length -2;
       }

       return index;
    }

    var restructuringArray = function(arr,index)
    {
       var r_arr = [];

       if(index != arr.length - 1)
       {
           var last_el = arr[arr.length - 1];

           arr.splice(arr.length - 1,1);

           var part_first = arr.slice(0,index+1);
           var part_second = arr.slice(index+1);

           r_arr = r_arr.concat(part_first);
           r_arr.push(last_el);
           r_arr = r_arr.concat(part_second);

           for(var i=0;i<r_arr.length;i++)
           {
               r_arr[i].array_index = i;
           }
       }
       else
       {
           r_arr = arr;
       }

       return r_arr;
    }
}


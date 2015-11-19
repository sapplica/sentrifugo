/*
	 
    Copyright (C) 2011 T. Connell & Associates, Inc.

	Dual-licensed under the MIT and GPL licenses
	
	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF 
	MERCHANTABILITY, 	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE 
	FOR ANY CLAIM, 	DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION 
	WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
	
	Resizable scroller widget for the jQuery tablesorter plugin
	
	Version 1.0
	Requires jQuery, v1.2.3 or higher 
	Requires the tablesorter plugin, v2.0 or higher, available at www.tablesorter.com
	
	Usage:
	
		$(document).ready(function() {
			
			$('table.tablesorter').tablesorter({
				scrollHeight: 300,
				widgets: ['zebra','scroller']  //<-- This adds the scroller to the tablesorter.  Everything else is for resizing
			 });
	
			//Setup window.resizeEnd event
			$(window).bind('resize', window_resize);
			$(window).bind('resizeEnd', function (e) {
				
				//IE calls resize when you modify content, so we have to unbind the resize event
				//so we don't end up with an infinite loop. we can rebind after we're done.
				
				$(window).unbind('resize', window_resize);
				$('table.tablesorter').each(function(n, t) {
	        		if (typeof t.resizeWidth === 'function') t.resizeWidth();
	        	});
	        	$(window).bind('resize', window_resize);
	    	});
		});
	
		function window_resize() {
			if (this.resize_timer) clearTimeout(this.resize_timer);
			this.resize_timer = setTimeout(function () {
					$(this).trigger('resizeEnd');
				}
				, 250
			);
		}

	Website: www.tconnell.com
	
*/

(function($) {
	$.fn.hasScrollBar = function() {
		return this.get(0).scrollHeight > this.height();
	} 
})(jQuery); 

$.tablesorter.addWidget({
    id: "scroller",
    format: function (table) {
		var SCROLLBAR_WIDTH = 17;

        var $tbl = $(table);

        if (!table.config.isScrolling) {
            var h = table.config.scrollHeight || 250;

            var bdy_h = $('tbody', $tbl).height();
            if (bdy_h != 0 && h > bdy_h) h = bdy_h + 10;  //Table is less than h px

            var id = 's_' + Math.floor(Math.random() * 101);

            $tbl.wrap('<div id="' + id + '" class="scroller" style="text-align:left;" />');

			/*var $hdr = $('<table class="' + $tbl.attr('class') + '" cellpadding=0 cellspacing=0><thead>' + $('thead', table[0]).html() + '<thead></table>');	*/
var $hdr = $('<table class="' + $tbl.attr('class') + '" cellpadding=0 cellspacing=0><thead>' + $('thead', $tbl).html() + '<thead></table>');			
            $tbl.before($hdr);
            
            $hdr.wrap('<div class="scroller_hdr" style="width:' + $tbl.width() + ';" />');
            $tbl.wrap('<div class="scroller_tbl" style="height:' + h + 'px;width:' + $tbl.width() + ';overflow-y:scroll;" />');

            $('th', $hdr).each(function (i, hd) {
            	hd.column = i;
            	hd.count = 0;
                $(hd).unbind('click');
                $(hd).bind('click', function (e) {
                    var column = this.column;
                    this.order = this.count++ % 2;
                    $tbl.trigger("sorton", [[[this.column, this.order]]]);
                    $(this).removeClass("headerSortDown").removeClass("headerSortUp");
                    $(this).siblings().removeClass("headerSortDown").removeClass("headerSortUp");
                    $(this).addClass(this.order == 0 ? "headerSortDown" : "headerSortUp");
                });
            });

            var resize = function () {
                //Hide other scrollers so we can resize 
                $('div.scroller[id != "' + id + '"]').hide();
                
                $('thead', table).show();

                //Reset sizes so parent can resize.
                $('th', $hdr).width(0);
                $hdr.width(0);
                var h = $hdr.parent();
                h.width(0);

                $('th', $tbl).width(0);
                $tbl.width(0);
                var d = $tbl.parent();
                d.width(0);
                d.parent().trigger('resize');
                d.width(d.parent().innerWidth() - (d.parent().hasScrollBar() ? SCROLLBAR_WIDTH : 0)); //Shrink a bit to accommodate scrollbar

                $tbl.width(d.innerWidth() - (d.hasScrollBar() ? SCROLLBAR_WIDTH : 0));
                $('th', $tbl).each(function (i, c) {
                    var $th = $(c);
                    //Wrap in browser detect??
                    var w = parseInt($th.css('min-width').replace('auto', '0').replace('px', '').replace('em', ''), 10);
                    if ($th.width() < w) $th.width(w);
                    else w = $th.width();
                    $('th', $hdr).eq(i).width(w);
                });

                $hdr.width($tbl.innerWidth());
                //$('thead', table).hide();
                $('div.scroller[id != "' + id + '"]').show();
            }

            //Expose to external calls
            table.resizeWidth = resize;

            resize();

            $tbl.css("margin-top", "0px");
            $hdr.css("margin-bottom", "0px");

            //Hide the thead, while keeping its widths intact for resizing
            
            $('th', table).css('line-height', '0px')
            			  .css('height', '0px')
                          .css('border', 'none')
                          .css('background-image', 'none')
                          .css("padding-top", "0px")
                          .css("padding-bottom", "0px")
                          .css("margin-top", "0px")
                          .css("margin-bottom", "0px")
                          .children().css('height', '0px')
                          			 .css("padding-top", "0px")
                          			 .css("padding-bottom", "0px")
                          			 .css("margin-top", "0px")
									 .css("margin-bottom", "0px");
									 
			$('thead', table).css('visibility', 'hidden');
			
            table.config.isScrolling = true;
        }

        //Sorting , so scroll to top
        $tbl.parent().animate({ scrollTop: 0 }, 'fast');
    }
});

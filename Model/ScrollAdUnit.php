<?php
/**
 * Created by PhpStorm.
 * User: behcetmutlu
 * Date: 29/09/15
 * Time: 10:32
 */

namespace EnuygunCom\DfpBundle\Model;


class ScrollAdUnit extends AdUnit
{
    public function __construct($path, $sizes, $class, array $targets, $attrs)
    {
        parent::__construct($path, $sizes, empty($class) ? 'scrolldown_ad' : $class, $targets, $attrs);
    }

    public function output(Settings $settings)
    {
        $class  = $this->getClass($settings->getDivClass());
        $style  = $this->getStyles();
        $attrs  = $this->getAttrsAsString();
        $attr_array  = $this->getAttrs();
        $closed = isset($attr_array['data-closed']) ? $attr_array['data-closed'] : 'false';
        $scrolled = isset($attr_array['data-scrolled']) ? $attr_array['data-scrolled'] : 'false';
        $initTimer = isset($attr_array['data-init-timer']) ? $attr_array['data-init-timer'] : 2000;
        $closeBtn = isset($attr_array['data-close-btn']) ? $attr_array['data-close-btn'] : 'scrolldown_ad_close';

        return <<< RETURN
<div class="{$class}"{$attrs}>
<div id="{$this->divId}">
<a href="javascript:void(0)" class="{$closeBtn}">x</a>
<script type="text/javascript">
googletag.cmd.push(function() { googletag.display('{$this->divId}'); });
googletag.cmd.push(function() {
    $(function() {
        var slideTimer = setTimeout(function() {
            clearTimeout(slideTimer);
            $('div.{$class}').each(function() {
                var ad = $(this);
                var closed = {$closed};
                var scrolled = {$scrolled};
                var disabled = false;
                var showAd = function(force) { if(! closed || force) { ad.toggle('fast'); scrolled = true; closed = false; } }
                    var hideAd = function(forced) { if(!forced && disabled) return; ad.fadeOut(); closed = true; }

                    if(ad.find('div').css('display') != 'none') {
                        showAd();
                        $(window).scroll(function () {
                            if (!scrolled && !closed && $(this).scrollTop() > 40) {
                                showAd();
                            }
                        });
                        ad.find('.{$closeBtn}').click(function(e) {
                            e.preventDefault();
                            hideAd();
                        });
                    }
                    
                    var timeout = ad.data('timeout');
                    var timeout_wait = ad.data('wait-timeout');
                   
                    if(timeout) {
                        setTimeout(function(){
                            // Hide function
                            hideAd(true);
                        }, timeout);
                    }
                    
                    if(timeout_wait) {
                        var count=timeout_wait/1000;

                        var counter=setInterval(timer, 1000);
                        $('.{$class} .{$closeBtn}')
                                .html('' + count +'')
                                .css('display','inline');
                    
                    
                        function timer()
                        {
                            $('.{$class} .{$closeBtn}').html(--count);
                            if (count <= 0)
                            {
                                clearInterval(counter);
                                return;
                            }
                    
                        }
                    
                       // Disable Close Button
                        disabled = true;
                        setTimeout(function(){
                            // Enable Close Button
                            $('.{$class} .{$closeBtn}').html('x');
                            disabled = false;
                        }, timeout_wait);
                    }
                });
            }, {$initTimer});
    });
});
</script>
</div>
</div>
RETURN;
    }

}
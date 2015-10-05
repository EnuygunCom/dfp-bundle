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
    public function __construct($path, $sizes, $class, array $targets)
    {
        parent::__construct($path, $sizes, empty($class) ? 'scrolldown_ad' : $class, $targets);
    }

    public function output(Settings $settings)
    {
        $class  = $this->getClass($settings->getDivClass());
        $style  = $this->getStyles();

return <<< RETURN
<div id="{$this->divId}" class="{$class}" style="$style">
<a href="javascript:void(0)" class="scrolldown_ad_close">x</a>
<script type="text/javascript">
googletag.cmd.push(function() { googletag.display('{$this->divId}'); });
googletag.cmd.push(function() {
    $(function() {
        var slideTimer = setTimeout(function() {
            clearTimeout(slideTimer);
            $('div.{$class}').each(function() {
                var ad = $(this);
                var closed = false;
                var scrolled = false;
                var showAd = function(force) { if(! closed || force) { ad.toggle('slow'); scrolled = true; closed = false; } }
                            var hideAd = function() { ad.fadeOut(); closed = true; }

                            if(ad.find('div').css('display') != 'none') {
                                showAd();
                                $(window).scroll(function () {
                                    if (!scrolled && !closed && $(this).scrollTop() > 40) {
                                        showAd();
                                    }
                                });
                                ad.find('.scrolldown_ad_close').click(function(e) {
                                    e.preventDefault();
                                    hideAd();
                                });
                            }
                        });
                    }, 2000);
    });
});
</script>
</div>
RETURN;
    }

}
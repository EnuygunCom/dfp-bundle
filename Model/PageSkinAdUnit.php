<?php
/**
 * Created by PhpStorm.
 * User: behcetmutlu
 * Date: 29/09/15
 * Time: 10:32
 */

namespace EnuygunCom\DfpBundle\Model;


class PageSkinAdUnit extends AdUnit
{
    public function output(Settings $settings)
    {
        $class  = $this->getClass($settings->getDivClass());
        $style  = $this->getStyles();
        $attrs  = $this->getAttrsAsString();

        return <<< RETURN
<div class="{$class}"{$attrs}>
<div id="{$this->divId}">
<script type="text/javascript">
googletag.cmd.push(function() { googletag.display('{$this->divId}'); });
googletag.cmd.push(function() {
    $(function() {
        var slideTimer = setTimeout(function() {
            clearTimeout(slideTimer);
            $('div.{$class}').each(function() {
                var ad = $(this);
                var container = $('[data-role="pageskin"]');
                var closed = false;
                var scrolled = false;
                var showAd = function(force) {
                    if(! closed || force) {
                        ad.fadeIn('fast'); scrolled = true; closed = false;
                        container.css('margin-top', '90px');
                        container.css('z-index', '2');
                        container.css('position', 'relative');
                        ad.css('top', container.offset().top - 90);
                    }
                };
                var hideAd = function() {
                    ad.fadeOut('fast');
                    container.css('margin-top', '0');
                    closed = true;
                }

                            if(ad.find('div').css('display') != 'none') {
                                showAd();
                                $(window).scroll(function () {
                                    if (!scrolled && !closed && $(this).scrollTop() > 40) {
                                        showAd();
                                    }
                                });
                                ad.find('.pageskin_ad_close').click(function(e) {
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
</div>
RETURN;
    }

}
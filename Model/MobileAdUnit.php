<?php
/**
 * Created by PhpStorm.
 * User: behcetmutlu
 * Date: 29/09/15
 * Time: 10:32
 */

namespace EnuygunCom\DfpBundle\Model;


class MobileAdUnit extends AdUnit
{
    public function __construct($path, $sizes, $class, array $targets, $attrs)
    {
        parent::__construct($path, $sizes, empty($class) ? 'mobile_ad' : $class, $targets, $attrs);
    }

    public function output(Settings $settings)
    {
        $class  = $this->getClass($settings->getDivClass());
        $style  = $this->getStyles();
        $attrs  = $this->getAttrsAsString();

        return <<< RETURN
<div class="{$class}"{$attrs}>
<div id="{$this->divId}">
<script type="text/javascript" defer>
googletag.cmd.push(function() { googletag.display('{$this->divId}'); });
googletag.cmd.push(function() {
    $(function() {
        var slideTimer = setTimeout(function() {
            clearTimeout(slideTimer);
            $(function() {
                if($('.ad-container').length > 0) {
                    $('.ad-container > div:first').each(function() {
                        var el = $(this);
                        // don't show when there is no ad
                        if(el.css('display') == 'none')
                            return;

                        var parent = el.parent();
                        if(parent.data('placement') === 'bottom') {
                            $('body').css('padding-bottom', parent.data('height'));
                            $('#feedbackButton').css('bottom', parent.data('height'));
                        }
                        parent.show();
                        $('input,select,option,textarea')
                            .bind('focus', function() {
                                parent.hide();
                            }).bind('blur', function() {
                            parent.show();
                        });
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
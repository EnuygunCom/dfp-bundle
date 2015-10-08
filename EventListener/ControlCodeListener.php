<?php
/**
 * Created by PhpStorm.
 * User: behcetmutlu
 * Date: 29/09/15
 * Time: 10:40
 */

namespace EnuygunCom\DfpBundle\EventListener;


use EnuygunCom\DfpBundle\Model\AdUnitInterface;
use EnuygunCom\DfpBundle\Model\Collection;
use EnuygunCom\DfpBundle\Model\Settings;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Kernel;

class ControlCodeListener
{
    /**
     * The template placeholder where the DFP code is to be inserted.
     */
    const PLACEHOLDER = '<!-- DfpBundle Control Code -->';
    /**
     * @var Collection
     */
    protected $collection;
    /**
     * @var Settings
     */
    protected $settings;
    /**
     * @var array
     */
    protected $_unitCheckList = array();

    /**
     * Constructor.
     *
     * @param Collection $collection
     * @param Settings $settings
     */
    public function __construct(Collection $collection, Settings $settings)
    {
        $this->settings   = $settings;
        $this->collection = $collection;
    }
    /**
     * Switch out the Control Code placeholder for the Google DFP control code html,
     * based upon the included ads.
     *
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        $response = $event->getResponse();

        // check whether response content contains the placeholder
        if(strpos($response->getContent(), self::PLACEHOLDER) === false || !$this->settings->isEnabled()) {
            return;
        }

        $controlCode = '';
        if (count($this->collection) > 0) {
            $controlCode .= $this->getMainControlCode();

            $controlCode .= $this->buildAdControlBlocks();
        }
        $response->setContent(str_replace(self::PLACEHOLDER, $controlCode, $response->getContent()));
    }
    /**
     * Get the main google dfp control code block.
     *
     * This inserts the main google script.
     *
     * @return string
     */
    protected function getMainControlCode()
    {
        return <<< CONTROL
<script type="text/javascript">
var googletag = googletag || {};
googletag.cmd = googletag.cmd || [];
(function() {
var gads = document.createElement('script');
gads.async = true;
gads.type = 'text/javascript';
var useSSL = 'https:' == document.location.protocol;
gads.src = (useSSL ? 'https:' : 'http:') +
'//www.googletagservices.com/tag/js/gpt.js';
var node = document.getElementsByTagName('script')[0];
node.parentNode.insertBefore(gads, node);
})();
</script>
CONTROL;
    }

    /**
     * Get the unit checker code block.
     *
     * @return string
     */
    protected function getUnitCheckerCode()
    {
        $checkList = json_encode($this->_unitCheckList);
        $targets = $this->settings->getTargets();
        $modul = $targets['modul'];
        $sub_modul = empty($targets['sub_modul']) ? '-' : $targets['sub_modul'];

        return <<< CONTROL
<script type="text/javascript">
googletag.cmd.push(function() {
    var checkList = {$checkList};

    $(function() {
        var _checker = setTimeout(function() {
            $.each(checkList, function(id, name) {
                var isActive = $('#' + id).css('display') != 'none', action = isActive ? 'enable' : 'disable';

                $.get('/_dfp/unit-checker/{$modul}/{$sub_modul}/' + name + '/' + action + '/', function(data) {/**/}, function(data) {/**/});
            });
        }, 5000);
    });

    console.log(checkList);
});
</script>
CONTROL;
    }

    /**
     * Get the control block for an individual ad.
     *
     * @param AdUnitInterface $unit
     * @return string
     */
    protected function getAdControlBlock(AdUnitInterface $unit)
    {
        $publisherId = trim($this->settings->getPublisherId(), '/');
        $targets     = $this->getTargetsBlock($unit->getTargets());
        $sizes       = $this->printSizes($unit->getSizes());
        $divId       = $unit->getDivId();
        $path        = $unit->getPath();
        return <<< BLOCK
<script type="text/javascript">
googletag.cmd.push(function() {
var _ds = '/', key = _ds + '{$publisherId}' + _ds;
googletag.defineSlot(key + '{$path}', {$sizes}, '{$divId}').addService(googletag.pubads());
googletag.pubads().enableSingleRequest();
googletag.enableServices();{$targets}
});
</script>
BLOCK;
    }

    /**
     * Get the control block for an individual ad.
     *
     * @param AdUnitInterface $unit
     * @return string
     */
    protected function getOutOfPageAdControlBlock(AdUnitInterface $unit)
    {
        $publisherId = trim($this->settings->getPublisherId(), '/');
        $targets     = $this->getTargetsBlock($unit->getTargets());
        $divId       = $unit->getDivId();
        $path        = $unit->getPath();
        return <<< BLOCK
<script type="text/javascript">
googletag.cmd.push(function() {
var _ds = '/', key = _ds + '{$publisherId}' + _ds;
googletag.defineOutOfPageSlot(key + '{$path}', '{$divId}').addService(googletag.pubads());
googletag.pubads().enableSingleRequest();
googletag.enableServices();{$targets}
});
</script>
BLOCK;
    }

    /**
     * Print the sizes array in it's json equivalent.
     *
     * @param array $sizes
     * @return string
     */
    protected function printSizes(array $sizes)
    {
        if (count($sizes) == 1) {
            return '['.$sizes[0][0].', '.$sizes[0][1].']';
        }
        $string = '';
        foreach ($sizes as $size) {
            $string .= '['.$size[0].', '.$size[1].'], ';
        }

        return '['.trim($string, ', ').']';
    }

    /**
     * Get the targets block
     *
     * @param array $targets
     * @return string
     */
    protected function getTargetsBlock(array $targets)
    {
        $block = '';

        foreach ($this->settings->getTargets() as $name => $target) {
            if (!array_key_exists($name, $targets)) {
                $targets[$name] = $target;
            }
        }


        foreach ($targets as $name => $target) {
            if ($target === null || $target === '') {
                continue;
            }

            if (is_array($target)) {
                $values = array_values($target);
                $target = '';
                foreach ($values as $value) {
                    $target .= "'$value',";
                }

                $target = trim($target, ',');
            } else {
                $target = "'$target'";
            }
            $block .= "\ngoogletag.pubads().setTargeting('$name', $target);";
        }
        $env = $this->settings->getEnvironment();

        return $block;
    }

    /**
     * Get the control block for an individual ad.
     *
     * @return string
     */
    protected function getAdControlBlockStart()
    {
        $publisherId = trim($this->settings->getPublisherId(), '/');
        return <<< BLOCK
<script type="text/javascript">
googletag.cmd.push(function() {
var ds = '/', key = ds + '{$publisherId}' + ds;
BLOCK;
    }

    /**
     * Get the control block for an individual ad.
     *
     * @return string
     */
    protected function getAdControlBlockEnd()
    {
        return <<< BLOCK
\ngoogletag.pubads().enableSingleRequest();
googletag.enableServices();
});
</script>
BLOCK;
    }

    private function buildAdControlBlocks()
    {
        $controlCode = $this->getAdControlBlockStart();
        $this->_unitCheckList = array();
        $targets = array();
        foreach ($this->collection as $unit) {

            $this->_unitCheckList[$unit->getDivId()] = $unit->getPath();

            foreach($unit->getTargets() as $_key => $_value) {
                if(!array_key_exists($_key, $targets))
                    $targets[$_key] = $_value;
            }

            $controlCode .= $this->buildAdControlBlock($unit);
        }
        $controlCode .= $this->getTargetsBlock($targets);
        $controlCode .= $this->getAdControlBlockEnd();

        // TODO implement enable and disable decision maker here, we can not just implement a DFP code checker, because there might be some ad delays
        //      and also this is not secure
        // $controlCode .= $this->getUnitCheckerCode();

        return $controlCode;
    }

    private function buildAdControlBlock(AdUnitInterface $unit)
    {
        $sizes       = $this->printSizes($unit->getSizes());
        $divId       = $unit->getDivId();
        $path        = $unit->getPath();
        return <<< BLOCK
\ngoogletag.defineSlot(key + '{$path}', {$sizes}, '{$divId}').setCollapseEmptyDiv(true, true).addService(googletag.pubads());
BLOCK;

    }
}
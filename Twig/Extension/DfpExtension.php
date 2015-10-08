<?php

namespace EnuygunCom\DfpBundle\Twig\Extension;

use EnuygunCom\DfpBundle\Model\AdUnit;
use EnuygunCom\DfpBundle\Model\Collection;
use EnuygunCom\DfpBundle\Model\PageSkinAdUnit;
use EnuygunCom\DfpBundle\Model\ScrollAdUnit;
use EnuygunCom\DfpBundle\Model\Settings;

class DfpExtension extends \Twig_Extension
{
    protected $settings;
    protected $collection;

    /**
     * @param Settings $settings
     * @param Collection $collection
     */
    public function __construct(Settings $settings, Collection $collection)
    {
        $this->settings   = $settings;
        $this->collection = $collection;
    }

    /**
     * Define the functions that are available to templates.
     *
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('dfp_disable', array($this, 'disable')),
            new \Twig_SimpleFunction('dfp_enable', array($this, 'enable')),
            new \Twig_SimpleFunction('dfp_targets', array($this, 'setTargets')),
            new \Twig_SimpleFunction('dfp_ad_unit', array($this, 'addAdUnit'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('dfp_scroll_ad_unit', array($this, 'addScrollAdUnit'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('dfp_page_skin_ad_unit', array($this, 'addPageSkinAdUnit'), array('is_safe' => array('html'))),
        );
    }

    /**
     * This method is used to disable dfp ads, should be called before any other dfp methods
     */
    public function disable()
    {
        $this->settings->setEnabled(false);
    }

    /**
     * This method is used to enable dfp ads, should be called before any other dfp methods
     */
    public function enable()
    {
        $this->settings->setEnabled(true);
    }

    /**
     * This method is used to define targets, still overwrite option is there when you need it
     *
     * @param $targets
     */
    public function setTargets($targets)
    {
        $this->settings->setTargets($this->settings->applyTargets($targets));
    }

    /**
     * Create an ad unit and return the source
     *
     * @param string $path
     * @param array $sizes
     * @param string $class
     * @param array $targets
     * @return string
     */
    public function addAdUnit($path, array $sizes, $class = null, array $targets = array(), $attrs = array())
    {
        if(! $this->settings->isActive($path, $targets, true))
            return '';

        $unit = new AdUnit($path, $sizes, $class, $targets, $attrs);
        $this->collection->add($unit);

        return $unit->output($this->settings);
    }

    /**
     * Create an ad unit and return the source
     *
     * @param string $path
     * @param array $sizes
     * @param string $class
     * @param array $targets
     * @return string
     */
    public function addScrollAdUnit($path, array $sizes, $class = null, array $targets = array(), $attrs = array())
    {
        if(! $this->settings->isActive($path, $targets, true))
            return '';

        $unit = new ScrollAdUnit($path, $sizes, $class, $targets, $attrs);
        $this->collection->add($unit);

        return $unit->output($this->settings);
    }

    /**
     * Create an ad unit and return the source
     *
     * @param string $path
     * @param array $sizes
     * @param string $class
     * @param array $targets
     * @return string
     */
    public function addPageSkinAdUnit($path, array $sizes, $class = null, array $targets = array(), $attrs = array())
    {
        if(! $this->settings->isActive($path, $targets, true))
            return '';

        $unit = new PageSkinAdUnit($path, $sizes, $class, $targets, $attrs);
        $this->collection->add($unit);

        return $unit->output($this->settings);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'enuygun_com_dfp';
    }
}
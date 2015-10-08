<?php
/**
 * Created by PhpStorm.
 * User: behcetmutlu
 * Date: 29/09/15
 * Time: 10:32
 */

namespace EnuygunCom\DfpBundle\Model;


class AdUnit extends TargetContainer implements AdUnitInterface
{
    protected $path;
    protected $sizes;
    protected $class;
    protected $attrs;
    protected $divId;
    protected $targets = array();
    /**
     * @param string $path
     * @param array|null $sizes
     * @param string|null $class
     * @param array $targets
     * @param array $attrs
     */
    public function __construct($path, $sizes=null, $class=null, array $targets = array(), $attrs = array())
    {
        $this->setPath($path);
        $this->setSizes($sizes);
        $this->setClass($class);
        $this->setTargets($targets);
        $this->setAttrs($attrs);
        $this->buildDivId();
    }
    /**
     * Build the divId.
     */
    public function buildDivId()
    {
        $this->divId = 'dfp-'.spl_object_hash($this);
    }
    /**
     * Output the DFP code for this ad unit
     *
     * @param Settings $settings
     * @return string
     */
    public function output(Settings $settings)
    {
        $class  = $this->getClass($settings->getDivClass());
        $attrs  = $this->getAttrsAsString();

        return <<< RETURN
<div class="{$class}"{$attrs}>
<div id="{$this->divId}">
<script type="text/javascript">
googletag.cmd.push(function() { googletag.display('{$this->divId}'); });
</script>
</div>
</div>
RETURN;
    }

    protected function getStyles()
    {
        if ($this->getSizes() == null) {
            return 'width: 0; height: 0; position: absolute; bottom: 0;';
        }

        $width  = $this->getLargestWidth();
        $height = $this->getLargestHeight();

        return "width:{$width}px; height:{$height}px;";
    }
    /**
     * Get the largest width in the sizes.
     *
     * @return int
     */
    public function getLargestWidth()
    {
        if ($this->sizes === null) {
            return 0;
        }
        $largest = 0;
        foreach ($this->sizes as $size) {
            if ($size[0] > $largest) {
                $largest = $size[0];
            }
        }
        return $largest;
    }
    /**
     * Get the largest height in the sizes.
     *
     * @return int
     */
    public function getLargestHeight()
    {
        if ($this->sizes === null) {
            return 0;
        }
        $largest = 0;
        foreach ($this->sizes as $size) {
            if ($size[1] > $largest) {
                $largest = $size[1];
            }
        }
        return $largest;
    }
    /**
     * Fix the given sizes, if possible, so that they will match the internal array needs.
     *
     * @throws AdSizeException
     * @param array|null$sizes
     * @return array|null
     */
    protected function fixSizes($sizes)
    {
        if ($sizes === null) {
            return null;
        }
        if (count($sizes) == 0) {
            throw new AdSizeException('The size cannot be an empty array. It should be given as an array with a width and height. ie: array(800,600).');
        }
        if ($this->checkSize($sizes)) {
            return array($sizes);
        }
        foreach ($sizes as $size) {
            if (!$this->checkSize($size)) {
                throw new AdSizeException(sprintf('Cannot take the size: %s as a parameter. A size should be an array giving a width and a height. ie: array(800,600).', printf($size, true)));
            }
        }
        return $sizes;
    }

    /**
     * Check that the given size has is an array with two numeric elements.
     * @param $size
     * @return bool
     */
    protected function checkSize($size)
    {
        if (is_array($size) && count($size) == 2 && isset($size[0]) && is_numeric($size[0]) && isset($size[1]) && is_numeric($size[1])) {
            return true;
        }
        return false;
    }
    /**
     * Get the path.
     *
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }
    /**
     * Get the path.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }
    /**
     * Get the sizes.
     *
     * @throws AdSizeException
     * @param array $sizes
     */
    public function setSizes($sizes)
    {
        $this->sizes = $this->fixSizes($sizes);
    }
    /**
     * Get the sizes.
     *
     * @return array
     */
    public function getSizes()
    {
        return $this->sizes;
    }
    /**
     * Get the divId.
     *
     * @param string $divId
     */
    public function setDivId($divId)
    {
        $this->divId = $divId;
    }
    /**
     * Get the divId.
     *
     * @return string
     */
    public function getDivId()
    {
        return $this->divId;
    }

    /**
     * @param string $default
     * @return mixed
     */
    public function getClass($default = '')
    {
        return ! empty($this->class) ? $this->class : $default;
    }

    /**
     * @param mixed $class
     */
    public function setClass($class)
    {
        $this->class = $class;
    }

    /**
     * @return mixed
     */
    public function getAttrsAsString()
    {
        if(empty($this->attrs))
            return '';

        $attrs = '';

        foreach($this->attrs as $name => $value) {
            $attrs.= sprintf(' %s="%s"', $name, $value);
        }

        return $attrs;
    }

    /**
     * @return mixed
     */
    public function getAttrs()
    {
        return $this->attrs;
    }

    /**
     * @param mixed $attrs
     * @return AdUnit
     */
    public function setAttrs($attrs)
    {
        $this->attrs = $attrs;
        return $this;
    }

    /**
     * @return array
     */
    public function getTargets()
    {
        return $this->targets;
    }

    /**
     * @param array $targets
     * @return AdUnit
     */
    public function setTargets($targets)
    {
        $this->targets = $targets;
        return $this;
    }


}
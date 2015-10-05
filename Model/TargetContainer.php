<?php
/**
 * Created by PhpStorm.
 * User: behcetmutlu
 * Date: 29/09/15
 * Time: 10:26
 */

namespace EnuygunCom\DfpBundle\Model;


abstract class TargetContainer
{
    protected $targets = array();
    /**
     * Set the targets
     *
     * @param array $targets
     */
    public function setTargets(array $targets)
    {
        foreach ($targets as $name => $value) {
            $this->addTarget($name, $value);
        }
    }
    /**
     * Add a target to the collection.
     *
     * @param string $name
     * @param string|int|array|null $value
     */
    public function addTarget($name, $value)
    {
        if (!is_string($name)) {
            throw new \LogicException('Cannot add a target with a value that is not a string.');
        }
        if (!is_int($value) && !is_array($value) && !is_null($value) && !is_string($value)) {
            throw new \LogicException('Cannot add a target with a value that is not an array, integer, string or null.');
        }
        $this->targets[$name] = $value;
    }
    /**
     * Get the targets
     *
     * @return array
     */
    public function getTargets()
    {
        return $this->targets;
    }
}
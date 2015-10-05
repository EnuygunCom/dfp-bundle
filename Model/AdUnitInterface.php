<?php
/**
 * Created by PhpStorm.
 * User: behcetmutlu
 * Date: 29/09/15
 * Time: 13:44
 */

namespace EnuygunCom\DfpBundle\Model;


interface AdUnitInterface
{
    public function getTargets();
    public function getSizes();
    public function getDivId();
    public function getPath();
    public function getClass($default);
}
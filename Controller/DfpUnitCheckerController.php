<?php
/**
 * Created by JetBrains PhpStorm.
 * User: feyyaz
 * Date: 4/17/15
 * Time: 5:55 PM
 * To change this template use File | Settings | File Templates.
 */

namespace EnuygunCom\DfpBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;



class DfpUnitCheckerController extends Controller {

    /**
     * @Route("/unit-checker/{modul}/{sub_modul}/{path}/{action}/", name="enuygun_com_dfp_unit_checker", requirements={"_scheme" = "http"})
     */
    public function dfpAdminAction($modul, $sub_modul, $path, $action)
    {

        $dfpSettings = $this->container->get('enuygun_com_dfp.settings');

        $settings = $dfpSettings->getSettings();

        $sub_modul = $sub_modul === '-' ? null : $sub_modul;

        foreach($settings as &$setting) {
            if($setting['modul'] === $modul && ((empty($sub_modul) && empty($setting['sub_modul'])) || (!empty($sub_modul) && $setting['sub_modul'] === $sub_modul) )) {

                $setting['settings'] = json_decode($setting['settings'], true);

                $setting['settings'][$path] = $action === 'enable';
                $dfpSettings->saveSettings($setting);
                return new JsonResponse(array('success' => true));
            }
        }

        return new JsonResponse(array('success' => false));
    }
}
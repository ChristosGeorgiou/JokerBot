<?php
namespace Geo\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class StaticController extends Controller {
    /**
     * @Route("/help", name="help")
     * @Template()
     */
    public function helpAction() {
        return array();
    }

    /**
     * @Route("/tos", name="tos")
     * @Template()
     */
    public function tosAction() {
        return array();
    }
}

<?php
namespace Geo\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class StaticController extends Controller
{
    /**
     * @Route("/help", name="help")
     * @Template()
     */
    public function helpAction()
    {
//die( $this->get('kernel')->getRootDir());
        $path = $this->get('kernel')->getRootDir() . '/../README.md';
        $markdown = file_get_contents($path);

        $help = $this->container->get('markdown.parser')->transformMarkdown($markdown);

        return array(
            "help" => $help,
        );
    }

    /**
     * @Route("/tos", name="tos")
     * @Template()
     */
    public function tosAction()
    {
        return array();
    }
}

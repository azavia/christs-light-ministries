<?php

namespace ChristsLightMinistries\WebBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends ContainerAware
{
    /**
     * @Route("/", defaults={"page" = "index"})
     * @Route("/{page}.html")
     */
    public function indexAction($page)
    {
        return $this->container->get('templating')->render("ChristsLightMinistriesWebBundle:Default:$page.html.twig");
    }
}

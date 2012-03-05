<?php

namespace ChristsLightMinistries\WebBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class DefaultController extends ContainerAware
{
    /**
     * @Route("/{page}.html", defaults={"page" = "index"})
     * @Route("/", defaults={"page" = "index"})
     * @Method({"GET"})
     */
    public function indexAction($page)
    {
        return $this->container->get('templating')->renderResponse("ChristsLightMinistriesWebBundle:Default:$page.html.twig");
    }
}

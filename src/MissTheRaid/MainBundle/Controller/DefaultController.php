<?php

namespace MissTheRaid\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;

use Guzzle\Http\Client;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="home")
     */
    public function indexAction()
    {
        if ($this->getUser() !== null) {
            return $this->forward('MissTheRaidAttendanceBundle:Entry:new');
        } else {
            return $this->render('MissTheRaidMainBundle:Default:index.noauth.html.twig');
        }
    }
}

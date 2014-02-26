<?php

namespace MissTheRaid\CharacterBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use MissTheRaid\CharacterBundle\Entity\Guild;

/**
 * Guild controller.
 *
 * @Route("/guild")
 */
class GuildController extends Controller
{

    /**
     * Show a guild
     *
     * @Route("/{region}/{realm}/{name}", name="guild")
     * @Method("GET")
     * @Template()
     */
    public function showAction($region, $realm, $name)
    {
        $em = $this->getDoctrine()->getManager();

        $realm = $em->getRepository('MissTheRaidRealmBundle:Realm')->findBy(array(
            'region' => $region,
            'slug' => $realm,
        ));

        $guild = $em->getRepository('MissTheRaidCharacterBundle:Guild')->findOneBy(array(
            'realm' => $realm,
            'name' => $name,
        ));

        return array(
            'guild' => $guild,
        );
    }
}

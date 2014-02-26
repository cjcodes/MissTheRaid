<?php

namespace MissTheRaid\CharacterBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

class Builder extends ContainerAware
{
    public function characterMenu(FactoryInterface $factory, array $options)
    {
        $em = $this->container->get('doctrine.orm.entity_manager');
        $currentUser = $this->container->get('security.context')->getToken()->getUser();
        $characters = $em->getRepository('MissTheRaidCharacterBundle:Character')->findMine($currentUser);
        $guilds = $em->getRepository('MissTheRaidCharacterBundle:Guild')->findMine($currentUser);

        $menu = $factory->createItem('root');
        $menu->setChildrenAttribute('class', 'dropdown-menu');

        $menu->addChild('Characters')->setAttribute('class', 'dropdown-header');

        foreach ($characters as $character) {
            $item = $menu->addChild($character->getName(), array(
                'route'            => 'character_show',
                'routeParameters' => array(
                    'region'    => $character->getRealm()->getRegion(),
                    'realm'     => $character->getRealm()->getSlug(),
                    'character' => $character->getName(),
                )
            ));

            if ($character->isMain()) {
                $item->setLabel('<span class="glyphicon glyphicon-star"></span> ' . $character->getName());
            } else {
                $item->setLabel('<span class="glyphicon glyphicon-none"></span> ' . $character->getName());
            }
            $item->setExtra('safe_label', true);
        }

        $menu->addChild('<span class="glyphicon glyphicon-plus-sign"></span> Add new character', array(
            'route' => 'character_new',
            'extras' => array(
                'safe_label' => true,
            )
        ));

        $menu->addChild('Divider')->setAttribute('class', 'divider');

        $menu->addChild('Guilds')->setAttribute('class', 'dropdown-header');

        foreach ($guilds as $guild) {
            $menu->addChild($guild->getName(), array(
                'route' => 'guild',
                'routeParameters' => array(
                    'region' => $guild->getRealm()->getRegion(),
                    'realm'  => $guild->getRealm()->getSlug(),
                    'name'   => $guild->getName(),
                )
            ));
        }

        return $menu;
    }

    public function guildMenu(FactoryInterface $factory, array $options)
    {
        $this->getContainer()->get('doctrine.orm.entity_manager');

        $menu = $factory->createItem('root');

        return $menu;
    }
}
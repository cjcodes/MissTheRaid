<?php

namespace MissTheRaid\RealmBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use MissTheRaid\RealmBundle\Entity\Realm;

class RealmUpdateCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('realm:update')
            ->setDescription('Update the realm list')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $regions = array(
            'us',
            'eu',
        );
        $wow = $this->getContainer()->get('wow');
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $realmRepo = $em->getRepository('MissTheRaidRealmBundle:Realm');

        foreach ($regions as $region) {
            $realms = $wow->getRealms($region);

            foreach ($realms as $realm) {
                $saved = $realmRepo->findBy(array(
                    'name' => $realm['name'],
                    'slug' => $realm['slug'],
                ));

                if (!$saved) {
                    $newRealm = new Realm();

                    $newRealm
                        ->setRegion($region)
                        ->setName($realm['name'])
                        ->setSlug($realm['slug'])
                    ;

                    $em->persist($newRealm);
                }
            }
            $em->flush();
        }
    }
}
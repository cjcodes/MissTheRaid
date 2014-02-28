<?php

namespace MissTheRaid\CharacterBundle\Entity;

use Doctrine\ORM\EntityRepository;
use MissTheRaid\UserBundle\Entity\User;

class CharacterRepository extends EntityRepository
{
    public function findMine(User $user)
    {
        $characters = $this->findBy(array(
            'user' => $user,
        ));

        foreach ($characters as $key => $character) {
            if ($character->isMain()) {
                unset($characters[$key]);
                array_unshift($characters, $character);
            }
        }

        $characters = array_values($characters);

        return $characters;
    }

    public function getCharactersForUserQueryBuilder(User $user)
    {
        $qb = $this->getEntityManager()
            ->createQueryBuilder();

        $qb
            ->select('c')
            ->from('MissTheRaidCharacterBundle:Character', 'c')
            ->leftJoin('MissTheRaidUserBundle:User', 'u', 'WITH', $qb->expr()->eq('u.main', 'c'))
            ->orderBy('u.main', 'DESC')
            ->addOrderBy('c.name', 'DESC')
            ->where($qb->expr()->eq('c.user', ':user'))
            ->setParameter('user', $user)
        ;

        return $qb;
    }
}
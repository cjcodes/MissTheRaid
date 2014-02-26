<?php

namespace MissTheRaid\CharacterBundle\Entity;

use Doctrine\ORM\EntityRepository;
use MissTheRaid\UserBundle\Entity\User;
use MissTheRaid\CharacterBundle\Entity\Guild;

class GuildRepository extends EntityRepository
{
    public function findMine(User $user)
    {
        $qb = $this->getEntityManager()
            ->createQueryBuilder();

        $query = $qb
            ->select('g')
            ->from('MissTheRaidCharacterBundle:Guild', 'g')
            ->join('g.characters', 'c')
            ->join('c.user', 'u')
            ->where('u.id = :id')
            ->setParameter('id', $user->getId())
            ->getQuery();

        return $query->getResult();
    }
}
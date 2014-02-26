<?php

namespace MissTheRaid\UserBundle\Entity;

use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

use MissTheRaid\CharacterBundle\Entity\Character;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var Character
     *
     * @ORM\OneToOne(targetEntity="MissTheRaid\CharacterBundle\Entity\Character")
     */
    private $main;

    /**
     * @ORM\OneToMany(targetEntity="MissTheRaid\CharacterBundle\Entity\Character", mappedBy="user")
     */
    private $characters;

    public function __construct()
    {
        parent::__construct();
        $this->characters = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Add a character
     *
     * @param \MissTheRaid\UserBundle\Entity\Character $character
     * @return User
     */
    public function addCharacter(\MissTheRaid\CharacterBundle\Entity\Character $character)
    {
        $this->characters[] = $character;

        return $this;
    }

    /**
     * Remove a character
     *
     * @param \MissTheRaid\UserBundle\Entity\Character $character
     */
    public function removeCharacter(\MissTheRaid\CharacterBundle\Entity\Character $character)
    {
        $this->characters->removeElement($character);
    }

    /**
     * Get characters
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCharacters()
    {
        return $this->characters;
    }

    /**
     * {@inheritdoc}
     */
    public function setEmail($email)
    {
        $email = is_null($email) ? '' : $email;
        parent::setEmail($email);
        $this->setUsername($email);
    }

    /**
     * Set main
     *
     * @param \MissTheRaid\CharacterBundle\Entity\Character $main
     * @return User
     */
    public function setMain(\MissTheRaid\CharacterBundle\Entity\Character $main = null)
    {
        $this->main = $main;

        return $this;
    }

    /**
     * Get main
     *
     * @return \MissTheRaid\CharacterBundle\Entity\Character
     */
    public function getMain()
    {
        return $this->main;
    }
}

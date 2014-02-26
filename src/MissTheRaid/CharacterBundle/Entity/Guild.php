<?php

namespace MissTheRaid\CharacterBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use MissTheRaid\RealmBundle\Entity\Realm;

/**
 * Guild
 *
 * @ORM\Table(name="guild")
 * @ORM\Entity(repositoryClass="GuildRepository")
 */
class Guild
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="MissTheRaid\RealmBundle\Entity\Realm")
     */
    private $realm;

    /**
     * @ORM\OneToMany(targetEntity="Character", mappedBy="guild")
     */
    private $characters;

    /**
     * @var integer
     *
     * @ORM\Column(name="max_rank", type="integer")
     */
    private $maxRank = 0;


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
     * Set name
     *
     * @param string $name
     * @return Guild
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set realm
     *
     * @param Realm $realm
     * @return Guild
     */
    public function setRealm($realm)
    {
        $this->realm = $realm;

        return $this;
    }

    /**
     * Get realm
     *
     * @return string
     */
    public function getRealm()
    {
        return $this->realm;
    }

    /**
     * Add a character
     *
     * @param Character $character
     * @return Guild
     */
    public function addCharacter(Character $character)
    {
        $this->characters[] = $character;

        return $this;
    }

    /**
     * Remove a character
     *
     * @param character $character
     */
    public function removeCharacter(Character $character)
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
        static $characters;
        if (!$characters) {
            $characters = $this->characters->toArray();
            usort($characters, function ($a, $b) {
                return strcmp($a->getName(), $b->getName());
            });
        }

        return $characters;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->characters = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set maxRank
     *
     * @param integer $maxRank
     *
     * @return Guild
     */
    public function setMaxRank($maxRank)
    {
        $this->maxRank = $maxRank;

        return $this;
    }

    /**
     * Get maxRank
     *
     * @return integer
     */
    public function getMaxRank()
    {
        return $this->maxRank;
    }

    public function __toString()
    {
        return $this->name . ' (' . $this->realm . ')';
    }
}

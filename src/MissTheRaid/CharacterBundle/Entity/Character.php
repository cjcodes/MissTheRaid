<?php

namespace MissTheRaid\CharacterBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Character
 *
 * @ORM\Table(name="player", uniqueConstraints={@ORM\UniqueConstraint(name="name_realm",columns={"name", "realm_id"})})
 * @ORM\Entity(repositoryClass="CharacterRepository")
 */
class Character
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
     * @var string
     *
     * @ORM\Column(name="thumbnail", type="string", length=255)
     */
    private $thumbnail;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="MissTheRaid\UserBundle\Entity\User", inversedBy="characters")
     */
    private $user;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Guild", inversedBy="characters")
     */
    private $guild;

    /**
     * @ORM\OneToOne(targetEntity="CharacterVerification", mappedBy="character")
     */
    private $verification;

    /**
     * @var integer
     *
     * @ORM\Column(name="guild_rank", type="integer")
     */
    private $guildRank;

    /**
     * @ORM\OneToMany(targetEntity="MissTheRaid\AttendanceBundle\Entity\Entry", mappedBy="character")
     */
    private $attendance;


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
     * @return Character
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
     * @param string $realm
     * @return Character
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
     * Set thumbnail
     *
     * @param string $thumbnail
     * @return Character
     */
    public function setThumbnail($thumbnail)
    {
        $this->thumbnail = $thumbnail;

        return $this;
    }

    /**
     * Get thumbnail
     *
     * @return string
     */
    public function getThumbnail()
    {
        return $this->thumbnail;
    }

    /**
     * Set user
     *
     * @param \MissTheRaid\UserBundle\Entity\User $user
     *
     * @return Character
     */
    public function setUser(\MissTheRaid\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \MissTheRaid\UserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set guild
     *
     * @param \MissTheRaid\CharacterBundle\Entity\Guild $guild
     * @return Character
     */
    public function setGuild(Guild $guild = null)
    {
        $this->guild = $guild;

        return $this;
    }

    /**
     * Get guild
     *
     * @return \MissTheRaid\CharacterBundle\Entity\Guild
     */
    public function getGuild()
    {
        return $this->guild;
    }

    /**
     * Set verification
     *
     * @param \MissTheRaid\CharacterBundle\Entity\CharacterVerification $verification
     * @return Character
     */
    public function setVerification(\MissTheRaid\CharacterBundle\Entity\CharacterVerification $verification = null)
    {
        $this->verification = $verification;

        return $this;
    }

    /**
     * Get verification
     *
     * @return \MissTheRaid\CharacterBundle\Entity\CharacterVerification
     */
    public function getVerification()
    {
        return $this->verification;
    }

    /**
     * Convert a character to a string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->name . ' (' . $this->realm . ')';
    }

    /**
     * Set guildRank
     *
     * @param integer $guildRank
     * @return Character
     */
    public function setGuildRank($guildRank)
    {
        $this->guildRank = $guildRank;

        return $this;
    }

    /**
     * Get guildRank
     *
     * @return integer
     */
    public function getGuildRank()
    {
        return $this->guildRank;
    }

    /**
     * Check if this is the main of the user
     *
     * @return boolean
     */
    public function isMain()
    {
        if (!$this->getUser()->getMain()) {
            return false;
        }
        return $this->getUser()->getMain()->getId() == $this->getId();
    }

    private $sortedAttendance;

    public function getAttendance()
    {
        if (!$this->sortedAttendance) {
            $this->sortedAttendance = $this->attendance->toArray();
            usort($this->sortedAttendance, function ($a, $b) {
                return ($a->getStartDate() < $b->getStartDate()) ? -1 : 1;
            });
        }

        return $this->sortedAttendance;
    }

    public function isVerified()
    {
        return $this->verification === null;
    }
}

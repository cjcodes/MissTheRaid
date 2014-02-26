<?php

namespace MissTheRaid\CharacterBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CharacterVerification
 *
 * @ORM\Table(name="player_verification")
 * @ORM\Entity
 */
class CharacterVerification
{
    const PIECE_HEAD     = 'head';
    const PIECE_NECK     = 'neck';
    const PIECE_SHOULDER = 'shoulder';
    const PIECE_BACK     = 'back';
    const PIECE_CHEST    = 'chest';
    const PIECE_WRIST    = 'wrist';
    const PIECE_HANDS    = 'hands';
    const PIECE_WAIST    = 'waist';
    const PIECE_LEGS     = 'legs';
    const PIECE_FEET     = 'feet';

    const VERIFY_PIECES  = 2;

    /**
     * @var Character
     *
     * @ORM\OneToOne(targetEntity="Character", inversedBy="verification")
     * @ORM\Id
     */
    private $character;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $pieces;

    public function __construct(Character $character)
    {
        $pieces = $this->getAllPieces();

        $verify = array_rand($pieces, self::VERIFY_PIECES);

        $verify = array_intersect_key($pieces, array_flip($verify));

        $this->pieces = implode(',', $verify);
        $this->character = $character;
    }

    public function getPieces()
    {
        $allPieces = $this->getAllPieces();

        $return = array(
            'empty'  => array(),
            'filled' => array(),
        );

        $empties = explode(',', $this->pieces);

        foreach ($allPieces as $piece) {
            if (in_array($piece, $empties)) {
                $return['empty'][$piece] = constant("self::$piece");
            } else {
                $return['filled'][$piece] = constant("self::$piece");
            }
        }

        return $return;
    }

    private function getAllPieces()
    {
        static $pieces;

        if (!$pieces) {
            $reflection = new \ReflectionClass($this);
            $pieces = array_filter(array_keys($reflection->getConstants()), function ($var) {
                return strpos($var, 'PIECE_') !== false;
            });
        }

        return $pieces;
    }
}

<?php

namespace MissTheRaid\WowBundle\Service;

use Guzzle\Http\Client;

use MissTheRaid\RealmBundle\Entity\Realm;
use MissTheRaid\CharacterBundle\Entity\Character;

class Wow
{
    public function getRealms($region = 'us')
    {
        $client = $this->getClient($region);

        $request = $client->get('/api/wow/realm/status');

        $list = json_decode($request->send()->getBody(), true);

        $return = array();
        foreach ($list['realms'] as $realm) {
            $return[] = array(
                'name' => $realm['name'],
                'slug' => $realm['slug'],
            );
        }

        return $return;
    }

    public function getCharacter(Realm $realm, Character $character)
    {
        return $this->getCharacterField('guild', $realm, $character);
    }

    public function getCharacterField($field, Realm $realm, Character $character)
    {
        $client = $this->getClient($realm->getRegion());

        $realm = $realm->getSlug();
        $character = $character->getName();

        $request = $client->get("character/$realm/$character?fields=$field");

        return json_decode($request->send()->getBody(), true);
    }

    public function getCharacterRank(Realm $realm, Character $character)
    {
        $client = $this->getClient($realm->getRegion());

        $realm = $realm->getSlug();
        $guild = $character->getGuild()->getName();

        $request = $client->get("guild/$realm/$guild?fields=members");

        $response = json_decode($request->send()->getBody(), true);

        foreach ($response['members'] as $member) {
            if ($member['character']['name'] == $character->getName()) {
                return $member['rank'];
            }
        }

        return 1000;
    }

    private function getClient($region = 'us')
    {
        static $clients;

        if (!isset($clients[$region])) {
            $clients[$region] = new Client('http://{region}.battle.net/api/wow', array(
                'region' => $region,
            ));
        }

        return $clients[$region];
    }
}
<?php

namespace MissTheRaid\CharacterBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use MissTheRaid\CharacterBundle\Entity\Character;
use MissTheRaid\CharacterBundle\Entity\CharacterVerification;
use MissTheRaid\CharacterBundle\Form\CharacterType;
use MissTheRaid\CharacterBundle\Entity\Guild;

/**
 * Character controller.
 *
 * @Route("/character")
 */
class CharacterController extends Controller
{

    /**
     * Lists all Character entities.
     *
     * @Route("/", name="character")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('MissTheRaidCharacterBundle:Character')->findAll();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Creates a new Character entity.
     *
     * @Route("/", name="character_create")
     * @Method("POST")
     * @Template("MissTheRaidCharacterBundle:Character:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $character = new Character();
        $form = $this->createCreateForm($character);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            try {
                $armory = $this->get('wow')->getCharacter(
                    $character->getRealm(),
                    $character
                );

                $character->setThumbnail($armory['thumbnail']);
                $character->setUser($this->getUser());
                $character->setGuild($this->getGuild($armory['guild']['name'], $character));
                $character->setGuildRank($this->get('wow')->getCharacterRank($character->getRealm(), $character));

                $em->persist($character);
                $em->flush();

                $verification = new CharacterVerification($character);
                $em->persist($verification);
                $em->flush();

                if (!$this->getUser()->getMain()) {
                    $user = $this->getUser();
                    $user->setMain($character);

                    $em->persist($user);
                    $em->flush();
                }

                return $this->redirect($this->generateUrl('character_show', array(
                    'region' => $character->getRealm()->getRegion(),
                    'realm'  => $character->getRealm()->getSlug(),
                    'character' => $character->getName()
                )));
            } catch (\Guzzle\Http\Exception\BadResponseException $e) {
                $content = json_decode($e->getResponse()->getBody(), true);

                if ($content['reason']) {
                    $this->get('session')->getFlashBag()->add(
                        'error',
                        'Whoops. Looks like something\'s wrong. Here\'s what Blizzard told us: ' . $content['reason']
                    );
                } else if ($content['reason']) {
                    $this->get('session')->getFlashBag()->add(
                        'error',
                        'Problem: ' . $content['reason']
                    );
                }

                return $this->redirect($this->generateUrl('character_new'));
            }
        }
    }

    private function getGuild($guildName, Character $character)
    {
        $em = $this->getDoctrine()->getManager();

        $guild = $em->getRepository('MissTheRaidCharacterBundle:Guild')->findOneBy(array(
            'realm' => $character->getRealm(),
            'name'  => $guildName)
        );

        if (!$guild) {
            $guild = new Guild();

            $guild
                ->setName($guildName)
                ->setRealm($character->getRealm())
            ;

            $em->persist($guild);
            $em->flush();
        }

        return $guild;
    }

    /**
    * Creates a form to create a Character entity.
    *
    * @param Character $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Character $entity)
    {
        $form = $this->createForm(new CharacterType(), $entity, array(
            'action' => $this->generateUrl('character_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Character entity.
     *
     * @Route("/new", name="character_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Character();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Character entity.
     *
     * @Route("/{region}/{realm}/{character}", name="character_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($region, $realm, $character)
    {
        $entity = $this->translateCharacterSlug($region, $realm, $character);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Character entity.');
        }

        $deleteForm = $this->createDeleteForm($entity->getId());

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Verify a character
     *
     * @Route("/{region}/{realm}/{character}/verify", name="character_verify")
     * @Method("GET")
     * @Template()
     */
    public function verifyAction($region, $realm, $character)
    {
        $character = $this->translateCharacterSlug($region, $realm, $character);

        if (!$character->getVerification()) {
            return $this->redirect($this->generateUrl('character_show', array(
                'region' => $character->getRealm()->getRegion(),
                'realm'  => $character->getRealm()->getSlug(),
                'character' => $character->getName()
            )));
        }

        $verification = $character->getVerification();
        $pieces = $verification->getPieces();

        try {
            $gear = $this->get('wow')->getCharacterField('items', $character->getRealm(), $character);
        } catch (\Guzzle\Http\Exception\BadResponseException $e) {
            $content = json_decode($e->getResponse()->getBody(), true);
            if ($content['reason']) {
                $this->get('session')->getFlashBag()->add(
                    'error',
                    'Whoops. Looks like something\'s wrong. Here\'s what Blizzard told us: ' . $content['reason']
                );
            } else {
                $this->get('session')->getFlashBag()->add(
                    'error',
                    'There was a problem with the Blizzard API. Please try again later.'
                );
            }

            return $this->redirect($this->generateUrl('character_show', array(
                'region' => $character->getRealm()->getRegion(),
                'realm'  => $character->getRealm()->getSlug(),
                'character' => $character->getName()
            )));
        }
        $gear = $gear['items'];

        $valid = true;
        $needToRemove = array();
        foreach ($pieces['empty'] as $piece) {
            if (isset($gear[$piece])) {
                $needToRemove[] = $piece;
            }
        }

        $needToEquip = array();
        foreach ($pieces['filled'] as $piece) {
            if (!isset($gear[$piece])) {
                $needToEquip[] = $piece;
            }
        }

        if (empty($needToRemove) && empty($needToEquip)) {
            $em = $this->getDoctrine()->getManager();

            $em->remove($verification);
            $em->flush();

            return $this->redirect($this->generateUrl('character_show', array(
                'region' => $character->getRealm()->getRegion(),
                'realm'  => $character->getRealm()->getSlug(),
                'character' => $character->getName()
            )));
        } else {
            return array(
                'equip'  => $needToEquip,
                'remove' => $needToRemove,
                'entity' => $character,
            );
        }
    }

    private function translateCharacterSlug($region, $realm, $name)
    {
        $em = $this->getDoctrine()->getManager();

        $realm = $em->getRepository('MissTheRaidRealmBundle:Realm')->findBy(array(
            'region' => $region,
            'slug' => $realm,
        ));

        $entity = $em->getRepository('MissTheRaidCharacterBundle:Character')->findOneBy(array(
            'realm' => $realm,
            'name' => $name,
        ));

        return $entity;
    }

    /**
     * Deletes a Character entity.
     *
     * @Route("/{id}", name="character_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('MissTheRaidCharacterBundle:Character')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Character entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('character'));
    }

    /**
     * Creates a form to delete a Character entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('character_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }

    /**
     * Make a character my main
     *
     * @Route("/{region}/{realm}/{character}/main", name="make_main")
     * @Method("GET")
     */
    public function makeMainAction($region, $realm, $character)
    {
        $character = $this->translateCharacterSlug($region, $realm, $character);

        $user = $this->getUser()->setMain($character);

        $em = $this->getDoctrine()->getManager();

        $em->persist($user);
        $em->flush();

        return $this->redirect($this->generateUrl('character_show', array(
            'region' => $character->getRealm()->getRegion(),
            'realm'  => $character->getRealm()->getSlug(),
            'character' => $character->getName()
        )));
    }
}

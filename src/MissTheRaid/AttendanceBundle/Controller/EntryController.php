<?php

namespace MissTheRaid\AttendanceBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use MissTheRaid\AttendanceBundle\Entity\Entry;
use MissTheRaid\AttendanceBundle\Form\EntryType;

/**
 * Entry controller.
 *
 * @Route("/")
 */
class EntryController extends Controller
{

    /**
     * Lists all Entry entities.
     *
     * @Route("/", name="entry")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('MissTheRaidAttendanceBundle:Entry')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Entry entity.
     *
     * @Route("/", name="entry_create")
     * @Method("POST")
     * @Template("MissTheRaidAttendanceBundle:Entry:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Entry();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('home'));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
    * Creates a form to create a Entry entity.
    *
    * @param Entry $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Entry $entity)
    {
        $form = $this->createForm(new EntryType($this->getUser()), $entity, array(
            'action' => $this->generateUrl('entry_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Submit'));

        return $form;
    }

    /**
     * Displays a form to create a new Entry entity.
     *
     * @Route("/new", name="entry_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        if ($this->getUser()->getCharacters()->count() == 0) {
            return $this->redirect($this->generateUrl('character_new'));
        }

        $verified = false;
        foreach ($this->getUser()->getCharacters() as $character) {
            if ($character->isVerified()) {
                $verified = true;
            }
        }

        if (!$verified) {
            $this->get('session')->getFlashBag()->add(
                'error',
                'Please verify at least one character before continuing.'
            );
            $char = $this->getUser()->getMain();
            return $this->redirect($this->generateUrl('character_show', array(
                'region' => $char->getRealm()->getRegion(),
                'realm'  => $char->getRealm()->getSlug(),
                'character' => $char->getName(),
            )));
        }

        $entity = new Entry();
        $form   = $this->createCreateForm($entity);

        $guilds = $this->getDoctrine()->getManager()->getRepository('MissTheRaidCharacterBundle:Guild')->findMine($this->getUser());

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'guilds' => $guilds,
        );
    }

    /**
     * Finds and displays a Entry entity.
     *
     * @Route("/{id}", name="entry_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('MissTheRaidAttendanceBundle:Entry')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Entry entity.');
        }

        return array(
            'entity' => $entity,
        );
    }
}

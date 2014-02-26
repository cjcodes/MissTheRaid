<?php

namespace MissTheRaid\AttendanceBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use MissTheRaid\AttendanceBundle\Form\EventListener\CharacterFieldSubscriber;
use MissTheRaid\UserBundle\Entity\User;

class EntryType extends AbstractType
{
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $this->user;

        $builder
            ->add('startDate')
            ->add('endDate')
            ->add('character', 'entity', array(
                'expanded'       => false,
                'empty_value'    => false,
                'class'          => 'MissTheRaid\CharacterBundle\Entity\Character',
                'query_builder' => function($repository) use ($user) {
                    return $repository->getCharactersForUserQueryBuilder($user);
                },
            ))
            ->add('reason', null, array(
                'required' => false,
                'attr' => array(
                    'placeholder' => 'Optional. Let your raid leaders know why you won\'t be able to attend.',
                )
            ))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'MissTheRaid\AttendanceBundle\Entity\Entry'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'attendance';
    }
}

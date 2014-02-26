<?php

namespace MissTheRaid\UserBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\ProfileFormType as BaseType;

class ProfileFormType extends BaseType
{
    /**
     * {@inheritdoc}
     */
    protected function buildUserForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildUserForm($builder, $options);
        $builder->remove('username');

        $builder->add('main', 'entity', array(
            'expanded' => true,
            'empty_value' => false,
        ));
    }

    public function getName()
    {
        return 'profile';
    }
}

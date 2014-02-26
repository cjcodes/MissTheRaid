<?php

namespace MissTheRaid\UserBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;

class RegistrationFormType extends BaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $password = $builder->get('plainPassword');
        $builder->remove('plainPassword');
        $builder->remove('username');
        $builder->add('plainPassword', 'password', array('translation_domain' => 'FOSUserBundle', 'label' => 'Password'));
        $builder->add('email', null, array('label' => 'Email'));
    }

    public function getName()
    {
        return 'user_registration';
    }
}
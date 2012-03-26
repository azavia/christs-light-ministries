<?php

namespace Clm\PrayerRequestBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class PrayerRequestType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('email')
            ->add('contact_method')
            ->add('phone_number')
            ->add('skype_username')
            ->add('subject')
            ->add('prayer_request')
            ->add('ip_address')
            ->add('private')
        ;
    }

    public function getName()
    {
        return 'clm_prayerrequestbundle_prayerrequesttype';
    }
}

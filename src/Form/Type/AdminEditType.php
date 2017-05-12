<?php

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\FileType;


class AdminEditType extends AbstractType{

    public function buildForm(FormBuilderInterface $builder, array $options){

        $builder
            ->add('username', TextType::class)
            ->add('mail', TextType::class)
            ->add('role', ChoiceType::class, array(
                'multiple' => false,
                'choices' => array('Admin' => 'ROLE_ADMIN', 'User' => 'ROLE_USER')))
            ->add('frequency', ChoiceType::class, array(
                'multiple' => false,
                'choices'  => array('Quotidien' => 'Quotidien','Hebdomadaire' => 'Hebdomadaire','Mensuel' => 'Mensuel' )))
            ;
    }

    public function getName(){
        return 'user';
    }

}
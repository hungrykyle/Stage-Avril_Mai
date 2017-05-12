<?php

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints as Assert;

class ImageType extends AbstractType{

    public function buildForm(FormBuilderInterface $builder, array $options){

        $builder->add('avatar', FileType::class, array(
                'mapped' => false,
                'label' => false, 
                'constraints' => array(
                         new Image(array(
            'minWidth' => 200,
            'maxWidth' => 600,
            'minHeight' => 200,
            'maxHeight' => 600,
            'mimeTypes' => "image/png",
        )),
            ),
            ));
    }
    public function getName(){
        return 'user';
    }

}
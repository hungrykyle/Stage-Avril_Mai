<?php


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ChoiceKeywordType extends AbstractType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
    }

    public function getKeywords()
    {
        return 'keywords';
    }
}

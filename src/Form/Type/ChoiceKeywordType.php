<?php


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ChoiceKeywordsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('isAttending', ChoiceType::class, array(
        'choices'  => array(
        'Maybe' => null,
        'Yes' => true,
        'No' => false,
    ),
));
    }

    public function getKeywords()
    {
        return 'keywords';
    }
}

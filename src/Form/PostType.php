<?php

namespace App\Form;

use App\Entity\Post;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'help' => 'The title of your post',
                'required' => true
            ])
            ->add('shortDescription', TextType::class, [
                'help' => 'A short description that best summarize your post',
                'required' => true
            ])
            ->add('body', TextareaType::class, [
                'help' => 'The actual content of your post',
                'required' => true
            ])
            ->add('category', EntityType::class, [
                'class' => 'App\Entity\Category',
                'choice_label' => 'name',
                'choice_value' => 'id',
                'help' => 'Choose a category for your post'
            ])
            ->add('imageFile', FileType::class, [
                'mapped' => false,
                'required' => false,
                'help' => 'Upload an image that will be displayed with your post'
            ])
            ->add('send', SubmitType::class, [
                'label' => 'Submit',
                'attr' => [ 'class' => 'btn-primary btn-block' ],
                'row_attr' => [ 'class' => 'mb-0' ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}

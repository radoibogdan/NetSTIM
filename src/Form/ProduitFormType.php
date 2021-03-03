<?php

namespace App\Form;

use App\Entity\Produit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\PositiveOrZero;

class ProduitFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'constraints' =>[
                    new NotBlank(['message' => 'Ce champ ne peut être vide.']),
                    new Length([
                        'max' => 200,
                        'maxMessage' => 'Le nom ne peut contenir plus de {{ limit }} caractères.'
                    ])
                ],
                'required' => false
            ])
            ->add('short_description', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Ce champ ne peut être vide.']),
                    new Length([
                        'max' => 100,
                        'maxMessage' => 'Le résumé ne peut contenir plus de {{ limit }} caractères.'
                    ])
                ],
                'required' => false
            ])
            ->add('description', TextareaType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Ce champ ne peut être vide.']),
                ],
                'help' => 'La description courte du produit est limitée à 300 caractères.',
                'required' => false
            ])
            ->add('price', NumberType::class,[
                'constraints' => [
                    new PositiveOrZero(['message' => 'Le prix doit être positif ou zéro.']),
                    new NotBlank(['message' => 'Le prix ne peut être vide']),
                ],
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}

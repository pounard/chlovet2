<?php

declare(strict_types=1);

namespace App\Controller\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type as Form;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;

final class ContactFormType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $required = $options['required'] ?? true;

        $builder
            ->add('nom', Form\TextType::class, [
                'label' => "Nom/Prénom",
                'attr' => [
                    'placeholder' => 'Jacques DURAND',
                ],
                'required' => $required,
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => "Veuillez renseigner vos nom et prénom.",
                    ]),
                ],
            ])
            ->add('email', Form\TextType::class, [
                'label' => "E-mail",
                'attr' => [
                    'placeholder' => $options['placeholder_email'] ?? 'jacques.durand@example.fr',
                ],
                'required' => $required,
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => "Veuillez renseigner votre e-mail.",
                    ]),
                ],
            ])
            ->add('telephone', Form\TextType::class, [
                'label' => "Téléphone joignable",
                'attr' => [
                    'placeholder' => $options['placeholder_telephone'] ?? '02 21 32 43 54',
                ],
                'required' => $required,
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => "Veuillez renseigner un numéro de téléphone pour vous joindre.",
                    ]),
                ],
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'groups' => [Constraint::DEFAULT_GROUP],
            'html5' => true,
            'label' => false,
            'placeholder_email' => null,
            'required' => true,
        ]);
    }
}

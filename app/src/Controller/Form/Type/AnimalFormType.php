<?php

declare(strict_types=1);

namespace App\Controller\Form\Type;

use App\Controller\Form\FormHelper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Extension\Core\Type as Form;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;

final class AnimalFormType extends AbstractType
{
    use ParentActivatedValidationTrait;

    const GROUP_ANIMAL_ESPECE_AUTRE = 'AnimalEspeceAutre';

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $required = $options['required'] ?? true;

        $builder
            ->add('animal_nom', Form\TextType::class, [
                'label' => "Nom de l'animal",
                'attr' => [
                    'placeholder' => 'Nicky, Grisounette, ...',
                ],
                'required' => $required,
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => "Veuillez renseigner le nom de votre animal.",
                    ]),
                ],
            ])
            ->add('animal_suivi', Form\ChoiceType::class, [
                'label' => "Est-il suivi au cabinet ?",
                'expanded' => false,
                'required' => $required,
                'placeholder' => FormHelper::PLACEHOLDER_DEFAULT,
                'choices' => FormHelper::map([
                    "Oui",
                    "Non",
                ]),
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => "Veuillez préciser si votre animal est suivi au cabinet.",
                    ]),
                ],
            ])
            ->add('animal_espece', Form\ChoiceType::class, [
                'label' => "Espèce",
                'required' => $required,
                'placeholder' => FormHelper::PLACEHOLDER_DEFAULT,
                'choices' => FormHelper::map([
                    "Chien",
                    "Chat",
                    "Autre (précisez)",
                ]),
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => "Veuillez renseigner votre e-mail.",
                    ]),
                ],
            ])
            ->add('animal_espece_autre', Form\TextType::class, [
                'label' => "Si autre, précisez l'espèce",
                'data' => null,
                'attr' => [
                    'placeholder' => "Rat, Tortue, ...",
                ],
                'required' => false,
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => "Veuillez renseigner l'espèce de votre animal.",
                        'groups' => [self::GROUP_ANIMAL_ESPECE_AUTRE],
                    ]),
                ],
            ])
            ->add('animal_race', Form\TextType::class, [
                'label' => "Race",
                'data' => null,
                'attr' => [
                    'placeholder' => "Race si connue...",
                ],
                'required' => false,
            ])
            ->add('animal_date_naissance', Form\TextType::class, [
                'label' => "Date de naissance/âge approximatif",
                'required' => $required,
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => "Veuillez renseigner la date de naissance ou âge approximatif de votre animal.",
                    ]),
                ],
            ])
            ->add('animal_sexe', Form\ChoiceType::class, [
                'label' => "Sexe",
                'expanded' => false,
                'required' => $required,
                'placeholder' => FormHelper::PLACEHOLDER_DEFAULT,
                'choices' => FormHelper::map([
                    "Mâle",
                    "Femelle",
                ]),
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => "Veuillez préciser le sexe de votre animal.",
                    ]),
                ],
            ])
            ->add('animal_sterilise', Form\ChoiceType::class, [
                'label' => "Stérilisé",
                'expanded' => false,
                'required' => $required,
                'placeholder' => FormHelper::PLACEHOLDER_DEFAULT,
                'choices' => FormHelper::map([
                    "Oui",
                    "Non",
                ]),
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => "Veuillez préciser si votre animal est stérilisé ou non.",
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

        $this->doConfigureValidationGroupsOptions($resolver);
    }

    /**
     * {@inheritdoc}
     */
    protected function resolveValidationGroups(FormInterface $form, ?array $groups): ?array
    {
        if (
            $groups && $form->has('animal_espece') &&
            'autre' === $form->get('animal_espece')->getData()
        ) {
            $groups[] = self::GROUP_ANIMAL_ESPECE_AUTRE;
        }

        return \array_unique($groups);
    }
}

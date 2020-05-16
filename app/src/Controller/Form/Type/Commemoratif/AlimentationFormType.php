<?php

declare(strict_types=1);

namespace App\Controller\Form\Type\Commemoratif;

use App\Controller\Form\FormHelper;
use App\Controller\Form\Type\ParentActivatedValidationTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Extension\Core\Type as Form;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;

class AlimentationFormType extends AbstractType
{
    use ParentActivatedValidationTrait;

    const GROUP_CHANGEMENT_ALIMENTAUIRE = 'AlimentaireChangement';

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $required = $options['required'] ?? true;

        $builder->add('croquettes', Form\CheckboxType::class, [
            'label' => "Croquettes",
            'required' => false,
        ]);
        $builder->add('croquettes_marque', Form\TextType::class, [
            'label' => "Nom ou marque des croquettes (si connue)",
            'required' => false,
        ]);
        $builder->add('patee', Form\CheckboxType::class, [
            'label' => "Pâtée",
            'required' => false,
        ]);
        $builder->add('patee_marque', Form\TextType::class, [
            'label' => "Nom ou marque de la pâtée (si connue)",
            'required' => false,
        ]);
        $builder->add('menagere', Form\CheckboxType::class, [
            'label' => "Alimentation ménagères",
            'required' => false,
        ]);
        $builder->add('autres', Form\ChoiceType::class, [
            'label' => "Où achetez vous l'alimentation ?",
            'expanded' => true,
            'multiple' => true,
            'required' => false,
            'choices' => FormHelper::map([
                "En supermarché",
                "En animalerie",
                "Chez un vétérinaire",
                "Sur internet",
            ]),
        ]);

        $builder->add('changement_recent', Form\ChoiceType::class, [
            'label' => "Y-a-t'il eu un changement récent dans l'alimentation ?",
            'expanded' => false,
            'required' => $required,
            'placeholder' => FormHelper::PLACEHOLDER_DEFAULT,
            'choices' => [
                "Oui" => 1,
                "Non" => 0,
            ],
            'constraints' => [
                new Assert\NotBlank([
                    'message' => "Veuillez préciser s'il y a eu un changement récent dans l'alimentation.",
                ]),
            ],
        ]);
        $builder->add('changement_recent_transition', Form\ChoiceType::class, [
            'label' => "Si oui, y-a-t'il eu une période de transition alimentaire ?",
            'expanded' => false,
            'required' => $required,
            'placeholder' => FormHelper::PLACEHOLDER_DEFAULT,
            'attr' => [
                'placeholder' => "Saississez la date au format " . (new \DateTimeImmutable())->format('d/m/Y'),
            ],
            'choices' => [
                "Oui" => 1,
                "Non" => 0,
            ],
            'constraints' => [
                new Assert\NotBlank([
                    'message' => "Veuillez préciser s'il y a eu un changement récent dans l'alimentation.",
                    'groups' => [self::GROUP_CHANGEMENT_ALIMENTAUIRE],
                ]),
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'groups' => [Constraint::DEFAULT_GROUP],
            'html5' => false,
            'label' => false,
            'required' => false,
        ]);

        $this->doConfigureValidationGroupsOptions($resolver);
    }

    /**
     * {@inheritdoc}
     */
    protected function resolveValidationGroups(FormInterface $form, ?array $groups): ?array
    {
        if (
            $groups && $form->has('changement_recent') &&
            1 === $form->get('changement_recent')->getData()
        ) {
            $groups[] = self::GROUP_CHANGEMENT_ALIMENTAUIRE;
        }

        return \array_unique($groups);
    }
}

<?php

declare(strict_types=1);

namespace App\Controller\Form\Type\Commemoratif;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Extension\Core\Type as Form;
use Symfony\Component\Validator\Constraints as Assert;

final class ProblemesUrinairesFormType extends ProblemesFormType
{
    const GROUP_COULEUR_ODEUR = 'ProblemesUrinairesCouleurOdeur';

    protected static ?string $group = 'ProblemesUrinaires';

    /**
     * {@inheritdoc}
     */
    protected function getNatures(): ?array
    {
        return [
            "Malpropreté",
            "Urine plus souvent",
            "Douleur associée",
            "Avec difficulté",
            "Pas ou peu d'urine",
            "Couleur ou odeur anormale",
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function avecAutreNature(): bool
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add('couleur_odeur', Form\TextType::class, [
            'label' => "Si couleur ou odeur anormale, précisez",
            'required' => false,
            'constraints' => [
                new Assert\NotBlank([
                    'message' => "Veuillez donner des précisions sur la couleur ou odeur anormale.",
                    'groups' => [self::GROUP_COULEUR_ODEUR],
                ]),
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function resolveValidationGroups(FormInterface $form, ?array $groups): ?array
    {
        $groups = parent::resolveValidationGroups($form, $groups);

        if (
            $groups && $form->has('nature') &&
            \in_array("Couleur ou odeur anormale", $form->get('nature')->getData())
        ) {
            $groups[] = self::GROUP_COULEUR_ODEUR;
        }

        return $groups;
    }
}

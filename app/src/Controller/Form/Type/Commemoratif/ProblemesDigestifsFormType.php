<?php

declare(strict_types=1);

namespace App\Controller\Form\Type\Commemoratif;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Extension\Core\Type as Form;
use Symfony\Component\Validator\Constraints as Assert;

final class ProblemesDigestifsFormType extends ProblemesFormType
{
    const GROUP_VOMISSEMENTS = 'ProblemesDigestifsVomissements';

    protected static ?string $group = 'ProblemesDigestifs';

    /**
     * {@inheritdoc}
     */
    protected function getNatures(): ?array
    {
        return [
            "Selles molles",
            "Selles liquides",
            "Selles dures",
            "Absences de selles",
            "Présence de sang dans les selles",
            "Vomissements",
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

        $builder->add('vomissements_frequence', Form\TextType::class, [
            'label' => "Si vomissements, précisez la fréquence",
            'required' => false,
            'constraints' => [
                new Assert\NotBlank([
                    'message' => "Veuillez préciser la fréquence des vomissements.",
                    'groups' => [self::GROUP_VOMISSEMENTS],
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
            \in_array("Vomissements", $form->get('nature')->getData())
        ) {
            $groups[] = self::GROUP_VOMISSEMENTS;
        }

        return $groups;
    }
}

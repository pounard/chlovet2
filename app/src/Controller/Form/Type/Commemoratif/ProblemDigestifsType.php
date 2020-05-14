<?php

declare(strict_types=1);

namespace App\Controller\Form\Type\Commemoratif;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Extension\Core\Type as Form;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;

final class ProblemDigestifsType extends ProblemFormType
{
    const GROUP_VOMISSEMENTS = 'ProblemeDigestifVomissement';

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
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'groups' => [Constraint::DEFAULT_GROUP],
            'html5' => false,
            'label' => false,
            'natures' => [
                "Selles molles",
                "Selles liquides",
                "Selles dures",
                "Absences de selles",
                "Présence de sang dans les selles",
                "Vomissements",
            ],
            'required' => false,
        ]);

        $this->doConfigureValidationGroupsOptions($resolver);
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

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

final class ComportementFormType extends AbstractType
{
    use ParentActivatedValidationTrait;

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $required = $options['required'] ?? true;

        $builder->add('etat_general', Form\ChoiceType::class, [
            'label' => "État général",
            'expanded' => false,
            'required' => $required,
            'placeholder' => FormHelper::PLACEHOLDER_DEFAULT,
            'choices' => FormHelper::map([
                "En forme",
                "Un peu fatigué",
                "Très abattu",
                "Complètement prostré",
            ]),
            'constraints' => [
                new Assert\NotBlank([
                    'message' => "Veuillez préciser l'état général de votre animal.",
                ]),
            ],
        ]);

        $builder->add('apetit', Form\ChoiceType::class, [
            'label' => "Appétit",
            'expanded' => false,
            'required' => $required,
            'placeholder' => FormHelper::PLACEHOLDER_DEFAULT,
            'choices' => FormHelper::map([
                "Normal",
                "Diminué",
                "Nul",
                "Augmenté",
            ]),
            'constraints' => [
                new Assert\NotBlank([
                    'message' => "Veuillez préciser l'appétit de votre animal.",
                ]),
            ],
        ]);

        $builder->add('prise_boisson', Form\ChoiceType::class, [
            'label' => "Prise de boisson",
            'expanded' => false,
            'required' => $required,
            'placeholder' => FormHelper::PLACEHOLDER_DEFAULT,
            'choices' => FormHelper::map([
                "Normale",
                "Diminuée",
                "Nulle",
                "Augmentée",
            ]),
            'constraints' => [
                new Assert\NotBlank([
                    'message' => "Veuillez préciser la prise de boisson de votre animal.",
                ]),
            ],
        ]);

        $builder->add('autres_anomalies', Form\ChoiceType::class, [
            'label' => "Autres anomalies",
            'expanded' => true,
            'multiple' => true,
            'required' => false,
            'placeholder' => FormHelper::PLACEHOLDER_DEFAULT,
            'choices' => FormHelper::map([
                "Plaintes",
                "Tremblements",
                "Convulsions",
                "Altération de la conscience",
                "Défaut de toilettage",
                "Bave",
                "Mauvaise haleine",
            ]),
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
        return $groups;
    }
}

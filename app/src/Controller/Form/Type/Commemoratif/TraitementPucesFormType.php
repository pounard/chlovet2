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

final class TraitementPucesFormType extends AbstractType
{
    use ParentActivatedValidationTrait;

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $required = $options['required'] ?? true;

        $builder->add('frequence', Form\ChoiceType::class, [
            'label' => "Fréquence",
            'expanded' => false,
            'required' => $required,
            'placeholder' => FormHelper::PLACEHOLDER_DEFAULT,
            'choices' => FormHelper::map([
                "Très régulier, en prévention",
                "À la demande",
            ]),
            'constraints' => [
                new Assert\NotBlank([
                    'message' => "Veuillez préciser la fréquence de traitement contre les puces.",
                ]),
            ],
        ]);

        $builder->add('dernier_traitement_date', Form\DateType::class, [
            'label' => "Date du dernier traitement",
            'attr' => [
                'placeholder' => "Saississez la date au format " . (new \DateTimeImmutable())->format('d/m/Y'),
            ],
            'widget' => 'single_text',
            'format' => 'dd/MM/yyyy',
            'required' => false,
            'html5' => false,
        ]);

        $builder->add('dernier_traitement_nom', Form\TextType::class, [
            'label' => "Nom du dernier traitement",
            'attr' => [
                'placeholder' => "Saississez le nom du dernier traitement...",
            ],
            'required' => false,
        ]);

        $builder->add('achat', Form\ChoiceType::class, [
            'label' => "Achetez vous le plus souvent",
            'expanded' => false,
            'required' => $required,
            'placeholder' => FormHelper::PLACEHOLDER_DEFAULT,
            'choices' => FormHelper::map([
                "Chez un vétérinaire",
                "En grande surface ou animalerie",
            ]),
            'constraints' => [
                new Assert\NotBlank([
                    'message' => "Veuillez préciser l'endroit privilégié d'achat du traitement.",
                ]),
            ],
        ]);

        $builder->add('type', Form\ChoiceType::class, [
            'label' => "Quel type de traitement achetez vous",
            'expanded' => false,
            'required' => $required,
            'placeholder' => FormHelper::PLACEHOLDER_DEFAULT,
            'choices' => FormHelper::map([
                "Pipette",
                "Comprimé",
            ]),
            'constraints' => [
                new Assert\NotBlank([
                    'message' => "Veuillez préciser le type de traitement privilégié.",
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
            'natures' => [],
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

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

abstract class ProblemesFormType extends AbstractType
{
    use ParentActivatedValidationTrait;

    /**
     * Les natures (choix multiple) à cocher.
     */
    protected abstract function getNatures(): ?array;

    /**
     * Précise si "Autre" doit être ajouté parmis les natures.
     */
    protected abstract function avecAutreNature(): bool;

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $group = self::computeGroupFromClass();
        $groupNatureAutre = $group . 'NatureAutre';

        $builder->add(FormHelper::SECTION_KILLSWITCH, Form\CheckboxType::class, [
            'required' => false,
        ]);

        $builder->add('depuis', Form\DateType::class, [
            'label' => "Si oui, précisez la date",
            'data' => null,
            'attr' => [
                'placeholder' => "Saississez la date au format " . (new \DateTimeImmutable())->format('d/m/Y'),
            ],
            'invalid_message' => FormHelper::INVALID_VALUE_MESSAGE_DEFAULT,
            'widget' => 'single_text',
            'format' => 'dd/MM/yyyy',
            'required' => false,
            'html5' => false,
            'constraints' => [
                new Assert\NotBlank([
                    'message' => "Veuillez renseigner la date d'apparition des symptômes.",
                    'groups' => [$group],
                ]),
            ],
        ]);

        $natures = $this->getNatures();
        if ($natures) {
            $avecAutreNature = $this->avecAutreNature();

            if ($avecAutreNature) {
                $natures[] = "Autre";
            }

            $builder->add('nature', Form\ChoiceType::class, [
                'label' => "Précisez la nature du problème (plus choix sont possibles)",
                'expanded' => true,
                'multiple' => true,
                'required' => false,
                'choices' => FormHelper::map($natures),
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => "Veuillez préciser la ou les natures des symptômes.",
                        'groups' => [$group],
                    ]),
                ],
            ]);

            if ($avecAutreNature) {
                $builder->add('nature_autre', Form\TextType::class, [
                    'label' => "Si autre, précisez",
                    'required' => false,
                    'constraints' => [
                        new Assert\NotBlank([
                            'message' => "Si autre, vous devez préciser.",
                            'groups' => [$groupNatureAutre],
                        ]),
                    ],
                ]);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'html5' => false,
            'label' => false,
            'groups' => [Constraint::DEFAULT_GROUP],
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
            $groups && $form->has(FormHelper::SECTION_KILLSWITCH) &&
            $form->get(FormHelper::SECTION_KILLSWITCH)->getData()
        ) {
            $groups[] = self::computeGroupFromClass();
        }

        if (
            $groups && $form->has('nature') &&
            \in_array("Autre", $form->get('nature')->getData())
        ) {
            $groups[] = self::computeGroupFromClass() . 'NatureAutre';
        }

        return $groups;
    }
}

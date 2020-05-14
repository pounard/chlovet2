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

class ProblemFormType extends AbstractType
{
    use ParentActivatedValidationTrait;

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $triggerId = \uniqid('trig-');

        $builder->add(FormHelper::SECTION_KILLSWITCH, Form\CheckboxType::class, [
            'attr' => [
                'data-trigger' => $triggerId,
            ],
            'required' => false,
        ]);

        $builder->add('depuis', Form\DateType::class, [
            'label' => "Si oui, précisez la date",
            'data' => null,
            'attr' => [
                'placeholder' => "Saississez la date au format " . (new \DateTimeImmutable())->format('d/m/Y'),
                'data-show-if' => $triggerId,
            ],
            'widget' => 'single_text',
            'format' => 'dd/MM/yyyy',
            'required' => false,
            'html5' => false,
            'constraints' => [
                new Assert\NotBlank([
                    'message' => "Veuillez renseigner la date d'apparition des symptômes.",
                    'groups' => [$options['groups']],
                ]),
            ],
        ]);

        if ($options['natures']) {
            $builder->add('nature', Form\ChoiceType::class, [
                'label' => "Si oui, précisez la nature du problème (plus choix sont possibles)",
                'attr' => [
                    'data-show-if' => $triggerId,
                ],
                'expanded' => true,
                'multiple' => true,
                'required' => false,
                'choices' => FormHelper::map($options['natures']),
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => "Veuillez préciser la ou les natures des symptômes.",
                        'groups' => [$options['groups']],
                    ]),
                ],
            ]);
        }
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

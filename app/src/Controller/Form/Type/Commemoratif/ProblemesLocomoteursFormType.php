<?php

declare(strict_types=1);

namespace App\Controller\Form\Type\Commemoratif;

use App\Controller\Form\FormHelper;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type as Form;

final class ProblemesLocomoteursFormType extends ProblemesFormType
{
    protected static ?string $group = 'ProblemesLocomoteurs';

    /**
     * {@inheritdoc}
     */
    protected function getNatures(): ?array
    {
        return null;
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

        $builder->add('difficultes', Form\ChoiceType::class, [
            'label' => "Difficultés pour",
            'expanded' => true,
            'multiple' => true,
            'required' => false,
            'choices' => FormHelper::map([
                "Monter",
                "Descendre",
                "Sauter",
            ]),
        ]);

        $builder->add('boiteries', Form\ChoiceType::class, [
            'label' => "Boiterie",
            'expanded' => true,
            'multiple' => true,
            'required' => false,
            'choices' => FormHelper::map([
                "Intermittente",
                "Ne pose pas du tout",
                "Plus marquée au levé",
                "Plus marquée à l'effort",
            ]),
        ]);
    }
}

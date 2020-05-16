<?php

declare(strict_types=1);

namespace App\Controller\Form\Type\Commemoratif;

final class ProblemesAuxYeuxFormType extends ProblemesFormType
{
    protected static ?string $group = 'ProblemesAuxYeux';

    /**
     * {@inheritdoc}
     */
    protected function getNatures(): ?array
    {
        return [
            "Œil plus fermé",
            "Frottements",
            "Écoulement clair",
            "Écoulement épais",
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function avecAutreNature(): bool
    {
        return true;
    }
}

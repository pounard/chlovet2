<?php

declare(strict_types=1);

namespace App\Controller\Form\Type\Commemoratif;

final class ProblemesRespiratoiresFormType extends ProblemesFormType
{
    protected static ?string $group = 'ProblemesRespiratoires';

    /**
     * {@inheritdoc}
     */
    protected function getNatures(): ?array
    {
        return [
            "Eternuements",
            "Toux",
            "Difficultés pour respirer, essoufflement",
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

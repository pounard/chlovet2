<?php

declare(strict_types=1);

namespace App\Controller\Form\Type\Commemoratif;

final class ProblemesCutanesFormType extends ProblemesFormType
{
    protected static ?string $group = 'ProblemesCutanes';

    /**
     * {@inheritdoc}
     */
    protected function getNatures(): ?array
    {
        return [
            "Se gratte/se lèche",
            "Croutes",
            "Plaies à vif",
            "Mauvaise odeur",
            "Dépilations",
            "Présence de puces",
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

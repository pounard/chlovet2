<?php

declare(strict_types=1);

namespace App\Twig;

use App\Entity\Form;
use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;

final class FormExtension extends AbstractExtension
{
    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction(
                'app_form_label',
                fn ($type) => Form::getLabel($type),
                ['is_safe' => ['html']]
            ),
        ];
    }
}

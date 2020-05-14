<?php

declare(strict_types=1);

namespace App\Controller\Form;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

final class FormHelper
{
    const PLACEHOLDER_DEFAULT = 'Choississez une réponse...';
    const SECTION_KILLSWITCH = 'oui';

    public static function map(array $values)
    {
        return \array_combine($values, $values);
    }

    public static function cleanupPostData(array $values): array
    {
        if (\array_key_exists(self::SECTION_KILLSWITCH, $values)) {
            if (!$values[self::SECTION_KILLSWITCH]) {
                return [];
            }
        }

        return \array_map(
            fn ($value) => \is_array($value) ? self::cleanupPostData($value) : $value,
            $values
        );
    }

    public static function humanReadableValue($value): string
    {
        if (\is_array($value)) {
            return \implode(', ', \array_map(fn ($value) => self::humanReadableValue($value), $value));
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format('d/m/Y');
        }

        if (\is_object($value) && \method_exists($value, '__toString()')) {
            return $value->__toString();
        }

        return (string)$value;
    }

    public static function humanReadableFormData(FormInterface $form, $values)
    {
        if (\is_array($values) && !$form->getConfig()->getType() !== ChoiceType::class) {
            $ret = [];

            foreach ($form as $name => $child) {
                $value = null;
                if (\array_key_exists($name, $values)) {
                    $value = self::humanReadableFormData($child, $values[$name]);
                }

                if (self::SECTION_KILLSWITCH === $name && !$value) {
                    return "Non";
                }

                $label = $child->getConfig()->getOption('label');

                if (!$label) {
                    $label = $name;
                }

                $ret[$label] = $value ?? '<Non renseigé>';
            }

            return $ret;
        }

        return self::humanReadableValue($values);
    }

    private function __construct()
    {
    }
}

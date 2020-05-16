<?php

declare(strict_types=1);

namespace App\Entity;

final class Form
{
    const TYPE_COMMEMORATIF = 'commemoratif';

    public static function getAll(): array
    {
        return [
            self::TYPE_COMMEMORATIF => "Commémoratif",
        ];
    }

    public function getLabel(string $type): string
    {
        return self::getAll()[$type] ?? "Erreur";
    }

    public static function isValid(string $type): bool
    {
        return self::TYPE_COMMEMORATIF === $type;
    }
}

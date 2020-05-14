<?php

namespace App\Migrations;

use Goat\Driver\Platform\Escaper\Escaper;

/**
 * Proxifies escaper to change placeholder for doctrine.
 */
class DoctrineMigrationEscaper implements Escaper
{
    private Escaper $decorated;

    public function __construct(Escaper $decorated)
    {
        $this->decorated = $decorated;
    }

    /**
     * {@inheritdoc}
     */
    public function escapeIdentifier(string $string): string
    {
        return $this->decorated->escapeIdentifier($string);
    }

    /**
     * {@inheritdoc}
     */
    public function escapeIdentifierList($strings): string
    {
        return $this->decorated->escapeIdentifierList($strings);
    }

    /**
     * {@inheritdoc}
     */
    public function escapeLiteral(string $string): string
    {
        return $this->decorated->escapeLiteral($string);
    }

    /**
     * {@inheritdoc}
     */
    public function escapeLike(string $string): string
    {
        return $this->decorated->escapeLike($string);
    }

    /**
     * {@inheritdoc}
     */
    public function unescapePlaceholderChar(): string
    {
        return '??';
    }

    /**
     * {@inheritdoc}
     */
    public function writePlaceholder(int $index): string
    {
        return '?';
    }

    /**
     * {@inheritdoc}
     */
    public function getEscapeSequences(): array
    {
        return $this->decorated->getEscapeSequences();
    }

    /**
     * {@inheritdoc}
     */
    public function escapeBlob(string $word): string
    {
        return $this->decorated->escapeBlob($word);
    }

    /**
     * {@inheritdoc}
     */
    public function unescapeBlob($resource): ?string
    {
        return $this->decorated->unescapeBlob($resource);
    }
}

<?php

declare(strict_types=1);

namespace App\Entity;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\Mime\MimeTypes;

final class EditorialFile
{
    private \DateTimeImmutable $created_at;
    private int $filesize;
    private ?int $id;
    private string $mimetype = 'application/octet-stream';
    private string $name;
    private ?string $sha1sum;
    private string $uri;

    /**
     * Use static methods or hydration.
     *
     * @codeCoverageIgnore
     */
    private function __construct(string $uri)
    {
        self::ensureFilename($uri);

        $this->created_at = new \DateTimeImmutable('@'.\filemtime($uri));
        $this->uri = $uri;
        $this->filesize = \filesize($uri);
        $this->mimetype = self::guessUriMimeType($uri);
        $this->name = \basename($uri);
        $this->sha1sum = \sha1_file($uri);
    }

    /**
     * Guess URI mime type
     */
    public static function guessUriMimeType(string $uri): string
    {
        return MimeTypes::getDefault()->guessMimeType($uri) ?? 'application/octet-stream';
    }

    /**
     * Create instance from existing file
     */
    public static function fromFile(string $uri): self
    {
        return new self($uri);
    }

    /**
     * Get file creation date
     */
    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->created_at ?? ($this->created_at = \filemtime($this->uri));
    }

    /**
     * Get identifier if file is registed into database
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Does file exists in database
     */
    public function isTemporary(): bool
    {
        return empty($this->id);
    }

    /**
     * Get file real path
     */
    public function getURI(): string
    {
        return $this->uri;
    }

    /**
     * Get relative path of file starting from given working directory
     *
     * @return ?string
     *   Null if the file is not within the given path
     */
    public function getRelativePath(string $workingDirectory): ?string
    {
        $filesystem = new Filesystem();

        // Considers that file is already relative, just give that.
        if (!$filesystem->isAbsolutePath($this->uri)) {
            return $this->uri;
        }
        if (!$filesystem->isAbsolutePath($workingDirectory)) {
            return null;
        }

        return ($path = $filesystem->makePathRelative($this->uri, $workingDirectory)) ? \trim($path, '/') : null;
    }

    /**
     * Get file original name, without the path
     */
    public function getName(): string
    {
        return $this->name ?? ($this->name = \basename($this->uri));
    }

    /**
     * Get file size in bytes
     */
    public function getFilesize(): int
    {
        return (int)($this->filesize ?? ($this->filesize = \basename($this->uri)));
    }

    /**
     * Get file size in bytes
     */
    public function getMimetype(): string
    {
        return $this->mimetype;
    }

    /**
     * Get a SHA1 sum of the file, beware, return value can be empty in case of any error
     */
    public function getSha1Summary(): ?string
    {
        if ($this->sha1sum) {
            return $this->sha1sum;
        }

        self::ensureFilename($this->uri);

        return $this->sha1sum = \sha1_file($this->uri);
    }

    /**
     * Get SPL/Symfony file representation
     */
    public function getFileInfo(): File
    {
        return new File($this->uri, false);
    }

    /**
     * Does file exists
     */
    public function exists(): bool
    {
        return \file_exists($this->uri);
    }

    /**
     * Test if filename is a file and is readable,
     * throw exception if not.
     */
    public static function ensureFilename(string $filename)
    {
        if (!\is_file($filename)) {
            throw new FileNotFoundException($filename);
        }
        if (!\is_readable($filename)) {
            throw new AccessDeniedException($filename);
        }
    }
}

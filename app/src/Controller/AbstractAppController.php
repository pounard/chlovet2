<?php

declare(strict_types=1);

namespace App\Controller;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

abstract class AbstractAppController extends AbstractController implements LoggerAwareInterface
{
    use ControllerTrait;
    use LoggerAwareTrait;

    protected function getLogger(): LoggerInterface
    {
        return $this->logger ?? new NullLogger();
    }

    protected function handleError(\Throwable $e)
    {
        if ($this->isDebug()) {
            throw $e;
        }

        $this->getLogger()->critical("{class} exception while saving, message is: {message}" , ['class' => \get_class($e), 'message' => $e->getMessage(), 'exception' => $e]);

        $this->addFlash('error', "Une erreur est survenue");
    }
}

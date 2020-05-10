<?php

return [
    \Doctrine\Bundle\DoctrineBundle\DoctrineBundle::class => ['all' => true],
    \Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle::class => ['all' => true],
    \GeneratedHydrator\Bridge\Symfony\GeneratedHydratorBundle::class => ['all' => true],
    \Goat\Query\Symfony\GoatQueryBundle::class => ['all' => true],
    \Goat\Mapper\Bridge\Symfony\GoatMapperBundle::class => ['all' => true],
    \MakinaCorpus\Calista\Bridge\Symfony\CalistaBundle::class => ['all' => true],
    \MakinaCorpus\FilechunkBundle\FilechunkBundle::class => ['all' => true],
    \Symfony\Bundle\FrameworkBundle\FrameworkBundle::class => ['all' => true],
    \Symfony\Bundle\SecurityBundle\SecurityBundle::class => ['all' => true],
    \Symfony\Bundle\TwigBundle\TwigBundle::class => ['all' => true],
    \Symfony\Bundle\WebProfilerBundle\WebProfilerBundle::class => ['dev' => true, 'test' => true],
    \Symfony\WebpackEncoreBundle\WebpackEncoreBundle::class => ['all' => true],
];

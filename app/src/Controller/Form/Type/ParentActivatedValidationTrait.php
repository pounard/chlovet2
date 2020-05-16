<?php

declare(strict_types=1);

namespace App\Controller\Form\Type;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraint;

/**
 * All credits to @llo (taken from DinoSAUR).
 */
trait ParentActivatedValidationTrait
{
    /**
     * Default group for this class.
     */
    protected static ?string $group = null;

    /**
     * Get default group for this class.
     */
    public static function computeGroupFromClass(): string
    {
        return static::$group ?? (static::$group = \str_replace('\\', '', static::class));
    }

    /**
     * Declares the new "groups" option.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $this->doConfigureValidationGroupsOptions($resolver);
    }

    /**
     * Real validation group options configuration.
     */
    final protected function doConfigureValidationGroupsOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('groups', [Constraint::DEFAULT_GROUP]);
        $resolver->setDefault('validation_groups', function (FormInterface $form) {
            return $this->doResolveValidationGroups($form);
        });
    }

    /**
     * Determines if the form must be validated by resolving validation groups.
     */
    final protected function doResolveValidationGroups(FormInterface $form): ?array
    {
        $groups = $form->getConfig()->getOption('groups');
        $parent = $form->getParent();

        if ($parentGroups = $parent->getConfig()->getOption('validation_groups')) {
            if (!\is_string($parentGroups) && \is_callable($parentGroups)) {
                $parentGroups = \call_user_func($parentGroups, $parent);
                if (null !== $parentGroups && !\is_array($parentGroups)) {
                    $parentGroups = (array) $parentGroups;
                }
            }
            if ($parentGroups && !\array_intersect($groups, $parentGroups)) {
                return null;
            }
        }

        return $this->resolveValidationGroups($form, $groups);
    }

    /**
     * Resolve validation groups, implement this.
     */
    abstract protected function resolveValidationGroups(FormInterface $form, ?array $groups): ?array;
}

<?php
declare(strict_types = 1);

namespace TYPO3\CMS\Core\DataHandling\Model;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Core\Utility\MathUtility;

/**
 * A RecordState is an abstract description of a record that consists of
 *
 * - an EntityContext describing the "variant" of a record
 * - an EntityPointer that describes the node where the record is stored
 * - an EntityUidPointer of the record the RecordState instance represents
 *
 * Instances of this class are created by the RecordStateFactory.
 */
class RecordState
{
    /**
     * @var EntityContext
     */
    protected $context;

    /**
     * @var EntityPointer
     */
    protected $node;

    /**
     * @var EntityUidPointer
     */
    protected $subject;

    /**
     * @var EntityPointerLink
     */
    protected $languageLink;

    /**
     * @var EntityPointerLink
     */
    protected $versionLink;

    /**
     * @param EntityContext $context
     * @param EntityPointer $node
     * @param EntityUidPointer $subject
     */
    public function __construct(EntityContext $context, EntityPointer $node, EntityUidPointer $subject)
    {
        $this->context = $context;
        $this->node = $node;
        $this->subject = $subject;
    }

    /**
     * @return EntityContext
     */
    public function getContext(): EntityContext
    {
        return $this->context;
    }

    /**
     * @return EntityPointer
     */
    public function getNode(): EntityPointer
    {
        return $this->node;
    }

    /**
     * @return EntityUidPointer
     */
    public function getSubject(): EntityUidPointer
    {
        return $this->subject;
    }

    /**
     * @return EntityPointerLink
     */
    public function getLanguageLink(): ?EntityPointerLink
    {
        return $this->languageLink;
    }

    /**
     * @param EntityPointerLink|null $languageLink
     * @return static
     */
    public function withLanguageLink(?EntityPointerLink $languageLink): self
    {
        if ($this->languageLink === $languageLink) {
            return $this;
        }
        $target = clone $this;
        $target->languageLink = $languageLink;
        return $target;
    }

    /**
     * @return EntityPointerLink
     */
    public function getVersionLink(): ?EntityPointerLink
    {
        return $this->versionLink;
    }

    /**
     * @param EntityPointerLink|null $versionLink
     * @return static
     */
    public function withVersionLink(?EntityPointerLink $versionLink): self
    {
        if ($this->versionLink === $versionLink) {
            return $this;
        }
        $target = clone $this;
        $target->versionLink = $versionLink;
        return $target;
    }

    /**
     * @return bool
     */
    public function isNew(): bool
    {
        return !MathUtility::canBeInterpretedAsInteger(
            $this->subject->getIdentifier()
        );
    }

    /**
     * Resolve identifier of node used as aggregate. For translated pages
     * that would result in the `uid` of the outer-most language parent page.
     *
     * @return string
     */
    public function resolveAggregateNodeIdentifier(): string
    {
        if ($this->subject->isNode()
            && $this->context->getLanguageId() > 0
            && $this->languageLink !== null
        ) {
            return $this->languageLink->getHead()->getSubject()->getIdentifier();
        }

        return $this->node->getIdentifier();
    }
}
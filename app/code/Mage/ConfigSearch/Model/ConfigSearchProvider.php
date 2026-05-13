<?php

declare(strict_types=1);

namespace Mage\ConfigSearch\Model;

use Magento\Config\Model\Config\Structure;
use Magento\Config\Model\Config\Structure\Element\AbstractComposite;

class ConfigSearchProvider
{
    private const MAX_RESULTS = 50;

    public function __construct(
        private readonly Structure $configStructure
    ) {
    }

    /**
     * Search system configuration fields by label
     *
     * @return array<int, array{label: string, breadcrumb: string, section: string, group: string, field: string, type: string}>
     */
    public function search(string $query): array
    {
        $query = trim($query);
        if (mb_strlen($query) < 2) {
            return [];
        }

        $results = [];
        $this->findInStructure($this->configStructure->getTabs(), $query, [], $results);

        return $results;
    }

    /**
     * Recursively traverse config structure and find matching elements
     *
     * @param array<int, array{label: string, type: string}> $pathParts
     */
    private function findInStructure(
        \Iterator $iterator,
        string $query,
        array $pathParts,
        array &$results,
        string $sectionId = '',
        string $groupId = ''
    ): void {
        foreach ($iterator as $element) {
            if (count($results) >= self::MAX_RESULTS) {
                return;
            }

            // Collect data from flyweight BEFORE moving to next element
            $elementId = $element->getId();
            $elementLabel = (string) $element->getLabel();
            $elementData = $element->getData();
            $elementType = $elementData['_elementType'] ?? '';
            $isComposite = $element instanceof AbstractComposite;
            $hasChildren = $isComposite && $element->hasChildren();

            // Track section/group IDs for URL building
            $currentSectionId = $sectionId;
            $currentGroupId = $groupId;

            if ($elementType === 'section') {
                $currentSectionId = $elementId;
                $currentGroupId = '';
            } elseif ($elementType === 'group') {
                $currentGroupId = $elementId;
            }

            // Check if this element's label matches the query
            if ($elementLabel !== '' && mb_stripos($elementLabel, $query) !== false) {
                $currentParts = $pathParts;
                $currentParts[] = ['label' => $elementLabel, 'type' => $elementType];

                $results[] = [
                    'label' => $elementLabel,
                    'breadcrumbParts' => $currentParts,
                    'section' => $currentSectionId,
                    'group' => $currentGroupId,
                    'field' => $elementType === 'field' ? $elementId : '',
                    'type' => $elementType,
                ];
            }

            // Recurse into children (tabs > sections > groups > fields)
            if ($hasChildren) {
                $childParts = $pathParts;
                $childParts[] = ['label' => $elementLabel, 'type' => $elementType];
                $childIterator = $element->getChildren();
                $this->findInStructure(
                    $childIterator,
                    $query,
                    $childParts,
                    $results,
                    $currentSectionId,
                    $currentGroupId
                );
            }
        }
    }
}

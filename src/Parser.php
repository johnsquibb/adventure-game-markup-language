<?php

namespace AdventureGameMarkupLanguage;

use AdventureGameMarkupLanguage\Exception\InvalidTypeException;
use AdventureGameMarkupLanguage\Hydrator\AssignmentHydratorInterface;
use AdventureGameMarkupLanguage\Hydrator\ContainerEntityHydrator;
use AdventureGameMarkupLanguage\Hydrator\EventEntityHydrator;
use AdventureGameMarkupLanguage\Hydrator\ItemEntityHydrator;
use AdventureGameMarkupLanguage\Hydrator\LocationEntityHydrator;
use AdventureGameMarkupLanguage\Hydrator\PortalEntityHydrator;
use AdventureGameMarkupLanguage\Hydrator\TriggerEntityHydrator;
use AdventureGameMarkupLanguage\Syntax\Assignment;
use AdventureGameMarkupLanguage\Syntax\ListAssignment;
use AdventureGameMarkupLanguage\Syntax\MultilineAssignment;
use AdventureGameMarkupLanguage\Syntax\Type;

/**
 * Class Parser parses syntax tree into hydrator objects.
 * @package AdventureGameMarkupLanguage
 */
class Parser
{
    /**
     * Parse AGML syntax tree into adventure game hydrators.
     * @param SyntaxTree $tree
     * @return array
     * @throws InvalidTypeException
     */
    public function parseTree(SyntaxTree $tree): array
    {
        $hydrators = [];
        $nodes = $tree->getNodes();

        $index = 0;
        while ($index < count($nodes)) {
            if ($nodes[$index] instanceof Type) {
                $hydrator = $this->parseType($nodes[$index]);
                $index++;

                while ($index < count($nodes)) {
                    $node = $nodes[$index];
                    // Stop parsing this type when new type encountered.
                    if ($node instanceof Type) {
                        break;
                    }

                    if ($node instanceof Assignment) {
                        $this->parseAssignment($hydrator, $nodes[$index]);
                    }

                    if ($node instanceof ListAssignment) {
                        $this->parseListAssignment($hydrator, $nodes[$index]);
                    }

                    if ($node instanceof MultilineAssignment) {
                        $this->parseMultilineAssignment($hydrator, $nodes[$index]);
                    }

                    $index++;
                }

                $hydrators[] = $hydrator;
            }
        }

        return $hydrators;
    }

    /**
     * Parse type into Hydrator.
     * @param Type $type
     * @return AssignmentHydratorInterface
     * @throws InvalidTypeException
     */
    private function parseType(Type $type): AssignmentHydratorInterface
    {
        $identifier = $type->getIdentifier();

        return match ($identifier) {
            Literals::TYPE_ITEM => new ItemEntityHydrator(),
            Literals::TYPE_PORTAL => new PortalEntityHydrator(),
            Literals::TYPE_CONTAINER => new ContainerEntityHydrator(),
            Literals::TYPE_LOCATION => new LocationEntityHydrator(),
            Literals::TYPE_TRIGGER => new TriggerEntityHydrator(),
            Literals::TYPE_EVENT => new EventEntityHydrator(),
            default => throw new InvalidTypeException("Invalid type: $identifier"),
        };
    }

    /**
     * Parse assignment.
     * @param AssignmentHydratorInterface $hydrator
     * @param Assignment $assignment
     */
    private function parseAssignment(
        AssignmentHydratorInterface $hydrator,
        Assignment $assignment
    ): void {
        $hydrator->assign($assignment->getVariable(), $assignment->getValues());
    }

    /**
     * Parse list assignment.
     * @param AssignmentHydratorInterface $hydrator
     * @param ListAssignment $assignment
     */
    private function parseListAssignment(
        AssignmentHydratorInterface $hydrator,
        ListAssignment $assignment
    ): void {
        $hydrator->assign($assignment->getVariable(), $assignment->getValues());
    }

    /**
     * Parse multi-line assignment.
     * @param AssignmentHydratorInterface $hydrator
     * @param MultilineAssignment $assignment
     */
    private function parseMultilineAssignment(
        AssignmentHydratorInterface $hydrator,
        MultilineAssignment $assignment
    ): void {
        $hydrator->assign($assignment->getSection(), $assignment->getLines());
    }

}
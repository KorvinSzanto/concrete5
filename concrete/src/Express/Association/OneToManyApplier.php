<?php

namespace Concrete\Core\Express\Association;

use Concrete\Core\Entity\Express\Association;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Express\EntryList;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;
use Illuminate\Support\Arr;

class OneToManyApplier implements ApplierInterface
{

    use ApplierTrait;

    /**
     * The EntityManager we plan to use to apply associations
     *
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $entityManager;

    public function __construct(EntityManagerInterface $em)
    {
        $this->entityManager = $em;
    }


    public function associate(Association $association, Entry $entry, $input)
    {
        if (!is_array($input)) {
            throw new \InvalidArgumentException(t('Invalid input argument, ManyToMany input must be an array of entries.'));
        }
        $entries = $input;

        // First create the owning entry association
        $manyAssociation = $entry->getEntryAssociation($association);
        if (!is_object($manyAssociation)) {
            $manyAssociation = new Entry\ManyAssociation();
            $manyAssociation->setAssociation($association);
            $manyAssociation->setEntry($entry);
        }

        // Locate the inverse association
        $inverseAssociation = $this->getInverseAssociation($association, $this->entityManager);

        foreach($entries as $selectedEntry) {
            $oneAssociation = $selectedEntry->getEntryAssociation($inverseAssociation);
            if ($oneAssociation) {
                // Let's see if THAT entry relates back to this.
                $oneEntry = $oneAssociation->getSelectedEntry();
                if ($oneEntry) {
                    $oneEntryAssociation = $oneEntry->getEntryAssociation($association);
                    if ($oneEntryAssociation) {
                        foreach($oneEntryAssociation->getSelectedEntriesCollection() as $oneEntryAssociationEntry) {
                            if ($oneEntryAssociationEntry->getEntry()->getId() == $selectedEntry->getId()) {
                                $this->entityManager->remove($oneEntryAssociationEntry);
                            }
                        }
                        $this->entityManager->flush();
                    }
                }
                $this->entityManager->remove($oneAssociation);
            }
        }

        $this->entityManager->flush();

        foreach($manyAssociation->getSelectedEntriesCollection() as $manyAssociationSelectedEntry) {
            $this->entityManager->remove($manyAssociationSelectedEntry);
        }
        $this->entityManager->flush();

        $associationAssociatedEntries = [];
        $displayOrder = 0;
        foreach($entries as $associatedEntry) {
            $associationEntry = new Entry\AssociationEntry();
            $associationEntry->setEntry($associatedEntry);
            $associationEntry->setAssociation($manyAssociation);
            $associationEntry->setDisplayOrder($displayOrder);
            $displayOrder++;
            $associationAssociatedEntries[] = $associationEntry;
        }

        $inverseAssociation = $this->getInverseAssociation($association, $this->entityManager);

        $manyAssociation->setSelectedEntries($associationAssociatedEntries);
        $this->entityManager->persist($manyAssociation);
        $this->entityManager->flush();

        // Now, we go to the inverse side, and we get all possible entries.
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $result = $queryBuilder->select('ae.id')->from(Entry\AssociationEntry::class, 'ae')
            ->join('ae.association', 'ea')
            ->join('ea.association', 'many')
            ->where('ea.association=:association')
            ->getQuery()->execute([
                'association' => $inverseAssociation
            ], AbstractQuery::HYDRATE_ARRAY);

        if ($result) {
            $ids = Arr::flatten($result);
            $queryBuilder->delete(Entry\AssociationEntry::class, 'ae')->where($queryBuilder->expr()->in('ae.id', $ids))
                ->getQuery()->execute();
        }

        foreach($entries as $possibleResult) {
            $oneAssociation = $possibleResult->getEntryAssociation($inverseAssociation);
            if (!is_object($oneAssociation)) {
                $oneAssociation = new Entry\OneAssociation();
                $oneAssociation->setAssociation($inverseAssociation);
                $oneAssociation->setEntry($possibleResult);
            }

            $collection = $oneAssociation->getSelectedEntriesCollection();

            if (in_array($possibleResult, $entries)) {
                // If the item appears in the request (meaning we want it to be selected):
                if (count($collection) == 0) {
                    // Nothing is currently selected, so we have to add this one.
                    $oneAssociationEntry = new Entry\AssociationEntry();
                    $oneAssociationEntry->setEntry($entry);
                    $oneAssociationEntry->setAssociation($oneAssociation);
                    $oneAssociation->setSelectedEntry($oneAssociationEntry);
                    $this->entityManager->persist($oneAssociation);
                } else {
                    foreach($collection as $result) {
                        if ($result->getId() == $entry->getId()) {
                            // The result is already selected, so we don't reselect it.
                            continue;
                        } else {
                            // We are currently set to a different result. So we need to delete this association and
                            // Set it to this one.
                            $oneAssociationCollection = $oneAssociation->getSelectedEntriesCollection();
                            foreach($oneAssociationCollection as $oneAssociationEntry) {
                                $this->entityManager->remove($oneAssociationEntry);
                            }
                        }
                    }
                }
            } else {
                if (count($collection) > 0) {
                    foreach($collection as $result) {
                        if ($result->getEntry()->getId() == $entry->getId()) {
                            // The result is currently in the collection, so let's remove the association entirely..
                            $this->entityManager->remove($oneAssociation);
                        }
                    }
                }
            }
        }

        $this->entityManager->flush();
    }

    /**
     * Associate an entry with a list of entries
     *
     * @param \Concrete\Core\Entity\Express\Association $association
     * @param \Concrete\Core\Entity\Express\Entry $entry
     * @param \Concrete\Core\Entity\Express\Entry|\Concrete\Core\Entity\Express\Entry[] $input
     */
    public function oldassociate(Association $association, Entry $entry, $input)
    {
        if (!is_array($input)) {
            throw new \InvalidArgumentException(t('Invalid input argument, ManyToMany input must be an array of entries.'));
        }

        // Resolve related associations
        $manyAssociation = $this->resolveEntryAssociation($association, $entry);
        $inverseAssociation = $this->getInverseAssociation($association, $this->entityManager);

        // First create the owning entry association
        $selectedEntries = $manyAssociation->getSelectedEntriesCollection();
        $total = count($selectedEntries);

        // Clear out existing selected entries
        $this->clearAssociation($association, $this->entityManager);

        // Find the new selected association entries
        $newSelectedEntries = iterator_to_array($this->associatedEntries($input, $manyAssociation));

        // Apply the selected entries to the given association
        $manyAssociation->setSelectedEntries($newSelectedEntries);
        $this->entityManager->persist($manyAssociation);

        // Clear out existing inverse selected entries
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $result = $queryBuilder->select('ae.id')->from(Entry\AssociationEntry::class, 'ae')
            ->join('ae.association', 'ea')
            ->join('ea.association', 'many')
            ->where('ea.association=:association')
            ->getQuery()->execute([
                'association' => $inverseAssociation
            ], AbstractQuery::HYDRATE_ARRAY);

        if ($result) {
            $ids = Arr::flatten($result);
            $queryBuilder->delete(Entry\AssociationEntry::class, 'ae')->where($queryBuilder->expr()->in('ae.id', $ids))
                ->getQuery()->execute();
        }

        // Select the given entry on all selected entries
        foreach ($input as $entry) {
            $inverseManyAssociation = $this->resolveEntryAssociation($inverseAssociation, $entry, true);
            $inverseSelectedEntries = $this->associatedEntries([$entry], $inverseManyAssociation);
            $this->entityManager->persist($inverseManyAssociation);

            foreach ($inverseSelectedEntries as $inverseSelectedEntry) {
                $this->entityManager->persist($inverseSelectedEntry);
                $inverseManyAssociation->getSelectedEntriesCollection()->add($inverseSelectedEntry);
            }
        }

        $this->entityManager->flush();
    }

    /**
     * Get the entry association related to this
     *
     * @param \Concrete\Core\Entity\Express\Association $association
     * @param \Concrete\Core\Entity\Express\Entry $entry
     *
     * @param bool $create
     *
     * @return \Concrete\Core\Entity\Express\Entry\ManyAssociation
     */
    protected function resolveEntryAssociation(Association $association, Entry $entry, $create = false)
    {
        $manyAssociation = null;
        if (!$create) {
            $manyAssociation = $entry->getEntryAssociation($association);
        }

        if (!is_object($manyAssociation)) {
            $manyAssociation = new Entry\ManyAssociation();
            $manyAssociation->setAssociation($association);
            $manyAssociation->setEntry($entry);
        }

        return $manyAssociation;
    }

}

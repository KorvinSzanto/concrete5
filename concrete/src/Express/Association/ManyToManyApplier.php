<?php

namespace Concrete\Core\Express\Association;

use Concrete\Core\Entity\Express\Association;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Express\EntryList;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;
use Illuminate\Support\Arr;

class ManyToManyApplier implements ApplierInterface
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

    /**
     * Associate an entry with a list of entries
     *
     * @param \Concrete\Core\Entity\Express\Association $association
     * @param \Concrete\Core\Entity\Express\Entry $entry
     * @param \Concrete\Core\Entity\Express\Entry|\Concrete\Core\Entity\Express\Entry[] $input
     */
    public function associate(Association $association, Entry $entry, $input)
    {
        if (!is_array($input)) {
            throw new \InvalidArgumentException(t('Invalid input argument, ManyToMany input must be an array of entries.'));
        }

        // Resolve related associations
        $manyAssociation = $this->resolveEntryAssociation($association, $entry);
        $inverseAssociation = $this->getInverseAssociation($association, $this->entityManager);

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

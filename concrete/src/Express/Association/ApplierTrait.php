<?php

namespace Concrete\Core\Express\Association;

use Concrete\Core\Entity\Express\Association;
use Concrete\Core\Entity\Express\Entry;
use Doctrine\ORM\EntityManagerInterface;

trait ApplierTrait
{

    /**
     * Transform a given input into a list of AssociationEntries
     *
     * @param \Concrete\Core\Entity\Express\Entry|\Concrete\Core\Entity\Express\Entry[] $input
     * @param \Concrete\Core\Entity\Express\Entry\Association $association
     * @param int $displayOrder
     *
     * @return \Generator|\Concrete\Core\Entity\Express\Entry\AssociationEntry[]
     */
    protected function associatedEntries($input, Entry\Association $association, $displayOrder = 0)
    {
        $entries = is_array($input) ? $input : [$input];

        foreach ($entries as $entry) {
            $associatedAssociationEntry = new Entry\AssociationEntry();
            $associatedAssociationEntry->setEntry($entry);
            $associatedAssociationEntry->setAssociation($association);
            $associatedAssociationEntry->setDisplayOrder($displayOrder++);

            yield $associatedAssociationEntry;
        }
    }

    /**
     * Clear entries from a given association
     *
     * @param \Concrete\Core\Entity\Express\Association $association
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     *
     * @return bool
     */
    protected function clearEntryAssociation(Entry\Association $association, EntityManagerInterface $entityManager)
    {
        $eb = $entityManager->getExpressionBuilder();
        return (bool) $entityManager->createQueryBuilder()
            ->delete(Entry\Association::class)->where($eb->eq('association', ':association'))
            ->setParameter('association', $association);
    }

    /**
     * Clear entries from a given association
     *
     * @param \Concrete\Core\Entity\Express\Association $association
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     *
     * @return bool
     */
    protected function clearAssociation(Association $association, EntityManagerInterface $entityManager)
    {
        $eb = $entityManager->getExpressionBuilder();
        return (bool) $entityManager->createQueryBuilder()
            ->delete(Entry\Association::class)->where($eb->eq('association', ':association'))
            ->setParameter('association', $association);
    }

    /**
     * Get the inverse association or null if none is found
     *
     * @param \Concrete\Core\Entity\Express\Association $association
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     *
     * @return \Concrete\Core\Entity\Express\Association|null
     */
    protected function getInverseAssociation(Association $association, EntityManagerInterface $entityManager)
    {
        return $entityManager->getRepository(Association::class)
            ->findOneBy([
                'target_property_name' => $association->getInversedByPropertyName(),
                'inversed_by_property_name' => $association->getTargetPropertyName(),
                'target_entity' => $association->getSourceEntity(),
                'source_entity' => $association->getTargetEntity()
            ]);
    }

    /**å
     * Delete items from an entitymanager
     *
     * @param $results
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     */
    protected function clearResults($results, EntityManagerInterface $entityManager, $flush = true, $batch = 100)
    {
        $i = 0;

        foreach ($results as $result) {
            $entityManager->remove($result);
            $i++;

            if ($i > $batch && $flush) {
                $entityManager->flush();
                $i = 0;
            }
        }

        if ($i && $flush) {
            $entityManager->flush();
        }
    }

}

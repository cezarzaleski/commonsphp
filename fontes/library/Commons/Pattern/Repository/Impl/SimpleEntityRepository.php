<?php

namespace Commons\Pattern\Repository\Impl;

use Commons\Pattern\Repository\Repository;
use Doctrine\ORM\EntityRepository;

/**
 * Representação básica de um repositório de entidades.
 */
class SimpleEntityRepository extends EntityRepository implements Repository
{
    /**
     * {@inheritDoc}
     * @see \Commons\Pattern\Repository\Repository::save()
     */
    public function save(array $data, $id = null)
    {
        if (!$data && !$id) {
            throw new \InvalidArgumentException('Não foi possível encontrar dados para persistir.');
        }

        $entityManager = $this->getEntityManager();
        $entityName = $this->getEntityName();
        if ($id) {
            $entity = $entityManager->getReference($entityName, $id);
            $entity->fromArray($data);
        } else {
            $entity = new $entityName($data);
        }

        // Pré
        if ($id) {
            $this->preUpdate($entity);
        } else {
            $this->preInsert($entity);
        }

        $entityManager->persist($entity);
        $entityManager->flush();
        $entityManager->clear();

        // Pos
        if ($id) {
            $this->postUpdate($entity);
        } else {
            $this->postInsert($entity);
        }

        return $entity;
    }
    // ---- INSERT -----------------------------------------------------------------------------------------------------

    /**
     * Efetua operação antes de executar o insert
     */
    protected function preInsert($entity)
    {
    }

    /**
     * Efetua operações depois de executar o insert
     */
    protected function postInsert($entity)
    {
    }

    // ---- UDPATE -----------------------------------------------------------------------------------------------------

    /**
     * Efetua operações antes de executar o update
     */
    protected function preUpdate($entity)
    {
    }

    /**
     * Efetua operações depois de executar o update
     */
    protected function postUpdate($entity)
    {
    }

    // ---- DELETE -----------------------------------------------------------------------------------------------------
    /**
     * {@inheritDoc}
     * @see \Commons\Pattern\Repository\Repository::delete()
     */
    public function delete($id)
    {
        $reference = $this->getEntityManager()->getReference($this->getEntityName(), $id);
        if ($reference) {
            $this->preDelete($reference);

            $this->getEntityManager()->remove($reference);
            $this->getEntityManager()->flush();

            $this->postDelete($reference);
        }
    }

    /**
     * Efetua operações antes de executar o delete
     */
    protected function preDelete($reference)
    {
    }

    /**
     * Efetua operações depois de executar o delete
     */
    protected function postDelete($reference)
    {
    }
}

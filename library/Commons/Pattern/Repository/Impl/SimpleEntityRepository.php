<?php

namespace Commons\Pattern\Repository\Impl;

use Commons\Pattern\Repository\Repository;
use Doctrine\ORM\EntityRepository;
use Commons\Pattern\Paginator\Impl\EntityPaginator;
use Commons\Pattern\Paginator\PaginatorAware;
use Commons\Pattern\Validator\Validatable;
use Commons\Exception\ServiceException;

/**
 * Representação básica de um repositório de entidades.
 */
class SimpleEntityRepository extends EntityRepository implements Repository, PaginatorAware
{

    /**
     * @var Validatable
     */
    protected $validatable = null;

    /**
     * {@inheritDoc}
     * @see \Commons\Pattern\Repository\Repository::save()
     */
    public function save(array $data, $id = null)
    {
        if (!$data && !$id) {
            throw new \InvalidArgumentException('Não foi possível encontrar dados para persistir.');
        }

        // recupera o entityManager
        $entityManager = $this->getEntityManager();

        // recupera o nome da entidade
        $entityName = $this->getEntityName();

        // resolve quaisquer referências do repositório alocadas no array de entrada.
        $resolvedData = $this->resolveReferences($data);

        // define a ação de salvar (insert ou update)
        $saveAction = function ($entityManager, $entity) {
            $entityManager->persist($entity);
            // verificar esses dois métodos para realizar apenas para a entidade
            $entityManager->flush();
            $entityManager->clear();
        };

        $entity = null;
        // se houver Id é por que é um update
        if ($id) {
            // resgata a referência da entidade
            $entity = $entityManager->getReference($entityName, $id);
            // atualiza os dados
            $entity->fromArray($resolvedData);
            // realiza operação pré
            $this->preUpdate($entity);
            //salva a entidade
            $saveAction($entityManager, $entity);
            // realiza operação pós
            $this->postUpdate($entity);
        } else {
            // caso contrário é uma nova entidade (insert)
            $entity = new $entityName($resolvedData);
            // realiza operação pré
            $this->preInsert($entity);
            //salva a entidade
            $saveAction($entityManager, $entity);
            //realiza operação pós
            $this->postInsert($entity);
        }

        return $entity;
    }

    /**
     * Resolve as referências no array de dados.
     *
     * @param array $data
     * @return array
     */
    protected function resolveReferences(array $data)
    {
        $resolvedData = array();

        foreach ($data as $key => $value) {
            $resolvedValue = $value;
            if ($value instanceof Reference) {
                switch ($value->getType()) {
                    case Reference::PARTIAL:
                        $resolvedValue = $this->getEntityManager()
                                              ->getPartialReference($value->getName(), $value->getId());
                        break;
                    case Reference::FULL:
                        $resolvedValue = $this->getEntityManager()
                                              ->getReference($value->getName(), $value->getId());
                        break;
                    default:
                        $resolvedValue = $value;
                        break;
                }
            }
            $resolvedData[$key] = $resolvedValue;
        }

        return $resolvedData;
    }

    // ---- VALIDATE ---------------------------------------------------------------------------------------------------

    protected function validate($entity)
    {
        if ($this->validatable !== null && !$this->validatable->isValid($entity)) {
            throw new ServiceException($this->validatable->getMessages());
        }
    }

    // ---- INSERT -----------------------------------------------------------------------------------------------------

    /**
     * Efetua operação antes de executar o insert
     */
    protected function preInsert($entity)
    {
        $this->validate($entity);
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
        $this->validate($entity);
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
            // verificar o flush para realizar apenas para a entidade
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

    /**
     * {@inheritDoc}
     * @see \Commons\Pattern\Paginator\PaginatorAware::createPaginator()
     */
    public function createPaginator()
    {
        return new EntityPaginator($this);
    }

    /**
     * Recupera instância de validação da entidade.
     *
     * @return \Commons\Pattern\Validator\Validatable
     */
    public function getValidatable()
    {
        return $this->validatable;
    }

    /**
     * Registra uma classe para validação da entidade.
     *
     * @param Validatable $validatable
     */
    public function setValidatable(Validatable $validatable)
    {
        $this->validatable = $validatable;
    }
}

<?php

namespace CommonsTest\Pattern\Service\Mock;

use Commons\Pattern\Entity\Impl\AbstractEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Mock\ExemploEntity
 *
 * @ORM\Table(name="TB_EXEMPLO")
 * @ORM\Entity(repositoryClass="Commons\Pattern\Repository\Impl\SimpleEntityRepository")
 */
class ExemploEntity extends AbstractEntity
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="ID", type="integer", length=3, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string $name
     *
     * @ORM\Column(name="NAME", type="string", length=50, nullable=false)
     */
    private $name;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
}

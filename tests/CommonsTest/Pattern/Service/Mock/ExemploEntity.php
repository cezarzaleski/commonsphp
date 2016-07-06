<?php

namespace CommonsTest\Pattern\Service\Mock;

use Commons\Pattern\Entity\Impl\AbstractEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @Assert\NotNull()
     * @Assert\Length(min=2, max=50, minMessage="At least 2 letters", maxMessage="Cannot exceed 50 letters and spaces")
     * @Assert\Regex(pattern="/\d/", match=false, message="Must not contain numbers")
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

<?php

namespace CommonsTest\Pattern\Service;

use Commons\Pattern\Service\Impl\AbstractORMCompositeService;

class DataCompositeService extends AbstractORMCompositeService
{
    public function recuperarInstanciasExemploTipadas()
    {
        $query = $this->getDataManager()->createQuery(
            'select  i.name as nome, e.name as tipo '.
            'from CommonsTest\Pattern\Service\Mock\ExemploEntity e, '.
            '     CommonsTest\Pattern\Service\Mock\InstanciaExemploEntity i '.
            'where e = i.tipoExemplo');
        return $query->getScalarResult();
    }
}

<?php
namespace TheCodingMachine\TDBM\MetaHydrator;


use MetaHydrator\Database\DBProvider;
use MetaHydrator\Exception\DBException;
use TheCodingMachine\TDBM\NoBeanFoundException;
use TheCodingMachine\TDBM\TDBMService;

class TdbmDbProvider implements DBProvider
{
    /**
     * @var TDBMService
     */
    private $tdbmService;

    public function __construct(TDBMService $tdbmService)
    {
        $this->tdbmService = $tdbmService;
    }

    /**
     * This method should:
     * - return null if NO value corresponding to primary keys is passed
     * - throw a DBException if missing some primary keys, or object not found
     * - return the found bean otherwise
     *
     * @param string $table
     * @param array $params
     * @return mixed|null
     *
     * @throws DBException
     */
    public function getObject(string $table, array $params)
    {
        $primaryKeys = $this->tdbmService->_getPrimaryKeysFromObjectData($table, $params);
        if (empty($primaryKeys)) {
            return null;
        }

        try {
            return $this->tdbmService->findObjectByPk($table, $params);
        } catch (NoBeanFoundException $e) {
            throw new DBException($e->getMessage(), 0, $e);
        }
    }

    /**
     * This method should return the bean class name corresponding to given table
     *
     * @param string $table
     * @return string
     *
     * @throws DBException
     */
    public function getClassName(string $table)
    {
        return $this->tdbmService->getBeanClassName($table);
    }
}

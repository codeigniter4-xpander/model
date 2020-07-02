<?php namespace CI4Xpander\Model;

trait ModelFactoryTrait
{
    /**
     * @param \CodeIgniter\Database\ConnectionInterface $db
     * @param \CodeIgniter\Validation\ValidationInterface $validation
     * @return self
     */
    public static function create(\CodeIgniter\Database\ConnectionInterface &$db = null, \CodeIgniter\Validation\ValidationInterface $validation = null)
    {
        return new self($db, $validation);
    }
}
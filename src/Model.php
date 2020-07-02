<?php namespace CI4Xpander;

class Model extends \CodeIgniter\Model
{
    protected $primaryKey = 'id';
    protected $useSoftDelete = true;
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';

    public function __construct(\CodeIgniter\Database\ConnectionInterface &$db = null, \CodeIgniter\Validation\ValidationInterface $validation = null)
    {
        $this->allowedFields = array_merge($this->allowedFields, [
            'created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by', 'deleted_by'
        ]);

        parent::__construct($db, $validation);
    }

    /**
     * @var ReflectionClass[]
     */
    protected $_savedEntityReflection = [];

    /**
     * @return self
     */
    public function withScheme()
    {
        if (defined("{$this->returnType}::SCHEMA")) {
            $this->select("{$this->table}.*");

            $this->_buildSchema(constant("{$this->returnType}::SCHEMA"), [
                '$name' => $this->table
            ]);
        }

        return $this;
    }

    protected function _buildSchema($schema = [], $options = [])
    {
        foreach ($schema as $name => $definition) {
            $entity = array_shift($definition);

            $source = 'id';
            if (array_key_exists('$source', $definition)) {
                $source = $definition['$source'];
                unset($definition['$source']);
            }

            $target = 'id';
            if (array_key_exists('$target', $definition)) {
                $target = $definition['$target'];
                unset($definition['$target']);
            }

            $sourceName = $options['$name'];
            $alias = "{$sourceName}_{$name}";

            if (!array_key_exists($entity, $this->_savedEntityReflection)) {
                $entityReflection = new \ReflectionClass($entity);
                $this->_savedEntityReflection[$entity] = $entityReflection;
            } else {
                $entityReflection = $this->_savedEntityReflection[$entity];
            }

            // foreach ($entityReflection->getDefaultProperties()['casts'] as $fieldName => $fieldType) {
            //     $this->select("{$alias}.{$fieldName} {$alias}_{$fieldName}");
            // }

            $this->join("{$name} {$alias}", "{$alias}.{$target} = {$sourceName}.{$source}", 'left');

            $this->_buildSchema($definition, [
                '$name' => $alias,
            ]);
        }
    }

    use \CI4Xpander\Model\ModelFactoryTrait;
}

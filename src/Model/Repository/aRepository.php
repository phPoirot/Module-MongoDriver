<?php
namespace Module\MongoDriver\Model\Repository;

use \MongoDB;


class aRepository
{
    protected $typeMap = [
        'root'     => 'MongoDB\Model\BSONDocument',
        'array'    => 'array',
        'document' => 'MongoDB\Model\BSONDocument' , // !! traversable object to fully serialize to array
    ];

    /** @var MongoDB\Database */
    protected $gateway;
    /** @var string Collection Name That Query Executed On */
    protected $collection_name;
    /** @var MongoDB\BSON\Persistable Data Object Model*/
    protected $persist;
    
    /** @var MongoDB\Database Prepared DB Gateway With Options */
    protected $_q;


    /**
     * RepositoryCategories constructor.
     *
     * @param MongoDB\Database              $mongoDb
     * @param string                        $collection
     * @param MongoDB\BSON\Persistable|null $persistable
     */
    function __construct(MongoDB\Database $mongoDb, $collection, MongoDB\BSON\Persistable $persistable = null)
    {
        $this->_giveGateway($mongoDb);
        $this->_giveDbCollection($collection);

        if ($persistable) 
            $this->setModelPersist($persistable);
        
        $this->__init();
    }

    /**
     * Initialize Object 
     * 
     */
    protected function __init()
    {
        // Implement Construct Initialization
    }
    
    
    // Options:

    /**
     * Set Data Gateway
     *
     * @param MongoDB\Database $mongoClient
     *
     * @return $this
     */
    protected function _giveGateway(MongoDB\Database $mongoClient)
    {
        // reset _query collection
        $this->_q = null;

        $this->gateway = $mongoClient;
        return $this;
    }

    /**
     * Set Db Categories Collection Name
     *
     * @param string $collectionName
     *
     * @return $this
     */
    protected function _giveDbCollection($collectionName)
    {
        // reset _query collection
        $this->_q = null;

        $this->collection_name = $collectionName;
        return $this;
    }

    /**
     * Set Data Model Object
     * 
     * @param MongoDB\BSON\Persistable $persistable
     * 
     * @return $this
     */
    function setModelPersist(MongoDB\BSON\Persistable $persistable)
    {
        // reset _query collection
        $this->_q = null;
        
        $this->typeMap['root'] = get_class($persistable);
        $this->persist = $this->typeMap['root'];
        return $this;
    }
    
    
    // ..

    /**
     * prepared mongo client with options
     * - select db collection
     * 
     * @return MongoDB\Collection
     */
    protected function _query()
    {
        if ($this->_q)
            return $this->_q;

        $db = $this->gateway;
        if ($this->persist) {
            $db = $db->withOptions([
                'typeMap' => $this->typeMap,
            ]);
        }

        $this->_q = $db->selectCollection($this->collection_name);
        return $this->_q;
    }
}

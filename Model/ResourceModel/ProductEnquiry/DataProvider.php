<?php


namespace PurpleCommerce\ProductEnquiry\Model\ResourceModel\ProductEnquiry;

use PurpleCommerce\ProductEnquiry\Model\ResourceModel\ProductEnquiry\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;

/**
 * Class PurpleCommerce\ProductEnquiry\Model\ResourceModel\ProductEnquiry\DataProvider
 */
class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var array
     */
    private $loadedData;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = []
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->dataPersistor = $dataPersistor;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->meta = $this->prepareMeta($this->meta);
    }

    /**
     *  {@inheritDoc}
     */
    public function getCollection()
    {
        if (null == $this->collection) {
            $this->collection = $this->collectionFactory->create();
        }
        return $this->collection;
    }

    /**
     * Prepares Meta
     *
     * @param array $meta
     * @return array
     */
    public function prepareMeta(array $meta)
    {
        return $meta;
    }

    /**
     *  {@inheritDoc}
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->getCollection()->getItems();
        /** @var $transaction \PurpleCommerce\ProductEnquiry\Model\ProductEnquiry */
        foreach ($items as $transaction) {
            $this->loadedData[$transaction->getId()] = $transaction->getData();
        }

        $data = $this->dataPersistor->get('transaction');
        if (!empty($data)) {
            $transaction = $this->getCollection()->getNewEmptyItem();
            $transaction->setData($data);
            $this->loadedData[$transaction->getId()] = $transaction->getData();
            $this->dataPersistor->clear('transaction');
        }

        return $this->loadedData;
    }
}

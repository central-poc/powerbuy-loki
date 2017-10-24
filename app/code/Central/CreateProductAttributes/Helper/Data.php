<?php
namespace Central\CreateProductAttributes\Helper;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\File\Csv;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Class Data
 *
 * @package Central\CreateProductAttributes\Helper
 */
class Data extends AbstractHelper
{

    const CONFIG_DATA_PATH = '/app/code/Central/CreateProductAttributes/Data/';

    const FILE_ADMIN_ROLE = 'productAttribute.csv';

    /**
     * Reader Csv
     *
     * @var Csv
     */
    protected $csvReader;

    /**
     * File System
     *
     * @var Filesystem
     */
    protected $fileSystem;

    /**
     * DirectToryList
     *
     * @var DirectoryList
     */
    protected $directoryList;


    /**
     * Data constructor.
     * 
     * @param Context       $context       Context
     * @param Csv           $csvReader     CsvReader
     * @param Filesystem    $filesystem    Filesystem
     * @param DirectoryList $directoryList directoryList
     */
    public function __construct(
        Context $context,
        Csv $csvReader,
        Filesystem $filesystem,
        DirectoryList $directoryList
    ) {
        $this->csvReader = $csvReader;
        $this->fileSystem = $filesystem;
        $this->directoryList = $directoryList;
        parent::__construct($context);

    }

    /**
     * Read Csv by path
     *
     * @param string $file    name of file
     * @param bool   $command read file by command
     * 
     * @return array
     * @throws \Exception
     */
    public function getCsvContent($file = '', $command = false)
    {
        $baseDir = $this->directoryList->getPath(DirectoryList::ROOT);
        if (!$command) {
            $fileName = $baseDir . self::CONFIG_DATA_PATH . $file;
        } else {
            $fileName = $file;
        }
        return $this->csvReader->getData($fileName);
    }

}
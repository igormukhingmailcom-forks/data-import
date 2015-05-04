<?php

namespace Ddeboer\DataImport\Writer;

/**
 * Writes to a CSV file
 */
class CsvWriter extends AbstractStreamWriter
{
    private $delimiter = ';';
    private $enclosure = '"';
    private $utf8Encoding = false;
    private $row = 1;

    /**
     * @var boolean
     */
    protected $prependHeaderRow;

    /**
     * Constructor
     *
     * @param string   $delimiter The delimiter
     * @param string   $enclosure The enclosure
     * @param resource $stream
     * @param bool     $utf8Encoding
     */
    public function __construct($delimiter = ';', $enclosure = '"', $stream = null, $utf8Encoding = false, $prependHeaderRow = false)
    {
        parent::__construct($stream);

        $this->delimiter = $delimiter;
        $this->enclosure = $enclosure;
        $this->utf8Encoding = $utf8Encoding;

        $this->prependHeaderRow = $prependHeaderRow;
    }

    /**
     * @inheritdoc
     */
    public function prepare()
    {
        if ($this->utf8Encoding) {
            fprintf($this->getStream(), chr(0xEF) . chr(0xBB) . chr(0xBF));
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function writeItem(array $item)
    {
        if ($this->prependHeaderRow && 1 == $this->row++) {
            $headers = array_keys($item);
            fputcsv($this->getStream(), $headers, $this->delimiter, $this->enclosure);
        }

        fputcsv($this->getStream(), $item, $this->delimiter, $this->enclosure);

        return $this;
    }
}
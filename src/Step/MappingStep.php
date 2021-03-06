<?php

namespace Ddeboer\DataImport\Step;

use \Ddeboer\DataImport\Exception\MappingException;
use \Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;
use \Symfony\Component\PropertyAccess\Exception\UnexpectedTypeException;
use \Symfony\Component\PropertyAccess\PropertyAccessor;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class MappingStep implements StepInterface
{
    private $mappings;

    public function __construct(array $mappings = [], PropertyAccessor $accessor = null)
    {
        $this->mappings = $mappings;
        $this->accessor = $accessor ?: new PropertyAccessor();
    }

    public function map($from, $to)
    {
        $this->mappings[$from] = $to;

        return $this;
    }

    public function process(&$item)
    {
        try {
            foreach ($this->mappings as $from => $to) {
                $value = $this->accessor->getValue($item, $from);
                $this->accessor->setValue($item, $to, $value);

                $from = str_replace(array('[',']'), '', $from);

                // Check if $item is an array, because properties can't be unset.
                // So we don't call unset for objects to prevent side affects.
                if (is_array($item) && isset($item[$from])) {
                    unset($item[$from]);
                }
            }
        } catch (NoSuchPropertyException $exception) {
            throw new MappingException('Unable to map item',null,$exception);
        } catch (UnexpectedTypeException $exception) {
            throw new MappingException('Unable to map item',null,$exception);
        }
    }
}

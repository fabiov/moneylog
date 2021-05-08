<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use Laminas\InputFilter\InputFilter;
use Laminas\InputFilter\Factory as InputFactory;
use Laminas\InputFilter\InputFilterAwareInterface;
use Laminas\InputFilter\InputFilterInterface;

/**
 * Setting.
 *
 * @ORM\Entity
 * @ORM\Table(name="setting")
 * @property int $userId
 * @property int $payDay
 * @property int $monthsRetrospective
 * @property int $stored
 */
class Setting implements InputFilterAwareInterface
{
    protected $inputFilter;

    /**
     * @ORM\Id
     * @ORM\Column(name="userId", type="integer", options={"unsigned"=true});
     * @var int
     */
    protected $userId;

    /**
     * @ORM\Column(name="payDay", type="integer", nullable=false, options={"unsigned"=true})
     * @var int
     */
    protected $payDay = 0;

    /**
     * @ORM\Column(name="monthsRetrospective", type="integer", nullable=false, options={"unsigned"=true})
     * @var int
     */
    protected $monthsRetrospective = 12;

    /**
     * @ORM\Column(name="`stored`", type="boolean", nullable=false)
     * @var boolean
     */
    protected $stored = false;

    /**
     * Magic getter to expose protected properties.
     *
     * @param string $property
     * @return mixed
     */
    public function __get($property)
    {
        return $this->$property;
    }

    /**
     * Magic setter to save protected properties.
     *
     * @param string $property
     * @param mixed $value
     */
    public function __set($property, $value)
    {
        $this->$property = $value;
    }

    /**
     * @return boolean
     */
    public function hasStored()
    {
        return $this->stored;
    }

    /**
     * @param boolean $stored
     * @return $this
     */
    public function setStored($stored)
    {
        $this->stored = (bool) $stored;
        return $this;
    }

    /**
     * Convert the object to an array.
     *
     * @return array
     */
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    /**
     * Populate from an array.
     *
     * @param array $data
     * @return Setting
     */
    public function exchangeArray(array $data)
    {
        $this->userId              = isset($data['userId']) ? $data['userId'] : null;
        $this->payDay              = isset($data['payDay']) ? $data['payDay'] : null;
        $this->monthsRetrospective = isset($data['monthsRetrospective']) ? $data['monthsRetrospective'] : null;

        if (array_key_exists('stored', $data)) {
            $this->setStored($data['stored']);
        }
        return $this;
    }

    /**
     * Set input filter
     *
     * @param  InputFilterInterface $inputFilter
     * @return InputFilterAwareInterface
     */
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        $this->inputFilter = $inputFilter;
        return $this;
    }

    /**
     * @return InputFilter
     */
    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $this->inputFilter = new InputFilter();
            $this->inputFilter->add(array(
                'filters'  => [['name' => 'Laminas\Filter\ToInt']],
                'name'     => 'payDay',
                'required' => true,
            ));
            $this->inputFilter->add(array(
                'filters'  => [['name' => 'Laminas\Filter\ToInt']],
                'name'     => 'monthsRetrospective',
                'required' => true,
            ));
            $this->inputFilter->add(array(
                'filters'  => [['name' => 'Laminas\Filter\ToInt']],
                'name'     => 'stored',
                'required' => true,
            ));
        }
        return $this->inputFilter;
    }
}

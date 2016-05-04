<?php
namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

/**
 * Setting.
 *
 * @ORM\Entity
 * @ORM\Table(name="categorie")
 * @property int $id
 * @property int $userId
 * @property string $descrizione
 * @property string $created
 * @property string $updated
 */
class Categorie implements InputFilterAwareInterface
{

    protected $inputFilter;

    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer", options={"unsigned"=true});
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="userId", type="integer", options={"unsigned"=true})
     */
    protected $userId;

    /**
     * @ORM\Column(name="descrizione", type="string")
     */
    protected $descrizione;

    /**
     * @ORM\Column(name="created", type="datetime")
     */
    protected $created;

    /**
     * @ORM\Column(name="updated", type="datetime")
     */
    protected $updated;

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
    public function exchangeArray($data = array())
    {
        $this->userId      = isset($data['userId']) ? $data['userId'] : null;
        $this->descrizione = isset($data['descrizione']) ? $data['descrizione'] : null;
        $this->created     = isset($data['created']) ? $data['created'] : null;
        $this->updated     = isset($data['updated']) ? $data['updated'] : null;
    }

    /**
     * Set input filter
     *
     * @param  InputFilterInterface $inputFilter
     * @return InputFilterAwareInterface
     */
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    /**
     * @return InputFilter
     */
    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            $inputFilter->add(array(
                'name' => 'payDay',
                'required' => true,
                'filters' => array(array('name' => 'Zend\Filter\Int'))
            ));

            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }

}
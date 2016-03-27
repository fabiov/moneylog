<?php
namespace Auth\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
// the object will be hydrated by Zend\Db\TableGateway\TableGateway
class Auth implements InputFilterAwareInterface
{
    public $id;
    public $email;
    public $name;
    public $password;
    public $salt;
    public $status;
    public $role;
    public $registrationToken;

    protected $inputFilter;

    /**
     * Hydration
     * ArrayObject, or at least implement exchangeArray. For Zend\Db\ResultSet\ResultSet to work
     *
     * @param array $data
     */
    public function exchangeArray(array $data)
    {
        $this->id                = isset($data['id'])                ? $data['id']                : null;
        $this->email             = isset($data['email'])             ? $data['email']             : null;
        $this->name              = isset($data['name'])              ? $data['name']              : null;
        $this->password          = isset($data['password'])          ? $data['password']          : null;
        $this->salt              = isset($data['salt'])              ? $data['salt']              : null;
        $this->status            = isset($data['status'])            ? $data['status']            : null;
        $this->role              = isset($data['role'])              ? $data['role']              : null;
        $this->registrationToken = isset($data['registrationToken']) ? $data['registrationToken'] : null;
    }

	// Extraction. The Registration from the tutorial works even without it.
	// The standard Hydrator of the Form expects getArrayCopy to be able to bind
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }


    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            $factory     = new InputFactory();

            $inputFilter->add($factory->createInput(array(
                'name'     => 'email',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 100,
                        ),
                    ),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name'     => 'password',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 100,
                        ),
                    ),
                ),
            )));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }

}
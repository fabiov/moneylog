<?php
namespace Auth\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
// the object will be hydrated by Zend\Db\TableGateway\TableGateway
class Auth implements InputFilterAwareInterface
{
    public $usr_id;
    public $username;
    public $password;
    public $usr_email;	
    public $usrl_id;	
    public $lng_id;	
    public $usr_active;	
    public $usr_question;	
    public $usr_answer;	
    public $usr_picture;	
    public $password_salt;
    public $usr_registration_date;
    public $usr_registration_token;	
    public $usr_email_confirmed;	

	// Hydration
	// ArrayObject, or at least implement exchangeArray. For Zend\Db\ResultSet\ResultSet to work
    public function exchangeArray($data) 
    {
        $this->usr_id     = (!empty($data['usr_id'])) ? $data['usr_id'] : null;
        $this->username = (!empty($data['username'])) ? $data['username'] : null;
        $this->password = (!empty($data['password'])) ? $data['password'] : null;
        $this->usr_email = (!empty($data['usr_email'])) ? $data['usr_email'] : null;
        $this->usrl_id = (!empty($data['usrl_id'])) ? $data['usrl_id'] : null;
        $this->lng_id = (!empty($data['lng_id'])) ? $data['lng_id'] : null;
        $this->usr_active = (isset($data['usr_active'])) ? $data['usr_active'] : null;
        $this->usr_question = (!empty($data['usr_question'])) ? $data['usr_question'] : null;
        $this->usr_answer = (!empty($data['usr_answer'])) ? $data['usr_answer'] : null;
        $this->usr_picture = (!empty($data['usr_picture'])) ? $data['usr_picture'] : null;
        $this->password_salt = (!empty($data['password_salt'])) ? $data['password_salt'] : null;
        $this->usr_registration_date = (!empty($data['usr_registration_date'])) ? $data['usr_registration_date'] : null;
        $this->usr_registration_token = (!empty($data['usr_registration_token'])) ? $data['usr_registration_token'] : null;
        $this->usr_email_confirmed = (isset($data['usr_email_confirmed'])) ? $data['usr_email_confirmed'] : null;
    }	

	// Extraction. The Registration from the tutorial works even without it.
	// The standard Hydrator of the Form expects getArrayCopy to be able to bind
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
	
	
	protected $inputFilter;

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
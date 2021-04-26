<?php
namespace Auth\Model;

use Laminas\InputFilter\Factory as InputFactory;
use Laminas\InputFilter\InputFilter;
use Laminas\InputFilter\InputFilterAwareInterface;
use Laminas\InputFilter\InputFilterInterface;

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

    public function exchangeArray(array $data)
    {
        $this->id                = $data['id'] ?? null;
        $this->email             = $data['email'] ?? null;
        $this->name              = $data['name'] ?? null;
        $this->password          = $data['password'] ?? null;
        $this->salt              = $data['salt'] ?? null;
        $this->status            = $data['status'] ?? null;
        $this->role              = $data['role'] ?? null;
        $this->registrationToken = $data['registrationToken'] ?? null;
    }

	public function getArrayCopy(): array
    {
        return get_object_vars($this);
    }


    /**
     * @throws \Exception
     */
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            $factory     = new InputFactory();

            $inputFilter->add($factory->createInput([
                'name'     => 'email',
                'required' => true,
                'filters'  => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => ['encoding' => 'UTF-8', 'max' => 100, 'min' => 1],
                    ],
                ],
            ]));

            $inputFilter->add($factory->createInput([
                'name'     => 'password',
                'required' => true,
                'filters'  => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => ['encoding' => 'UTF-8', 'max' => 100, 'min' => 1],
                    ],
                ],
            ]));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}

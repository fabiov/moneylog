<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use Laminas\Filter\StringTrim;
use Laminas\InputFilter\InputFilter;
use Laminas\InputFilter\InputFilterAwareInterface;
use Laminas\InputFilter\InputFilterInterface;

/**
 * @ORM\Entity(repositoryClass="Application\Repository\ProvisionRepository")
 * @ORM\Table(name="provision")
 */
class Provision implements InputFilterAwareInterface
{
    private $inputFilter;

    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer", options={"unsigned"=true});
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="userId", type="integer", options={"unsigned"=true});
     */
    private $userId;

    /**
     * @ORM\Column(name="valuta", type="date")
     */
    private $valuta;

    /**
     * @ORM\Column(name="importo", type="decimal", precision=8, scale=2)
     */
    private $importo;

    /**
     * @ORM\Column(name="descrizione", type="string")
     */
    private $descrizione;

    /**
     * Magic getter to expose protected properties.
     *
     * @param string $property
     * @return mixed
     */
    public function __get(string $property)
    {
        return $this->$property;
    }

    /**
     * Magic setter to save protected properties.
     *
     * @param string $property
     * @param mixed $value
     */
    public function __set(string $property, $value)
    {
        $this->$property = $value;
    }

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    public function setValuta(\DateTime $valuta): void
    {
        $this->valuta = $valuta;
    }

    public function setImporto(float $importo): void
    {
        $this->importo = $importo;
    }

    public function setDescrizione(string $descrizione): void
    {
        $this->descrizione = $descrizione;
    }

    public function getArrayCopy(): array
    {
        return get_object_vars($this);
    }

    /**
     * @throws \Exception
     */
    public function exchangeArray($data = []): void
    {
        if (isset($data['id'])) {
            $this->id = $data['id'];
        }
        if (isset($data['userId'])) {
            $this->userId = $data['userId'];
        }
        $this->valuta = isset($data['valuta']) ? new \DateTime($data['valuta']) : null;
        $this->importo = $data['importo'] ?? null;
        $this->descrizione = $data['descrizione'] ?? null;
    }

    /**
     * @param \Laminas\InputFilter\InputFilterInterface $inputFilter
     * @throws \Exception
     */
    public function setInputFilter(InputFilterInterface $inputFilter): void
    {
        throw new \Exception('Not used');
    }

    public function getInputFilter(): InputFilter
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();

            $inputFilter->add([
                'name' => 'valuta',
                'required' => true,
                'filters' => [['name' => StringTrim::class]],
            ]);
            $inputFilter->add([
                'name' => 'importo',
                'required' => true,
                'filters' => [['name' => StringTrim::class]],
            ]);
            $inputFilter->add([
                'name' => 'descrizione',
                'required' => true,
                'filters' => [['name' => StringTrim::class]],
            ]);

            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }
}

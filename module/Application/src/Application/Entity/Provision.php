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
    /**
     * @var ?InputFilterInterface
     */
    private $inputFilter;

    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer", options={"unsigned"=true});
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var int
     */
    private $id;

    /**
     * Many provisions have one user. This is the owning side.
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="userId", referencedColumnName="id")
     * @var User
     */
    private $user;

    /**
     * @ORM\Column(name="valuta", type="date")
     * @var \DateTime
     */
    private $valuta;

    /**
     * @ORM\Column(name="importo", type="decimal", precision=8, scale=2)
     * @var float
     */
    private $importo;

    /**
     * @ORM\Column(name="descrizione", type="string")
     * @var string
     */
    private $descrizione;

    public function getId(): int
    {
        return $this->id;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function getValuta(): \DateTime
    {
        return $this->valuta;
    }

    public function setValuta(\DateTime $valuta): void
    {
        $this->valuta = $valuta;
    }

    public function getImporto(): float
    {
        return $this->importo;
    }

    public function setImporto(float $importo): void
    {
        $this->importo = $importo;
    }

    public function getDescrizione(): string
    {
        return $this->descrizione;
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
    public function exchangeArray(array $data = []): void
    {
        if (isset($data['id'])) {
            $this->id = $data['id'];
        }
        if (isset($data['userId'])) {
            $this->user = $data['userId'];
        }
        if (isset($data['valuta'])) {
            $this->valuta = new \DateTime($data['valuta']);
        }
        $this->importo = $data['importo'] ?? null;
        $this->descrizione = $data['descrizione'] ?? null;
    }

    /**
     * @param \Laminas\InputFilter\InputFilterInterface $inputFilter
     * @return \Laminas\InputFilter\InputFilterAwareInterface
     * @throws \Exception
     */
    public function setInputFilter(InputFilterInterface $inputFilter): InputFilterAwareInterface
    {
        throw new \Exception('Not used');
    }

    public function getInputFilter(): InputFilterInterface
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

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
     * @ORM\JoinColumn(name="userId", referencedColumnName="id", nullable=false)
     * @var User
     */
    private $user;

    /**
     * @ORM\Column(name="date", type="date", nullable=false)
     * @var \DateTime
     */
    private $date;

    /**
     * @ORM\Column(name="amount", type="decimal", precision=8, scale=2, nullable=false)
     * @var float
     */
    private $amount;

    /**
     * @ORM\Column(name="description", type="string", nullable=false)
     * @var string
     */
    private $description;

    public function getId(): int
    {
        return $this->id;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function getDate(): \DateTime
    {
        return $this->date;
    }

    public function setDate(\DateTime $date): void
    {
        $this->date = $date;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): void
    {
        $this->amount = $amount;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getArrayCopy(): array
    {
        return get_object_vars($this);
    }

    public function exchangeArray(array $data = []): void
    {
        if (isset($data['id'])) {
            $this->id = $data['id'];
        }
        if (isset($data['userId'])) {
            $this->user = $data['userId'];
        }
        if (isset($data['valuta'])) {
            $this->date = new \DateTime($data['valuta']);
        }
        if (isset($data['importo'])) {
            $this->amount = $data['importo'];
        }
        if (isset($data['descrizione'])) {
            $this->description = $data['descrizione'];
        }
    }

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

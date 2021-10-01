<?php

declare(strict_types=1);

namespace MoneyLog\Form;

use Application\Entity\Account;
use Laminas\Form\Element\Select;
use Laminas\Form\Element\Text;
use Laminas\Form\Form;

class AccountForm extends Form
{
    /**
     * AccountForm constructor.
     * @param string $name
     */
    public function __construct($name = 'account')
    {
        parent::__construct($name);

        $this->add([
            'attributes' => ['type' => 'text', 'class' => 'form-control'],
            'name' => 'name',
            'options' => ['label' => 'Name'],
            'type' => Text::class,
        ]);
        $this->add([
            'name' => 'status',
            'options' => [
                'label' => 'Stato',
                'value_options' => [
                    Account::STATUS_CLOSED => 'Chiuso',
                    Account::STATUS_OPEN => 'Aperto',
                    Account::STATUS_HIGHLIGHT => 'In evidenza',
                ],
            ],
            'type' => Select::class,
        ]);
    }
}

<?php

declare(strict_types=1);

namespace MoneyLog\Form;

use Application\Entity\Account;
use Laminas\Form\Element\Select;
use Laminas\Form\Form;

class SearchMovementForm extends Form
{
    /**
     * @param array<Account> $accounts
     */
    public function __construct(array $accounts)
    {
        parent::__construct();

        $this->add([
            'attributes' => [
                'class' => 'form-control',
            ],
            'name' => 'account',
            'options' => [
                'value_options' => self::getValueOptions($accounts),
            ],
            'type' => Select::class,
        ]);
    }

    /**
     * @param array<Account> $accounts
     * @return array<int|string, string|array>
     */
    private static function getValueOptions(array $accounts): array
    {
        $openedAccounts = [];
        $closedAccounts = [];
        $valueOptions = [0 => 'Tutti'];

        foreach ($accounts as $account) {
            if ($account->getStatus() === Account::STATUS_CLOSED) {
                $closedAccounts[$account->getId()] = $account->getName();
            } else {
                $openedAccounts[$account->getId()] = $account->getName();
            }
        }

        if ($openedAccounts && $closedAccounts) {
            return array_merge($valueOptions, [
                'opened' => ['label' => 'Aperti', 'options' => $openedAccounts],
                'closed' => ['label' => 'Chiusi', 'options' => $closedAccounts],
            ]);
        }

        return array_merge($valueOptions, $openedAccounts, $closedAccounts);
    }
}

<?php

namespace MoneyLog\View\Helper;

use Laminas\View\Helper\AbstractHelper;

/**
 * Class BalanceModalForm
 * @package Accantona\View\Helper
 */
class BalanceModalForm extends AbstractHelper
{
    public function __invoke(): string
    {
        return <<< EOC
            <div class="modal fade" id="modal-balance" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button><h4 class="modal-title" id="myModalLabel">Conguaglia</h4>
                        </div>
                        <form id="balance-form" method="post">
                            <div class="modal-body">
                                <div class="form-group">
                                    <label>Descrizione</label><input class="form-control" name="description" placeholder="Descrizione" required="required" type="text" value="Conguaglio" />
                                </div>
                                <div class="form-group"><label>Importo</label><input class="form-control" name="amount" placeholder="0,00" required="required" step="0.01" type="number" /></div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Annulla</button><button type="submit" class="btn btn-primary">Salva</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            EOC;
    }
}

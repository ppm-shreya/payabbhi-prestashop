<?php
class PayabbhiPaymentModuleFrontController extends ModuleFrontController
{
  public $ssl = true;
  	public $display_column_left = false;
    public function initContent()
    {
        parent::initContent();

        $cart = $this->context->cart;
        $payabbhi = new payabbhi();
        $payabbhi->execPayment($cart);

        $this->context->smarty->assign(array(
              'nbProducts' => $cart->nbProducts(),
              'total' => $cart->getOrderTotal(true, Cart::BOTH),
              'this_path' => $this->module->getPathUri(),
			        'this_path_bw' => $this->module->getPathUri(),
			        'this_path_ssl' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->module->name.'/'
          )
        );

        $this->setTemplate('payment_execution.tpl');
    }
}

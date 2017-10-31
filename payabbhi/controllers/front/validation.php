<?php


class PayabbhiValidationModuleFrontController extends ModuleFrontController
{
    public function postProcess()
    {
        global $cookie;

        $payment_id = $_REQUEST['payment_id'];
        $attributes = array(
          'payment_id' => $payment_id,
          'order_id'   => $_REQUEST['order_id'],
          'payment_signature' => $_REQUEST['payment_signature'],
        );

        $cart = $this->context->cart;
        $cart_id = $cart->id;

        $payabbhi = new Payabbhi();
        $success = true;

        $accessID  = Configuration::get('PAYABBHI_ACCESS_ID'); //TODO fetch from $this if possible
        $secretKey = Configuration::get('PAYABBHI_SECRET_KEY');
        $client = new \Payabbhi\Client($accessID, $secretKey);

        try {
            $client->utility->verifyPaymentSignature($attributes);
            $payment = $client->payment->retrieve($payment_id);
        } catch (\Payabbhi\Error $e) {
            $success = false;
            $error = 'Prestashop Error: Payment failed because signature verification error';
        }

        if ($success == true)
        {
            $customer = new Customer($cart->id_customer);
            $total = (float) $cart->getOrderTotal(true, Cart::BOTH);
            $payabbhi->validateOrder($cart_id,  _PS_OS_PAYMENT_, $total, $payabbhi->displayName . ' (' . $payment->method . ')', '', array(), NULL, false, $customer->secure_key);

            Logger::addLog("Payment Successful for Order#" . $cart_id . ". Payabbhi payment ID: " . $payment_id . ". Payabbhi order ID: " . $_REQUEST['order_id'], 1);

            $order = new Order((int)$payabbhi->currentOrder);
            $payments = $order->getOrderPayments();

            if (!empty($payments)) {
              $payments[0]->transaction_id = $payment_id;
              $payments[0]->update();
            }

            $query = http_build_query(array(
                'controller'    =>  'order-confirmation',
                'id_cart'       =>  (int) $cart->id,
                'id_module'     =>  (int) $this->module->id,
                'id_order'      =>  $payabbhi->currentOrder
            ), '', '&');

            $url = 'index.php?'. $query;
            Tools::redirect($url);
        }
        else
        {
            Logger::addLog("Payment Failed for Order# ". $cart_id  . "Error: ". $error, 4);
            echo 'Error! Please contact the seller directly for assistance.</br>';
            echo 'Order Id: '.$cart_id.'</br>';
            echo 'Error: '.str_replace(' ', ' ', ucwords(str_replace('_', ' ', $response_code))).'</br>';
        }

    }
}

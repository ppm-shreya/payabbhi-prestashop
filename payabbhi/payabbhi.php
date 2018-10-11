<?php
if (!defined('_PS_VERSION_'))
  exit;

require_once('vendor/autoload.php');

class Payabbhi extends PaymentModule
{
  public function __construct()
  {
    $this->name = 'payabbhi';
    $this->tab = 'payments_gateways';
    $this->version = '1.0.1';
    $this->author = 'Payabbhi Team';
    $this->need_instance = 0;
    $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    $this->bootstrap = true;

    parent::__construct();

    $this->displayName = $this->l('Payabbhi');
    $this->description = $this->l('Prestashop module for accepting payments with Payabbhi.');
    $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
  }

  public function install()
  {
    if (Shop::isFeatureActive()) {
      Shop::setContext(Shop::CONTEXT_ALL);
    }

    if (!parent::install() || !$this->registerHook('payment') || !$this->registerHook('paymentReturn')) {
      return false;
    }

    if (!Configuration::updateValue('PAYABBHI_ACCESS_ID', '')
        || !Configuration::updateValue('PAYABBHI_SECRET_KEY', '')
        || !Configuration::updateValue('PAYABBHI_PAYMENT_DESCRIPTION', 'Pay with Card, Netbanking, Wallet')
        || !Configuration::updateValue('PAYABBHI_PAYMENT_AUTO_CAPTURE', '')) {
      return false;
    }

    return true;
  }

  public function uninstall()
  {
    if (!Configuration::deleteByName('PAYABBHI_ACCESS_ID')
        || !Configuration::deleteByName('PAYABBHI_SECRET_KEY')
        || !Configuration::deleteByName('PAYABBHI_PAYMENT_DESCRIPTION')
        || !Configuration::deleteByName('PAYABBHI_PAYMENT_AUTO_CAPTURE')
        || !parent::uninstall()) {
      return false;
    }
    return true;
  }

  private function checkConfig($accessID, $secretKey, $paymentDescription)
  {
    if (!$accessID || empty($accessID)) {
      return false;
    }

    if (!$secretKey || empty($secretKey)) {
      return false;
    }

    if (!$paymentDescription || empty($paymentDescription)) {
      return false;
    }

    return true;
  }

  public function getContent()
  {
      $output = null;

      if (Tools::isSubmit('submit'.$this->name))
      {
          $accessID           = strval(Tools::getValue('PAYABBHI_ACCESS_ID'));
          $secretKey          = strval(Tools::getValue('PAYABBHI_SECRET_KEY'));
          $paymentDescription = strval(Tools::getValue('PAYABBHI_PAYMENT_DESCRIPTION'));
          $paymentAutoCapture = strval(Tools::getValue('PAYABBHI_PAYMENT_AUTO_CAPTURE'));

          if (!$this->checkConfig($accessID, $secretKey, $paymentDescription))
          {
              $output .= $this->displayError($this->l('Invalid Configuration values'));
          }
          else
          {
              Configuration::updateValue('PAYABBHI_ACCESS_ID',  $accessID);
              Configuration::updateValue('PAYABBHI_SECRET_KEY', $secretKey);
              Configuration::updateValue('PAYABBHI_PAYMENT_DESCRIPTION', $paymentDescription);
              Configuration::updateValue('PAYABBHI_PAYMENT_AUTO_CAPTURE', $paymentAutoCapture);
              $output .= $this->displayConfirmation($this->l('Settings updated'));
          }
      }
      return $output.$this->displayForm();
  }

  public function displayForm()
  {
      // Get default language
      $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

      // Init Fields form array
      $fields_form[0]['form'] = array(
          'legend' => array(
              'title' => $this->l('Settings'),
          ),
          'input' => array(
              array(
                  'type'     => 'text',
                  'label'    => $this->l('Access ID'),
                  'desc'     => $this->l('Access ID is available as part of API keys downloaded from the Portal'),
                  'name'     => 'PAYABBHI_ACCESS_ID',
                  'size'     => 20,
                  'required' => true
              ),
              array(
                  'type'     => 'text',
                  'label'    => $this->l('Secret Key'),
                  'desc'     => $this->l('Secret Key is available as part of API keys downloaded from the Portal'),
                  'name'     => 'PAYABBHI_SECRET_KEY',
                  'size'     => 20,
                  'required' => true
              ),
              array(
                  'type'     => 'text',
                  'label'    => $this->l('Description'),
                  'desc'     => $this->l('This text will be displayed alongside payabbhi logo on payments page'),
                  'name'     => 'PAYABBHI_PAYMENT_DESCRIPTION',
                  'required' => true
              ),
              array(
                'type'   => 'select',
                'label'  => $this->l('Payment Auto Capture'),
                'desc' => $this->l('Specify whether the payment should be captured automatically. Refer to Payabbhi API Reference.'),
                'name'   => 'PAYABBHI_PAYMENT_AUTO_CAPTURE',
                'options' => array(
                  'id' => 'id_option',
                  'name' => 'name',
                  'query' => array(
                      array(
                        'id_option' => 'true',
                        'name' => 'True'
                      ),
                      array(
                        'id_option' => 'false',
                        'name' => 'False'
                      )
                  )
                )
              )
           ),
          'submit' => array(
              'title' => $this->l('Save'),
              'class' => 'btn btn-default pull-right'
          )
        );

      $helper = new HelperForm();

      // Module, token and currentIndex
      $helper->module = $this;
      $helper->name_controller = $this->name;
      $helper->token = Tools::getAdminTokenLite('AdminModules');
      $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;

      // Language
      $helper->default_form_language = $default_lang;
      $helper->allow_employee_form_lang = $default_lang;

      // Title and toolbar
      $helper->title = $this->displayName;
      $helper->show_toolbar = true;        // false -> remove toolbar
      $helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
      $helper->submit_action = 'submit'.$this->name;
      $helper->toolbar_btn = array(
          'save' =>
          array(
              'desc' => $this->l('Save'),
              'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.
              '&token='.Tools::getAdminTokenLite('AdminModules'),
          ),
          'back' => array(
              'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
              'desc' => $this->l('Back to list')
          )
      );

      // Load current value
      $helper->fields_value['PAYABBHI_ACCESS_ID'] = Configuration::get('PAYABBHI_ACCESS_ID');
      $helper->fields_value['PAYABBHI_SECRET_KEY'] = Configuration::get('PAYABBHI_SECRET_KEY');
      $helper->fields_value['PAYABBHI_PAYMENT_DESCRIPTION'] = Configuration::get('PAYABBHI_PAYMENT_DESCRIPTION');
      $helper->fields_value['PAYABBHI_PAYMENT_AUTO_CAPTURE'] = Configuration::get('PAYABBHI_PAYMENT_AUTO_CAPTURE');
      return $helper->generateForm($fields_form);
  }

  public function hookPayment($params)
  {
    global $smarty,$cart;
    $smarty->assign(array(
    'this_path'           => $this->_path,
    'this_path_ssl'       => Configuration::get('PS_FO_PROTOCOL').$_SERVER['HTTP_HOST'].__PS_BASE_URI__."modules/{$this->name}/",
    'payment_description' => Configuration::get('PAYABBHI_PAYMENT_DESCRIPTION')));
    return $this->display(__FILE__, 'payment.tpl');
  }

  protected function verify_order_amount($payabbhi_order_id, $cart) {
    $accessID  = Configuration::get('PAYABBHI_ACCESS_ID');
    $secretKey = Configuration::get('PAYABBHI_SECRET_KEY');

    $client = new \Payabbhi\Client($accessID, $secretKey);

    try {
      $payabbhi_order = $client->order->retrieve($payabbhi_order_id);
    } catch(Exception $e) {
        return false;
    }

    $payabbhi_order_args = array(
        'id'                  => $payabbhi_order_id,
        'amount'              => (int) (number_format($cart->getOrderTotal(true, 3), 2, '.', '')*100),
        'currency'            => 'INR',
        'merchant_order_id'   => (string) $cart->id,
    );

    $orderKeys = array_keys($payabbhi_order_args);

    foreach ($orderKeys as $key)
    {
        if ($payabbhi_order_args[$key] !== $payabbhi_order[$key])
        {
            return false;
        }
    }

    return true;
  }

  protected function create_payabbhi_order($cart) {
    global $cookie;

    $accessID    = Configuration::get('PAYABBHI_ACCESS_ID');
    $secretKey   = Configuration::get('PAYABBHI_SECRET_KEY');
    $autoCapture = Configuration::get('PAYABBHI_PAYMENT_AUTO_CAPTURE');

    if (!empty($autoCapture)) {
        $paymentAutoCapture = ($autoCapture === 'true');
    } else {
        $paymentAutoCapture = true;
    }
    $orderAmount = number_format($cart->getOrderTotal(true, 3), 2, '.', '')*100;
    $orderCurrency = 'INR';

    $orderParams = array('merchant_order_id'    => $cart->id,
                         'amount'               => $orderAmount,
                         'currency'             => $orderCurrency,
                         'payment_auto_capture' => $paymentAutoCapture);

    $client = new \Payabbhi\Client($accessID, $secretKey);
    $payabbhi_order_id = $client->order->create($orderParams)->id;
    $cookie->payabbhi_order_id = $payabbhi_order_id;
    return $payabbhi_order_id;
  }

  public function execPayment($cart)
    {
      global $smarty;
      global $cookie;

      $invoice = new Address((int) $cart->id_address_invoice);
      $customer = new Customer((int) $cart->id_customer);

      $contact = $invoice->phone;

      if (empty($contact)) {
          $contact = $invoice->phone_mobile;
      }

      if ($cart->id_currency != 1)
      {
          $error = 'Payabbhi Error: Currency should only be in INR';
          die(Tools::displayError($error));
      }

      try {
        $payabbhi_order_id = $cookie->payabbhi_order_id;

        if (($payabbhi_order_id === false) or
              (($payabbhi_order_id and ($this->verify_order_amount($payabbhi_order_id, $cart)) === false)))
        {
            $payabbhi_order_id = $this->create_payabbhi_order($cart);
        }

      } catch (\Payabbhi\Error $e) {
          die(Tools::displayError('Payabbhi Error: ' . $e->getMessage()));
      } catch (Exception $e) {
          die(Tools::displayError('Prestashop Error: ' . $e->getMessage()));
      }

      $checkoutArgs = array(
                  'access_id'     => Configuration::get('PAYABBHI_ACCESS_ID'),
                  'order_id'      => $payabbhi_order_id,
                  'amount'        => number_format($cart->getOrderTotal(true, 3), 2, '.', '')*100,
                  'name'          => Configuration::get('PS_SHOP_NAME'),
                  'description'   => 'Order #' . $cart->id,
                  'prefill'     => array(
                    'name'      => $invoice->firstname . ' ' . $invoice->lastname,
                    'email'     => $customer->email,
                    'contact'   => $contact
                  ),
                  'notes'       => array(
                    'merchant_order_id' => (string) $cart->id
                  )
                );

      $checkout_url = 'https://checkout.payabbhi.com/v1/checkout.js';
      $return_url = __PS_BASE_URI__."?fc=module&module=payabbhi&controller=validation";

      $smarty->assign(array(
          'checkout_url'  => $checkout_url,
          'return_url'    => $return_url,
          'checkout_args' => Tools::jsonEncode($checkoutArgs),
          'cart_id'       => $cart->id
      ));

      return $this->display(__FILE__, 'payment_execution.tpl');
    }
}

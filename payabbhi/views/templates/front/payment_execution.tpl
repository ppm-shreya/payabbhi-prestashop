<script src="{$checkout_url}"></script>

{capture name=path}
<a href="{$link->getPageLink('order', true, NULL, " step=3 ")|escape:'html':'UTF-8'}" title="{l s='Go back to the Checkout' mod='payabbhi'}">{l s='Checkout' mod='payabbhi'}</a><span class="navigation-pipe">{$navigationPipe}</span>{l s='Pay With Payabbhi'
mod='payabbhi'} {/capture}

<h1 class="page-heading">
    {l s='Order summary' mod='payabbhi'}
</h1>

{assign var='current_step' value='payment'}
{include file="$tpl_dir./order-steps.tpl"}

{if $nbProducts <= 0}
    <p class="warning">{l s='Your shopping cart is empty.' mod='payabbhi'}</p>
{else}
    <div class="box cheque-box">
      <h3 class="page-subheading">
        {l s='Payabbhi payment' mod='payabbhi'}
      </h3>
      <p class="cheque-indent">
        <strong class="dark">
          {l s='You have chosen to pay by payabbhi. Here is a short summary of your order:' mod='payabbhi'}
        </strong>
      </p>
      <p>
        - {l s='The total amount of your order is' mod='payabbhi'}
        <span id="amount" class="price">{displayPrice price=$total}</span> {if $use_taxes == 1} {l s='(tax incl.)' mod='payabbhi'} {/if}
      </p>
      <p>
        - {l s='Please confirm your order by clicking "I confirm my order".' mod='payabbhi'}
      </p>
    </div>

    <form name='checkoutform' id="checkout-form" action="{$return_url}" method="POST">
      <input type="hidden" name="merchant_order_id" value="{$cart_id}">
      <input type="hidden" name="order_id" id="order_id">
      <input type="hidden" name="payment_id" id="payment_id">
      <input type="hidden" name="payment_signature" id="payment_signature">
    </form>

    <p class="cart_navigation clearfix" id="cart_navigation">
      <a class="button-exclusive btn btn-default" href="{$link->getPageLink('order', true, NULL, " step=3 ")|escape:'html':'UTF-8'}">
        <i class="icon-chevron-left"></i>{l s='Other payment methods' mod='payabbhi'}
      </a>

      <button class="button btn btn-default button-medium" id="btn-checkout">
        <a onclick="payabbhiCheckout.open();" style="color: #ffffff;">
          <span>{l s='I confirm my order' mod='payabbhi'}<i class="icon-chevron-right right"></i></span>
        </a>
      </button>
    </p>

{/if}

<script>
  var checkoutArgs = {$checkout_args};
  checkoutArgs.handler = function(payment){
    document.getElementById('order_id').value = payment.order_id;
    document.getElementById('payment_id').value = payment.payment_id;
    document.getElementById('payment_signature').value = payment.payment_signature;
    document.getElementById('checkout-form').submit();
  };
  var payabbhiCheckout = new Payabbhi(checkoutArgs);
  payabbhiCheckout.open();
</script>

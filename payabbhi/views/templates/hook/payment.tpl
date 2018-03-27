<p class="payment_module">
    <a class="payabbhi" href="{$link->getModuleLink('payabbhi', 'payment', [], true)}" title="{l s='Pay by Payabbhi' mod='payabbhi'}">
        {l s=$payment_description mod='payabbhi'}
        {if $payment_description}
          <span>(via Payabbhi)</span>
        {/if}
    </a>
</p>

<style>
p.payment_module a.payabbhi {
    background: url("{$this_path}logo.png") 15px 15px no-repeat #fbfbfb;
    background-size: 64px;
}
p.payment_module a.payabbhi:after {
    display: block;
    content: "\f054";
    position: absolute;
    right: 15px;
    margin-top: -11px;
    top: 50%;
    font-family: "FontAwesome";
    font-size: 25px;
    height: 22px;
    width: 14px;
    color: #777;
}
</style>

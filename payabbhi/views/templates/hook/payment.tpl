<p class="payment_module">
    <a class="payabbhi" href="{$link->getModuleLink('payabbhi', 'payment', [], true)}" title="{l s='Pay by Payabbhi' mod='payabbhi'}">
        {l s='Pay with Card, NetBanking, Wallet' mod='payabbhi'}
        <span>(via Payabbhi)</span>
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

## Payabbhi Payments - Prestashop Integration

This extension is built on Payabbhi PHP Library to provide seamless integration of [Payabbhi Checkout ](https://payabbhi.com/docs/checkout) with PrestaShop 1.6


### Installation

Make sure you have signed up for your [Payabbhi Account](https://payabbhi.com/docs/account) and downloaded the [API keys](https://payabbhi.com/docs/account/#api-keys) from the [Portal](https://payabbhi.com/portal).

1. Unzip [payabbhi-prestashop-VERSION.zip](https://github.com/payabbhi/payabbhi-prestashop/releases).

2. Navigate to `PrestaShop Back Office` -> `Modules and Services` and click on `Add a new module`.

3. Browse for `payabbhi.zip` to add Payabbhi Payment Extension to PrestaShop.

4. On successful upload, `payabbhi` folder should get added to PrestaShop installation directory as follows:

```
PrestaShop/
	modules/
		payabbhi/
			config.xml
			controllers/
			index.php
			logo.png
			payabbhi-php/
			payabbhi.php
			VERSION
			views/
```

4. After successful upload, navigate to `Modules List` ->`Payments and Gateways` and install `Payabbhi Checkout` as per on-screen instructions. If you do not find Payabbhi on the list, please use the search option to find it.

5. Configure `Payabbhi` and Save the settings:
  - [Access ID](https://payabbhi.com/docs/account/#api-keys)
  - [Secret Key](https://payabbhi.com/docs/account/#api-keys)
  - [payment_auto_capture](https://payabbhi.com/docs/api/#create-an-order)
  - Description -  This text will be displayed alongside payabbhi logo on payments page.



[Payabbhi Checkout](https://payabbhi.com/docs/checkout) is now enabled in PrestaShop.

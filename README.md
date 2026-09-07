# Free Shipping Bar (`wg_freeshippingbar`)

A free PrestaShop module that shows customers how much more they need to spend to get free shipping.

**Version:** 1.0.0 · **PrestaShop:** 1.7.6 – 9.x · **License:** EUPL-1.2 · **Author:** [Webski Gość](https://webskigosc.com)

## What it does

The module reads the free shipping threshold from your shop (Shipping → Preferences → _Free shipping starts at_), converts it to the customer's currency and displays a bar with the amount left: _"€12.50 left for free shipping"_. Once the cart reaches the threshold it switches to _"You have free delivery!"_.

The bar updates live whenever the cart changes (add to cart, quantity change, remove) — no page reload needed. You choose where it appears, which theme it uses and, if you want, your own colours.

## Features

- Amount left to free shipping, calculated in the current currency
- Optional animated progress bar
- Live update on every cart change (`updateCart` event)
- Show or hide the bar when free shipping is reached
- Show or hide the bar when the cart is empty
- Calculate with tax included or excluded, optional tax label
- 10 selectable placements (product page, cart, cart modal, checkout, top banner…)
- 4 built-in themes: Basic, Classic, Minimal, Modern
- Custom colours (background, text, progress, progress background)
- Multilingual: EN, ES, FR, PL
- Optional automatic update check with a notice in the back office

## Installation

Modules → Module Manager → **Upload a module** → select the ZIP → **Install** → **Configure**.

Alternatively copy the `wg_freeshippingbar` folder into `modules/` and install it from the Module Manager.

Make sure the free shipping threshold is set: **Shipping → Preferences → Free shipping starts at** (price). If it is 0 the bar has nothing to count down to.

## Configuration

### Settings

| Option                 | Description                                                                             | Default                                            |
| ---------------------- | --------------------------------------------------------------------------------------- | -------------------------------------------------- |
| Progress Bar           | Display the progress bar under/over the text                                            | Yes                                                |
| Free Shipping Achieved | Show or hide the bar once free shipping is reached                                      | Show                                               |
| Empty Cart             | Show or hide the bar when the cart is empty                                             | Hide                                               |
| Tax Included           | Calculate the amount left with tax included or excluded (disabled when taxes are off)   | follows shop tax setting                           |
| Tax Label              | Append the shop's "tax incl./excl." label to the amount                                 | follows shop setting                               |
| Check updates          | Check for a new module version (once per hour) and show a notice in the module settings | Yes                                                |
| Assign Hooks           | Where the bar is displayed — at least one is required                                   | Product Additional Info, Checkout Subtotal Details |

### Placement (Assign Hooks)

| Label in back office        | Hook                              | Where it shows                     |
| --------------------------- | --------------------------------- | ---------------------------------- |
| Top Banner                  | `displayBanner`                   | Header banner area on every page   |
| Cart Modal Content          | `displayCartModalContent`         | "Added to cart" modal, content     |
| Cart Modal Footer           | `displayCartModalFooter`          | "Added to cart" modal, footer      |
| Cart Reassurance            | `displayReassurance`              | Cart page only (reassurance block) |
| Checkout Subtotal Details   | `displayCheckoutSubtotalDetails`  | Cart summary, under subtotals      |
| Cross Selling Shopping Cart | `displayCrossSellingShoppingCart` | Cart page, cross-selling area      |
| Product Additional Info     | `displayProductAdditionalInfo`    | Product page, under add-to-cart    |
| Product Footer              | `displayFooterProduct`            | Product page, footer               |
| Shopping Cart               | `displayShoppingCart`             | Cart page, top                     |
| Shopping Cart Footer        | `displayShoppingCartFooter`       | Cart page, bottom                  |

### Design

| Option                                             | Description                                                                             |
| -------------------------------------------------- | --------------------------------------------------------------------------------------- |
| Theme                                              | Basic, Classic (default), Minimal or Modern — each is a single CSS file in `views/css/` |
| Custom Colors                                      | Enable to override the theme colours                                                    |
| Background / Text / Progress / Progress Background | Colour pickers, visible when Custom Colors is on                                        |

Custom colours are saved on **Save** as CSS variables into `views/css/custom.css`, which is loaded after the theme file. The file must be writable by the web server; otherwise an error is shown in the back office.

## Requirements

- PrestaShop 1.7.6.0 – 9.2.x
- PHP 7.1+ (8.x recommended)
- Free shipping threshold configured (Shipping → Preferences)
- `modules/wg_freeshippingbar/views/css/custom.css` writable if you use Custom Colors
- PHP cURL extension if you keep the update check enabled

## Screenshots

<!-- ![Back office – settings](docs/screenshot-settings.png) -->
<!-- ![Back office – design](docs/screenshot-design.png) -->
<!-- ![Front office – product page](docs/screenshot-product.png) -->
<!-- ![Front office – cart](docs/screenshot-cart.png) -->

## Compatibility

- ✅ PrestaShop 1.7.6, 8.x and 9.x
- ✅ EN, ES, FR, PL
- ✅ Free and open source (EUPL-1.2 license)

## Download

Latest release: **1.0.0**

- [Module page on webskigosc.com](https://webskigosc.com/prestashop/freeshippingbar)
- [Download from GitHub](https://github.com/YOUR_GITHUB_USER/wg_freeshippingbar) <!-- TODO: replace with the real repository URL -->

## Feedback

Bug reports and ideas are welcome. Write to [marcin@webskigosc.com](mailto:marcin@webskigosc.com) or [ask@webskigosc.com](mailto:ask@webskigosc.com).

Like the module? [Buy me a coffee](https://tip.webskigosc.com).

## License

Licensed under the [European Union Public Licence v1.2](https://joinup.ec.europa.eu/software/page/eupl) or later. See [LICENSE.txt](LICENSE.txt).

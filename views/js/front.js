/**
 * Copyright 2021-2025 Webski Gość • Marcin Lewandowski • WebskiGosc.com
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the EUPL-1.2 or later.
 * You may not use this work except in compliance with the Licence.
 *
 * You may obtain a copy of the Licence at:
 * https://joinup.ec.europa.eu/software/page/eupl
 * It is also bundled with this package in the file LICENSE.txt
 *
 * Unless required by applicable law or agreed to in writing,
 * software distributed under the Licence is distributed on an AS IS basis,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the Licence for the specific language governing permissions
 * and limitations under the Licence.
 *
 *  @author    Webski Gość • Marcin Lewandowski <marcin@webskigosc.com>
 *  @copyright 2021-2026 Webski Gość Marcin Lewandowski
 *  @license   https://joinup.ec.europa.eu/software/page/eupl
 */
$(document).ready(function () {
  if (typeof prestashop !== 'undefined') {
    try {
      prestashop.on('updateCart', function (event) {
        if (event?.resp?.cart) {
          updateFreeShippingBar(event.resp.cart);
        }
      });
    } catch (error) {
      console.error('Error on updating Free Shipping Bar: ', error);
    }
  }

  function updateFreeShippingBar(cart) {
    try {
      const freeShippingPrice = parseFloat(wg_freeshipping_price),
        displayCurrency = wg_freeshipping_currency,
        displayAchieved = wg_freeshipping_achieved,
        displayCartEmpty = wg_freeshipping_cart_empty;

      if (!cart?.subtotals?.products || !cart.subtotals.shipping) {
        throw new Error('Invalid cart structure');
      }

      const productsTotal = parseFloat(cart.subtotals.products.amount),
        shippingCost = parseFloat(cart.subtotals.shipping.amount);

      if (isNaN(productsTotal) || isNaN(shippingCost)) {
        throw new Error('Invalid amounts in cart subtotals');
      }

      const priceLeft = Math.max(0, freeShippingPrice - productsTotal).toFixed(2),
        amountLeftDisplay = displayCurrency.replace(/(\d+)[,.](\d{2})/, priceLeft);
      let percentage = 0;

      if (productsTotal == 0 && !displayCartEmpty) {
        $('.wg-fsb').hide(450, 'linear');
      }

      if (priceLeft > 0 || shippingCost > 0) {
        percentage = productsTotal > 0 ? ((productsTotal / freeShippingPrice) * 100).toFixed(2) : 0;
        if (!displayAchieved) $('.wg-fsb').show(250, 'linear');
        $('.wg-fsb__countdown').removeClass('wg-fsb__hidden');
        $('.wg-fsb__success').addClass('wg-fsb__hidden');
      }

      if (priceLeft <= 0 && productsTotal > 0) {
        percentage = 100;
        !displayAchieved && $('.wg-fsb').hide(450, 'linear');
        $('.wg-fsb__countdown').addClass('wg-fsb__hidden');
        $('.wg-fsb__success').removeClass('wg-fsb__hidden');
      } else {
        $('.wg-fsb__countdown').removeClass('wg-fsb__hidden');
        $('.wg-fsb__success').addClass('wg-fsb__hidden');
      }

      if ($('.wg-fsb__progress-bar').length > 0) {
        $('.wg-fsb__progress-bar').css('width', `${percentage}%`);
        $('.wg-fsb__progress-bar').attr('aria-valuenow', percentage);
      }

      $('.wg-fsb__amount-left').text(amountLeftDisplay);
    } catch (error) {
      console.error('Error on updating Free Shipping Bar: ', error);
    }
  }
});

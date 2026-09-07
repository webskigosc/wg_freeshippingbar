{**
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
 *}

{assign var="tax_label" value="" nocache}
{if $free_shipping_bar.display_tax_label}
    {assign var="tax_label" value=" `$cart.labels.tax_short`" nocache}
{/if}
<div {if !empty($free_shipping_bar.section_id)}id="{$free_shipping_bar.section_id}" {/if}
    class="wg-fsb card card-container"
    {if $free_shipping_bar.shipping_cost == 0 && !$free_shipping_bar.display_achieved || $free_shipping_bar.products_total == 0 && !$free_shipping_bar.display_cart_empty}
    style="display:none;" {/if}>
    <div class="wg-fsb__block card-block">
        {if $free_shipping_bar.display_progressbar}
            <div class="wg-fsb__progress progress">
                <div class="wg-fsb__progress-bar progress-bar bg-success" role="progressbar"
                    style="width: {$free_shipping_bar.progress|floatval}%;"
                    aria-valuenow="{$free_shipping_bar.progress|floatval}" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
        {/if}
        <div class="wg-fsb__text">
            <p class="wg-fsb__countdown{if $free_shipping_bar.progress >= 100} wg-fsb__hidden{/if}">
                {l s='%tag_open%%amount_left%%tag_close%%tax_label% left for free shipping' d='Modules.Wgfreeshippingbar.Shop' sprintf=['%amount_left%' => $free_shipping_bar.amount_left|escape, '%tag_open%' => '<span class="wg-fsb__amount-left">', '%tag_close%' => '</span>', '%tax_label%' => $tax_label]}
            </p>
            <p class="wg-fsb__success{if $free_shipping_bar.progress < 100} wg-fsb__hidden{/if}">
                {l s='You have free delivery!' d='Modules.Wgfreeshippingbar.Shop'}
            </p>
        </div>
    </div>
</div>
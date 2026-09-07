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

<div id="wg-fsb__update" class="bootstrap">
    <div class="alert alert-info conf confirm module_confirmation">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <a class="text-info" href="{$updateUrl}" target="_blank"
            title="{l s='Download new version of Free Shipping Bar module' d='Modules.Wgfreeshippingbar.Admin'}">
            {l s='A new version of Free Shipping Bar is available: %latest% (you have %current%).' sprintf=['%latest%' => $latestVersion, '%current%' => $currentVersion] d='Modules.Wgfreeshippingbar.Admin'}
        </a> {l s='Please update to get the latest features and fixes.' d='Modules.Wgfreeshippingbar.Admin'}
    </div>
</div>
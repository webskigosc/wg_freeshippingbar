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
  $('input[name="WG_FSB_CUSTOM"]').change(function () {
    if ($(this).is(':checked') && $(this).attr('id') === 'WG_FSB_CUSTOM_on') {
      $('.wg-fsb__custom-color').removeClass('hidden');
    } else {
      $('.wg-fsb__custom-color').addClass('hidden');
    }
  });
});

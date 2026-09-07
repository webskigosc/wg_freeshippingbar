<?php

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

use PrestaShop\PrestaShop\Core\Module\WidgetInterface;

if (!defined('_PS_VERSION_')) {
    exit;
}

class WG_FreeShippingBar extends Module implements WidgetInterface
{
    /** @var string */
    public $name;
    /** @var string */
    public $tab;
    /** @var string */
    public $author;
    /** @var string */
    public $author_uri = '';
    /** @var string */
    public $version;
    /** @var string key provided by addons.prestashop.com */
    public $module_key = '';
    /** @var int */
    public $need_instance;
    /** @var array */
    public $ps_versions_compliancy = [];
    /** @var array modules needed for install */
    public $dependencies = [];
    /** @var bool */
    public $bootstrap;
    /** @var string */
    public $displayName;
    /** @var string */
    public $description;
    /** @var string */
    public $confirmUninstall;
    /** @var string */
    public $message = '';
    /** @var string */
    public $logo = '';
    /** @var string Fill it if the module is installed but not yet set up */
    public $warning;
    /** @var string Module web path (eg. '/shop/modules/modulename/') */
    protected $_path = null;
    /** @var string Module local path (eg. '/home/prestashop/modules/modulename/') */
    protected $local_path = null;
    /** @var array Array filled with module errors */
    protected $_errors = [];
    /** @var array Array filled with module success */
    protected $_confirmations = [];
    /** @var string */
    public $author_email;
    /** @var string */
    public $cfg_prefix;
    /** @var array */
    public $cfg_keys = [];
    /** @var string */
    private $templateFile;
    /** @var string */
    protected $_trans_domain;

    public function __construct()
    {
        $this->name = 'wg_freeshippingbar';
        $this->tab = 'shipping_logistics';
        $this->author = 'Webski Gość';
        $this->author_email = 'marcin@webskigosc.com';
        $this->author_uri = 'https://webskigosc.com';
        $this->version = '1.0.0';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
          'min' => '1.7.6.0',
          'max' => '9.2.99',
        ];

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->trans('Free Shipping Bar', [], 'Modules.Wgfreeshippingbar.Admin');
        $this->description = $this->trans('Display the amount of free shipping left on the front end of your store.', [], 'Modules.Wgfreeshippingbar.Admin');
        $this->confirmUninstall = $this->trans('Are you sure you want to uninstall %modulename% module?', ['%modulename%' => $this->displayName], 'Modules.Wgfreeshippingbar.Admin');

        $this->_trans_domain = 'Modules.Wgfreeshippingbar.';
        $this->templateFile = 'module:' . $this->name . '/views/templates/hook/' . $this->name . '.tpl';

        if (!$this->_path) {
            $this->_path = __PS_BASE_URI__ . 'modules/' . $this->name . '/';
        }
        if (!$this->local_path) {
            $this->local_path = __DIR__ . '/';
        }

        $this->cfg_prefix = 'WG_FSB_';

        $this->cfg_keys = [
          $this->cfg_prefix . 'PROGRESS_BAR' => true,
          $this->cfg_prefix . 'ACHIEVED' => true,
          $this->cfg_prefix . 'CART_EMPTY' => false,
          $this->cfg_prefix . 'TAX_INCLUDED' => Configuration::get('PS_TAX') ? true : false,
          $this->cfg_prefix . 'TAX_LABEL' => Configuration::get('PS_TAX_DISPLAY') ? true : false,
          $this->cfg_prefix . 'HOOKS' => pSQL("displayProductAdditionalInfo;displayCheckoutSubtotalDetails;"),
          $this->cfg_prefix . 'CHECK_UPDATES' => true,
          $this->cfg_prefix . 'CHECK_UPDATES_TIMESTAMP' => time(),
          $this->cfg_prefix . 'NEW_VERSION' => false,
          $this->cfg_prefix . 'THEME' => 'classic',
          $this->cfg_prefix . 'CUSTOM' => false,
          $this->cfg_prefix . 'COLOR_BG' => '#FFF',
          $this->cfg_prefix . 'COLOR_TEXT' => '#222',
          $this->cfg_prefix . 'COLOR_PROGRESS' => '#5B3',
          $this->cfg_prefix . 'COLOR_PROGRESS_BG' => '#EEE',
        ];
    }

    public function install(): bool
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        if (version_compare(phpversion(), '5.6.0', '<')) {
            $this->_errors[] = $this->trans('Your PHP version is too old, please upgrade to a newer version. Your version is %s, library requires %s', [phpversion(), '5.6.0'], 'Modules.Wgfreeshippingbar.Admin');

            return false;
        }

        foreach ($this->cfg_keys as $key => $value) {
            Configuration::updateValue($key, $value);
        }

        $this->_clearCache('*');

        if (
            !parent::install()
            || !$this->registerHook('actionAdminControllerSetMedia')
            || !$this->registerHook('actionFrontControllerSetMedia')
            || !$this->registerHook('displayBanner')
            || !$this->registerHook('displayShoppingCart')
            || !$this->registerHook('displayShoppingCartFooter')
            || !$this->registerHook('displayReassurance')
            || !$this->registerHook('displayCrossSellingShoppingCart')
            || !$this->registerHook('displayCheckoutSubtotalDetails')
            || !$this->registerHook('displayCartModalContent')
            || !$this->registerHook('displayCartModalFooter')
            || !$this->registerHook('displayProductAdditionalInfo')
            || !$this->registerHook('displayFooterProduct')
        ) {
            $this->_errors[] = $this->trans('There was an error during the installation. Please send message with information to the email address: %s', [$this->author_email], 'Modules.Wgfreeshippingbar.Admin');
            return false;
        }
        return true;
    }

    public function uninstall(): bool
    {
        $this->_clearCache('*');

        if (
            !parent::uninstall()
            || !$this->unregisterHook('actionAdminControllerSetMedia')
            || !$this->unregisterHook('actionFrontControllerSetMedia')
            || !$this->unregisterHook('displayBanner')
            || !$this->unregisterHook('displayShoppingCart')
            || !$this->unregisterHook('displayShoppingCartFooter')
            || !$this->unregisterHook('displayReassurance')
            || !$this->unregisterHook('displayCrossSellingShoppingCart')
            || !$this->unregisterHook('displayCheckoutSubtotalDetails')
            || !$this->unregisterHook('displayCartModalContent')
            || !$this->unregisterHook('displayCartModalFooter')
            || !$this->unregisterHook('displayProductAdditionalInfo')
            || !$this->unregisterHook('displayFooterProduct')
            || !$this->removeConfigs()
        ) {
            $this->_errors[] = $this->trans('There was an error during the uninstallation. Please send message with information to the email address: %s', [$this->author_email], 'Modules.Wgfreeshippingbar.Admin');
            return false;
        }
        return true;
    }

    public function isUsingNewTranslationSystem()
    {
        return true;
    }

    protected function _clearCache($template, $cacheId = null, $compileId = null)
    {
        Media::clearCache();
        parent::_clearCache($this->templateFile, $cacheId);
    }

    public function getContent(): string
    {
        $html = '';
        if (((bool)Tools::isSubmit('submitModuleWgFreeShippingBar')) === true) {
            $this->postProcess();
            $this->_clearCache('*');

            if ((bool)Configuration::get($this->cfg_prefix . 'CUSTOM') === true) {
                $this->saveCustomColorsInFile();
            }

            if (count($this->_errors) > 0) {
                $html .= $this->displayError(implode('<br />', $this->_errors));
            } else {
                $html .= $this->displayConfirmation($this->trans('The settings have been successfully updated.', [], 'Admin.Notifications.Success'));
            }
        }

        if (
            (
                Configuration::get($this->cfg_prefix . 'CHECK_UPDATES', false)
                && Configuration::get($this->cfg_prefix . 'CHECK_UPDATES_TIMESTAMP', 0) < (time() - 3600)
            ) || Configuration::get($this->cfg_prefix . 'NEW_VERSION', false)
        ) {
            $checkUpdateResult = $this->checkForUpdates();
            Configuration::updateValue($this->cfg_prefix . 'CHECK_UPDATES_TIMESTAMP', time());

            if (isset($checkUpdateResult['update_available']) && $checkUpdateResult['update_available']) {
                Configuration::updateValue($this->cfg_prefix . 'NEW_VERSION', true);
                $this->context->smarty->assign('currentVersion', $this->version);
                $this->context->smarty->assign('latestVersion', $checkUpdateResult['latest_version'] ?? '');
                $this->context->smarty->assign('updateUrl', $checkUpdateResult['update_url'] ?? '');
                $html .= $this->context->smarty->fetch($this->local_path . 'views/templates/admin/alert_update.tpl');
            } else {
                Configuration::updateValue($this->cfg_prefix . 'NEW_VERSION', false);
            }
        }

        $html .= $this->context->smarty->fetch($this->local_path . 'views/templates/admin/header.tpl');
        $html .= $this->renderForm();
        $html .= $this->context->smarty->fetch($this->local_path . 'views/templates/admin/footer.tpl');

        return $html;
    }

    protected function postProcess(): void
    {
        $form_values = $this->getConfigFormValues(false);

        foreach (array_keys($form_values) as $key) {
            if ($key === $this->cfg_prefix . 'HOOKS') {
                $postHooks = [];
                foreach ($_POST as $postKey => $postValue) {
                    if (strpos($postKey, $this->cfg_prefix . 'HOOK') === 0 && $postValue === 'on') {
                        $postHooks[] = (string) str_replace($this->cfg_prefix . 'HOOK_', '', $postKey);
                    }
                }
                $selectedHooks = !empty($postHooks) ? implode(";", $postHooks) : $this->_errors[] = $this->trans('Please select at least one hook.', [], 'Modules.Wgfreeshippingbar.Admin');

                Configuration::updateValue($key, (string) pSQL($selectedHooks));
            } else {
                Configuration::updateValue($key, Tools::getValue($key));
            }
        }
    }

    public function renderForm(): string
    {
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
        $helper->identifier = $this->identifier;

        $helper->submit_action = 'submitModuleWgFreeShippingBar';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
          . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = [
          'uri' => $this->getPathUri(),
          'fields_value' => $this->getConfigFormValues(),
          'languages' => $this->context->controller->getLanguages(),
          'id_language' => $this->context->language->id,
        ];

        return $helper->generateForm([$this->getConfigForm(), $this->getDesigneFrom()]);
    }


    protected function getConfigFormValues($hooksSeparation = true): array
    {
        $form_values = [];
        foreach ($this->cfg_keys as $key => $value) {
            if ($hooksSeparation && $key === $this->cfg_prefix . 'HOOKS' && !empty($value)) {
                $hooks = explode(';', rtrim(Configuration::get($key, $value), ';'));

                if (!empty($hooks) && is_array($hooks)) {
                    foreach ($hooks as $hook) {
                        $form_values[$this->cfg_prefix . 'HOOK_' . $hook] = 'on';
                    }
                }
            }

            $form_values[$key] = Configuration::get($key, $value);
        }

        return $form_values;
    }


    protected function getConfigForm(): array
    {
        $taxEnabled = Configuration::get('PS_TAX');

        return [
          'form' => [
            'legend' => [
              'title' => $this->trans('Settings', [], 'Admin.Global'),
              'icon' => 'icon-cogs',
            ],
            'input' => [
              [
                'type' => 'switch',
                'label' => $this->trans('Progress Bar', [], 'Modules.Wgfreeshippingbar.Admin'),
                'name' => $this->cfg_prefix . 'PROGRESS_BAR',
                'is_bool' => true,
                'desc' => $this->trans('Display the progress bar.', [], 'Modules.Wgfreeshippingbar.Admin'),
                'default' => 1,
                'values' => [
                  [
                    'id' => 'active_on',
                    'value' => true,
                    'label' => $this->trans('Yes', [], 'Admin.Global'),
                  ],
                  [
                    'id' => 'active_off',
                    'value' => false,
                    'label' => $this->trans('No', [], 'Admin.Global'),
                  ],
                ],
                'validate' => 'isUnsignedInt',
              ],
              [
                'type' => 'switch',
                'label' => $this->trans('Free Shipping Achieved', [], 'Modules.Wgfreeshippingbar.Admin'),
                'name' => $this->cfg_prefix . 'ACHIEVED',
                'is_bool' => true,
                'desc' => $this->trans('Hide or leave the bar visible when free shipping is achieved.', [], 'Modules.Wgfreeshippingbar.Admin'),
                'default' => 0,
                'values' => [
                  [
                    'id' => 'active_on',
                    'value' => true,
                    'label' => $this->trans('Show', [], 'Admin.Actions'),
                  ],
                  [
                    'id' => 'active_off',
                    'value' => false,
                    'label' => $this->trans('Hide', [], 'Admin.Actions'),
                  ],
                ],
                'validate' => 'isUnsignedInt',
              ],
              [
                'type' => 'switch',
                'label' => $this->trans('Empty Cart', [], 'Modules.Wgfreeshippingbar.Admin'),
                'name' => $this->cfg_prefix . 'CART_EMPTY',
                'is_bool' => true,
                'desc' => $this->trans('Hide or leave the bar visible when cart is empty.', [], 'Modules.Wgfreeshippingbar.Admin'),
                'default' => 0,
                'values' => [
                  [
                    'id' => 'active_on',
                    'value' => true,
                    'label' => $this->trans('Show', [], 'Admin.Actions'),
                  ],
                  [
                    'id' => 'active_off',
                    'value' => false,
                    'label' => $this->trans('Hide', [], 'Admin.Actions'),
                  ],
                ],
                'validate' => 'isUnsignedInt',
              ],
              [
                'type' => 'switch',
                'label' => $this->trans('Tax Included', [], 'Modules.Wgfreeshippingbar.Admin'),
                'name' => $this->cfg_prefix . 'TAX_INCLUDED',
                'is_bool' => true,
                'desc' => $this->trans('Calculate amount left to free shipping with tax included or excluded.', [], 'Modules.Wgfreeshippingbar.Admin'),
                'disabled' => !$taxEnabled,
                'default' => 1,
                'values' => [
                  [
                    'id' => 'active_on',
                    'value' => true,
                    'label' => $this->trans('Yes', [], 'Admin.Global'),
                  ],
                  [
                    'id' => 'active_off',
                    'value' => false,
                    'label' => $this->trans('No', [], 'Admin.Global'),
                  ],
                ],
                'validate' => 'isUnsignedInt',
              ],
              [
                'type' => 'switch',
                'label' => $this->trans('Tax Label', [], 'Modules.Wgfreeshippingbar.Admin'),
                'name' => $this->cfg_prefix . 'TAX_LABEL',
                'is_bool' => true,
                'desc' => $this->trans('Display tax included/excluded label.', [], 'Modules.Wgfreeshippingbar.Admin'),
                'default' => 0,
                'values' => [
                  [
                    'id' => 'active_on',
                    'value' => true,
                    'label' => $this->trans('Yes', [], 'Admin.Global'),
                  ],
                  [
                    'id' => 'active_off',
                    'value' => false,
                    'label' => $this->trans('No', [], 'Admin.Global'),
                  ],
                ],
                'validate' => 'isUnsignedInt',
              ],
              [
                'type' => 'switch',
                'label' => $this->trans('Check updates', [], 'Modules.Wgfreeshippingbar.Admin'),
                'name' => $this->cfg_prefix . 'CHECK_UPDATES',
                'is_bool' => true,
                'desc' => $this->trans('Check for module updates automatically.', [], 'Modules.Wgfreeshippingbar.Admin'),
                'default' => 0,
                'values' => [
                  [
                    'id' => 'active_on',
                    'value' => true,
                    'label' => $this->trans('Yes', [], 'Admin.Global'),
                  ],
                  [
                    'id' => 'active_off',
                    'value' => false,
                    'label' => $this->trans('No', [], 'Admin.Global'),
                  ],
                ],
                'validate' => 'isUnsignedInt',
              ],
              [
                'type' => 'checkbox',
                'label' => $this->trans('Assign Hooks', [], 'Modules.Wgfreeshippingbar.Admin'),
                'name' => $this->cfg_prefix . 'HOOK',
                'desc' => $this->trans('Choice where to display the free shipping bar.', [], 'Modules.Wgfreeshippingbar.Admin'),
                'default' => 'displayCheckoutSubtotalDetails,displayProductAdditionalInfo',
                'form_group_class' => 'wg-fsb__hooks',
                'values' => [
                  'query' => [
                    [
                      'id' => 'displayBanner',
                      'label' => $this->trans('Top Banner', [], 'Modules.Wgfreeshippingbar.Admin'),
                    ],
                    [
                      'id' => 'displayCartModalContent',
                      'label' => $this->trans('Cart Modal Content', [], 'Modules.Wgfreeshippingbar.Admin'),
                    ],
                    [
                      'id' => 'displayCartModalFooter',
                      'label' => $this->trans('Cart Modal Footer', [], 'Modules.Wgfreeshippingbar.Admin'),
                    ],
                    [
                      'id' => 'displayReassurance',
                      'label' => $this->trans('Cart Reassurance', [], 'Modules.Wgfreeshippingbar.Admin'),
                    ],
                    [
                      'id' => 'displayCheckoutSubtotalDetails',
                      'label' => $this->trans('Checkout Subtotal Details', [], 'Modules.Wgfreeshippingbar.Admin'),
                    ],
                    [
                      'id' => 'displayCrossSellingShoppingCart',
                      'label' => $this->trans('Cross Selling Shopping Cart', [], 'Modules.Wgfreeshippingbar.Admin'),
                    ],
                    [
                      'id' => 'displayProductAdditionalInfo',
                      'label' => $this->trans('Product Additional Info', [], 'Modules.Wgfreeshippingbar.Admin'),
                    ],
                    [
                      'id' => 'displayFooterProduct',
                      'label' => $this->trans('Product Footer', [], 'Modules.Wgfreeshippingbar.Admin'),
                    ],
                    [
                      'id' => 'displayShoppingCart',
                      'label' => $this->trans('Shopping Cart', [], 'Modules.Wgfreeshippingbar.Admin'),
                    ],
                    [
                      'id' => 'displayShoppingCartFooter',
                      'label' => $this->trans('Shopping Cart Footer', [], 'Modules.Wgfreeshippingbar.Admin'),
                    ],
                  ],
                  'id' => 'id',
                  'name' => 'label',
                ],
                'validate' => 'isCleanHtml',
              ]
            ],
            'submit' => [
              'title' => $this->trans('Save', [], 'Admin.Actions'),
            ],
          ],
        ];
    }

    protected function getDesigneFrom(): array
    {
        $inputColorClass = Configuration::get($this->cfg_prefix . 'CUSTOM') ? 'wg-fsb__custom-color' : 'wg-fsb__custom-color hidden';

        return [
          'form' => [
            'legend' => [
              'title' => $this->trans('Design', [], 'Admin.Navigation.Menu'),
              'icon' => 'icon-paint-brush',
            ],
            'input' => [
              [
                'type' => 'select',
                'label' => $this->trans('Theme', [], 'Admin.Design.Feature'),
                'name' => $this->cfg_prefix . 'THEME',
                'desc' => $this->trans('Select theme that you like most.', [], 'Modules.Wgfreeshippingbar.Admin'),
                'class' => 'fixed-width-xxl',
                'options' => [
                  'query' => [
                    [
                      'id_option' => 'basic',
                      'name' => $this->trans('Basic', [], 'Modules.Wgfreeshippingbar.Admin'),
                    ],
                    [
                      'id_option' => 'classic',
                      'name' => $this->trans('Classic', [], 'Modules.Wgfreeshippingbar.Admin'),
                    ],
                    [
                      'id_option' => 'minimal',
                      'name' => $this->trans('Minimal', [], 'Modules.Wgfreeshippingbar.Admin'),
                    ],
                    [
                      'id_option' => 'modern',
                      'name' => $this->trans('Modern', [], 'Modules.Wgfreeshippingbar.Admin'),
                    ],
                  ],
                  'id' => 'id_option',
                  'name' => 'name',
                ],
                'validate' => 'isCleanHtml',
              ],
              [
                'type' => 'switch',
                'label' => $this->trans('Custom Colors', [], 'Modules.Wgfreeshippingbar.Admin'),
                'name' => $this->cfg_prefix . 'CUSTOM',
                'is_bool' => true,
                'desc' => $this->trans('Maybe you want to use custom colors?', [], 'Modules.Wgfreeshippingbar.Admin'),
                'id' => 'wg-fsb__custom-colors',
                'default' => 0,
                'values' => [
                  [
                    'id' => 'active_on',
                    'value' => true,
                    'label' => $this->trans('Yes', [], 'Admin.Global'),
                  ],
                  [
                    'id' => 'active_off',
                    'value' => false,
                    'label' => $this->trans('No', [], 'Admin.Global'),
                  ],
                ],
                'validate' => 'isUnsignedInt',
              ],
              [
                'type' => 'color',
                'label' => $this->trans('Background', [], 'Modules.Wgfreeshippingbar.Admin'),
                'name' => $this->cfg_prefix . 'COLOR_BG',
                'form_group_class' => $inputColorClass,
                'validate' => 'isColor',
                'required' => false,
                'default' => '#FFF',
              ],
              [
                'type' => 'color',
                'label' => $this->trans('Text', [], 'Admin.Global'),
                'name' => $this->cfg_prefix . 'COLOR_TEXT',
                'form_group_class' => $inputColorClass,
                'validate' => 'isColor',
                'required' => false,
                'default' => '#222',
              ],
              [
                'type' => 'color',
                'label' => $this->trans('Progress', [], 'Modules.Wgfreeshippingbar.Admin'),
                'name' => $this->cfg_prefix . 'COLOR_PROGRESS',
                'form_group_class' => $inputColorClass,
                'validate' => 'isColor',
                'required' => false,
                'default' => '#5B3',
              ],
              [
                'type' => 'color',
                'label' => $this->trans('Progress Background', [], 'Modules.Wgfreeshippingbar.Admin'),
                'name' => $this->cfg_prefix . 'COLOR_PROGRESS_BG',
                'form_group_class' => $inputColorClass,
                'validate' => 'isColor',
                'required' => false,
                'default' => '#EEE',
              ],
            ],
            'submit' => [
              'title' => $this->trans('Save', [], 'Admin.Actions'),
            ],
          ],
        ];
    }

    public function renderWidget($hookName = null, array $configuration = [])
    {
        $assignedHooks = $this->getAssignedHooks();

        if (
            !in_array($hookName, $assignedHooks, true)
            || (
                $hookName === 'displayReassurance'
                && $this->context->controller->getPageName() !== 'cart'
            )
        ) {
            return;
        }

        $widgetVars = $this->getWidgetVariables($hookName, $configuration);
        $this->smarty->assign(['free_shipping_bar' => $widgetVars]);

        return $this->fetch($this->templateFile);
    }

    public function getWidgetVariables($hookName = null, array $configuration = []): array
    {
        return array_merge($this->getCostVariables($hookName), $this->getConfigDisplayVariables());
    }

    protected function getAssignedHooks(): array
    {
        return array_filter(explode(';', Configuration::get($this->cfg_prefix . 'HOOKS')));
    }

    protected function addJsDefList(): void
    {
        $cost = $this->getCostVariables();

        Media::addJsDef([
          'wg_freeshipping_price' => (float) $cost['free_price'],
          'wg_freeshipping_currency' => (string) $cost['currency'],
          'wg_freeshipping_achieved' => (int) Configuration::get($this->cfg_prefix . 'ACHIEVED', 0),
          'wg_freeshipping_cart_empty' => (int) Configuration::get($this->cfg_prefix . 'CART_EMPTY', 0),
        ]);
    }

    protected function getCostVariables($hookName = null): array
    {
        if (!isset($this->context)) {
            return [];
        }

        $cart = $this->context->cart;
        $currency = $this->context->currency;
        $freeShippingPrice = Configuration::get('PS_SHIPPING_FREE_PRICE') * $currency->conversion_rate;
        $cartShippingCost = $cart->getTotalShippingCost();
        $deliveryOption = $cart->getDeliveryOption();
        $taxIncluded = Configuration::get($this->cfg_prefix . 'TAX_INCLUDED') ? true : false;
        $cartProductsTotal = $cart->getOrderTotal($taxIncluded, Cart::ONLY_PRODUCTS);
        $freeShippingCostProgress = 0;

        if ($cartProductsTotal > 0 && $freeShippingPrice > 0) {
            $freeShippingCostProgress = min(100, ($cartProductsTotal / $freeShippingPrice) * 100);
        }

        if (
            (int) $freeShippingPrice === 0
            || (
                $cartProductsTotal > 0
                && $cartShippingCost === 0
                && !empty($deliveryOption)
            )
        ) {
            $freeShippingCostProgress = 100;
        }

        $amountLeftDisplay = $this->context->getCurrentLocale()->formatPrice($freeShippingPrice - $cartProductsTotal, $currency->iso_code);
        $displayCurrency = $this->context->getCurrentLocale()->formatPrice(0, $currency->iso_code);
        $displayIdHookName = !is_null($hookName) ? $this->transformHookName($hookName) : false;

        return [
          'amount_left' => (string) $amountLeftDisplay,
          'products_total' => (float) $cartProductsTotal,
          'progress' => (float) number_format($freeShippingCostProgress, 2),
          'shipping_cost' => (float) $cartShippingCost,
          'free_price' => (float) $freeShippingPrice,
          'currency' => (string) $displayCurrency,
          'section_id' => (string) $displayIdHookName,
        ];
    }

    protected function getConfigDisplayVariables(): array
    {
        $cfg = $this->getConfigFormValues(false);

        return [
          'display_progressbar' => (int) $cfg[$this->cfg_prefix . 'PROGRESS_BAR'],
          'display_achieved' => (int) $cfg[$this->cfg_prefix . 'ACHIEVED'],
          'display_cart_empty' => (int) $cfg[$this->cfg_prefix . 'CART_EMPTY'],
          'display_tax_label' => (int) $cfg[$this->cfg_prefix . 'TAX_LABEL'],
        ];
    }

    public function transformHookName($hookName): string
    {
        if (strpos($hookName, 'display') === 0) {
            $hookName = substr($hookName, strlen('display'));
        }

        if (!empty($hookName)) {
            $transformed = preg_replace('/([A-Z])/', '-$1', $hookName);
            $result = strtolower('wg-fsb__' . ltrim($transformed, '-'));
        } else {
            $result = '';
        }

        return $result;
    }

    public function removeConfigs(): bool
    {
        foreach (array_keys($this->cfg_keys) as $key) {
            Configuration::deleteByName($key);
        }

        return true;
    }

    public function hookActionFrontControllerSetMedia(array $params): void
    {
        $assignedHooks = $this->getAssignedHooks();

        if (empty($assignedHooks)) {
            return;
        }

        $this->addJsDefList();

        $this->context->controller->registerStylesheet(
            'module-' . $this->name . '-css',
            $this->getThemePath(),
            [
                'media' => 'all',
                'priority' => 100,
                'inline' => false,
            ]
        );

        if ((bool)Configuration::get($this->cfg_prefix . 'CUSTOM') === true) {
            $this->context->controller->registerStylesheet(
                'module-' . $this->name . 'custom-css',
                $this->_path . 'views/css/custom.css',
                [
                    'media' => 'all',
                    'priority' => 110,
                ]
            );
        }

        $this->context->controller->registerJavascript(
            'module-' . $this->name . '-js',
            $this->_path . 'views/js/front.js',
            [
                'position' => 'bottom',
                'priority' => 100,
                'attributes' => 'defer',
                'server' => 'local',
            ]
        );
    }

    public function hookActionAdminControllerSetMedia(array $params): void
    {
        if ($this->context->controller->controller_name !== 'AdminModules') {
            return;
        }

        $configure = Tools::getValue('configure');

        if ($configure !== $this->name) {
            return;
        }

        $this->context->controller->addCSS($this->_path . 'views/css/admin.css', 'all');
        $this->context->controller->addJs($this->_path . 'views/js/admin.js', true);
    }

    protected function checkForUpdates(): array
    {
        $apiUrl = 'https://api.webskigosc.com';
        $cacheKey = 'update_check_' . $this->name . '_' . (int) $this->context->shop->id;
        $cachedResult = Cache::getInstance()->get($cacheKey);

        if (!empty($cachedResult) && is_array($cachedResult)) {
            return $cachedResult;
        }

        $defaultResult = ['update_available' => false];
        $result = $defaultResult;

        if (!extension_loaded('curl')) {
            Cache::getInstance()->set($cacheKey, $defaultResult, 3600);
            return $defaultResult;
        }

        $curl = curl_init();

        try {
            $headers = [
                'Accept: application/json',
                'Content-Type: application/json',
                'User-Agent: ' . $this->name . '/' . $this->version,
                'MODULE: ' . $this->name,
            ];

            curl_setopt($curl, CURLOPT_URL, $apiUrl . '/token/');
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($curl, CURLOPT_TIMEOUT, 10);

            $response = curl_exec($curl);

            if ($response === false) {
                throw new Exception('cURL error: ' . curl_error($curl));
            }

            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            if ($httpCode < 200 || $httpCode >= 300) {
                throw new Exception('Token endpoint returned HTTP ' . $httpCode);
            }

            $jsonDecode = json_decode($response, true);

            if (empty($jsonDecode) || !isset($jsonDecode['token'])) {
                Cache::getInstance()->set($cacheKey, $defaultResult, 3600);
                return $defaultResult;
            }

            $headers[] = 'Authorization: Bearer ' . (string) $jsonDecode['token'];
            $body = json_encode([
                'name' => $this->name,
                'version' => $this->version,
                'url' => _PS_BASE_URL_,
                'shop' => $this->context->shop->name,
                'shop_version' => _PS_VERSION_,
                'lang' => $this->context->language->locale,
                'country' => $this->context->country->iso_code,
            ]);

            curl_setopt($curl, CURLOPT_URL, $apiUrl . '/prestashop/modules/');
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $body);

            $response = curl_exec($curl);

            if ($response === false) {
                throw new Exception('cURL error: ' . curl_error($curl));
            }

            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            if ($httpCode < 200 || $httpCode >= 300) {
                throw new Exception('Modules endpoint returned HTTP ' . $httpCode);
            }

            $jsonDecode = json_decode($response, true);

            if (!empty($jsonDecode) && isset($jsonDecode['update_available'])) {
                $result = $jsonDecode;
                Configuration::updateValue($this->cfg_prefix . 'CHECK_UPDATES_TIMESTAMP', time());
            }
        } catch (Exception $exception) {
            PrestaShopLogger::addLog('Error checking for updates: ' . $exception->getMessage(), 3);
            $result = $defaultResult;
        }

        Cache::getInstance()->set($cacheKey, $result, 3600);

        return $result;
    }

    public function getThemePath(): string
    {
        $selectedTheme =  !empty(Configuration::get($this->cfg_prefix . 'THEME')) ? (string)Configuration::get($this->cfg_prefix . 'THEME') : false;
        $themePath = $this->_path . 'views/css/';

        switch ($selectedTheme) {
            case 'classic':
                $themePath .= 'classic.css';
                break;
            case 'minimal':
                $themePath .= 'minimal.css';
                break;
            case 'modern':
                $themePath .= 'modern.css';
                break;
            default:
                $themePath .= 'front.css';
                break;
        }

        return $themePath;
    }

    protected function saveCustomColorsInFile(): void
    {
        $customColors = [
          'background-color' => (string) Configuration::get($this->cfg_prefix . 'COLOR_BG'),
          'text-color' => (string) Configuration::get($this->cfg_prefix . 'COLOR_TEXT'),
          'progress-bar-color' => (string) Configuration::get($this->cfg_prefix . 'COLOR_PROGRESS'),
          'progress-bar-background-color' => (string) Configuration::get($this->cfg_prefix . 'COLOR_PROGRESS_BG'),
        ];
        $cssContent = ':root{';

        foreach ($customColors as $name => $value) {
            $cssContent .= '--wg-fsb-' . $name . ':' . $value . ';';
        }

        $cssContent .= '}';
        $cssFile = $this->local_path . 'views/css/custom.css';

        if (!file_exists($cssFile)) {
            $cssDir = dirname($cssFile);
            if (!is_dir($cssDir)) {
                mkdir($cssDir, 0777, true);
            }
            touch($cssFile, strtotime('now'));
        }

        if (file_exists($cssFile) && is_writable($cssFile)) {
            file_put_contents($cssFile, (string) $cssContent);
        } else {
            $this->_errors[] = $this->trans('File %file_name% is not writable or does not exist in the directory %dir%', ['%file_name%' => 'custom.css', '%dir%' => $this->_path], 'Modules.Wgfreeshippingbar.Admin');
        }
    }
}

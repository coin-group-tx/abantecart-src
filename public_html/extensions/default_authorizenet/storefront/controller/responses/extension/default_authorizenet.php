<?php
/*
 *   $Id$
 *
 *   AbanteCart, Ideal OpenSource Ecommerce Solution
 *   http://www.AbanteCart.com
 *
 *   Copyright © 2011-2026 Belavier Commerce LLC
 *
 *   This source file is subject to Open Software License (OSL 3.0)
 *   License details are bundled with this package in the file LICENSE.txt.
 *   It is also available at this URL:
 *   <http://www.opensource.org/licenses/OSL-3.0>
 *
 *  UPGRADE NOTE:
 *    Do not edit or add to this file if you wish to upgrade AbanteCart to newer
 *    versions in the future. If you wish to customize AbanteCart for your
 *    needs, please refer to http://www.AbanteCart.com for more information.
 */

/**
 * Class ControllerResponsesExtensionAuthorizeNet
 *
 * @property  ModelExtensionDefaultAuthorizeNet $model_extension_default_authorizenet
 */
class ControllerResponsesExtensionDefaultAuthorizeNet extends AController
{
    public function main()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadLanguage('default_authorizenet/default_authorizenet');

        $this->buildForm();

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);

        $this->data['callback_url'] = $this->html->getSecureURL('r/extension/default_authorizenet/send');
        $this->data['acceptUiUrl'] = $this->config->get('default_authorizenet_test_mode')
            ? 'https://jstest.authorize.net/v3/AcceptUI.js'
            : 'https://js.authorize.net/v3/AcceptUI.js';

        $this->data['error_unknown'] = $this->language->get('error_unknown');
        $this->view->batchAssign($this->data);
        $this->processTemplate('responses/default_authorizenet.tpl');
    }

    public function buildForm()
    {
        $orderId = (int) $this->session->data['order_id'];
        if (!$orderId) {
            redirect($this->html->getSecureURL('checkout/cart'));
        }
        /** @var ModelCheckoutOrder $mdl */
        $mdl = $this->loadModel('checkout/order');
        $this->data['order_info'] = $orderInfo = $mdl->getOrder($orderId);
        if (!$orderInfo) {
            redirect($this->html->getSecureURL('checkout/cart'));
        }
        $this->data['payment_address'] = $orderInfo['payment_address_1'] . " " . $orderInfo['payment_address_2'];
        $this->data['text_wait'] = $this->language->get('text_wait');

        $form = new AForm();
        $form->setForm(
            [
                'form_name' => 'authorizenet',
            ]
        );

        $this->data['form_open'] = $form->getFieldHtml(
            [
                'type' => 'form',
                'name' => 'authorizenet',
                'attr' => 'class = "validate-creditcard"',
                'csrf' => true,
            ]
        );

        $this->data['submit'] = HtmlElementFactory::create(
            [
                'type'  => 'button',
                'name'  => 'authorizenet_button',
                'text'  => $this->language->get('button_confirm'),
                'style' => 'button btn-primary',
                'icon'  => 'icon-ok icon-white',
            ]
        );

        $this->data['button_back'] = HtmlElementFactory::create(
            [
                'type'  => 'button',
                'name'  => 'authorizenet_back',
                'text'  => $this->language->get('button_back'),
                'style' => 'button btn-default',
                'icon'  => 'icon-arrow-left',
            ]
        );
    }

    public function send()
    {
        if (!$this->csrftoken->isTokenValid()) {
            $output['error_text'] = $this->language->get('error_unknown');
            $err = new AError('');
            $err->toJSONResponse(
                'VALIDATION_ERROR_406',
                $output
            );
        }
        
        $this->loadLanguage('default_authorizenet/default_authorizenet');
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        
        $post = $this->request->post;

        /** @var ModelCheckoutOrder $mdl */
        $mdl = $this->loadModel('checkout/order');
        /** @var ModelExtensionDefaultAuthorizenet $anetMdl */
        $anetMdl = $this->loadModel('extension/default_authorizenet');
        $orderId = (int) $this->session->data['order_id'];
        $orderInfo = $mdl->getOrder($orderId);
        if (!$orderInfo || !$orderId) {
            $output['error_text'] = 'Order not found';
            $err = new AError('');
            $err->toJSONResponse(
                'VALIDATION_ERROR_402',
                $output
            );
        }
        // currency code
        $currency = $this->currency->getCode();
        // order amount without decimal delimiter
        $amount = round((float) $orderInfo['total'], 2);

        ADebug::checkpoint('AuthorizeNet Payment: Order ID ' . $orderId);

        $pd = [
            'amount'             => $amount,
            'currency'           => $currency,
            'order_id'           => $orderId,
            'cc_owner_firstname' => html_entity_decode($orderInfo['payment_firstname'], ENT_QUOTES, 'UTF-8'),
            'cc_owner_lastname'  => html_entity_decode($orderInfo['payment_lastname'], ENT_QUOTES, 'UTF-8'),
            'dataDescriptor'     => $post['dataDescriptor'],
            'dataValue'          => $post['dataValue'],
        ];

        $processResult = $anetMdl->processPayment($pd);

        ADebug::variable('Processing payment result: ', $processResult);
        if ($processResult['error']) {
            // transaction failed
            $output['error_text'] = (string) $processResult['error'];
            if ($processResult['code']) {
                $output['error_text'] .= ' (' . $processResult['code'] . ')';
            }
        } else {
            if ($processResult['paid']) {
                $output['success'] = $this->html->getSecureURL('checkout/finalize');
            } else {
                //Unexpected result
                $output['error_text'] = $this->language->get('error_system') . '(abc)';
            }
        }

        //init controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);

        if (isset($output['error']) && $output['error']) {
            $csrftoken = $this->registry->get('csrftoken');
            $output['csrfinstance'] = $csrftoken->setInstance();
            $output['csrftoken'] = $csrftoken->setToken();
            $err = new AError('');
            $err->toJSONResponse(
                'APP_ERROR_402',
                $output
            );
        }
        $this->load->library('json');
        $this->response->addJSONHeader();
        $this->response->setOutput(AJson::encode($output));
    }
}

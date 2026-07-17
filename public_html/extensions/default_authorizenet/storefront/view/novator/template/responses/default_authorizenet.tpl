<div class="anet-wrapper">
    <?php
    $form_open->attr .= ' novalidate ';
    echo $form_open; ?>
    <?php
    echo $this->getHookVar('payment_table_pre'); ?>
    <div class="mb-3">
        <input type="hidden" name="dataValue" id="dataValue"/>
        <input type="hidden" name="dataDescriptor" id="dataDescriptor"/>
    </div>
    <?php
    echo $this->getHookVar('payment_table_post'); ?>

    <div class="form-group action-buttons text-center">
        <button id="anetBtn"
                class="AcceptUI d-none"
                type="button"
                data-apiLoginID="<?php echo $this->config->get('default_authorizenet_api_login_id'); ?>"
                data-clientKey="<?php echo $this->config->get('default_authorizenet_api_public_key'); ?>"
                data-acceptUIFormBtnTxt="<?php echo_html2view($submit->text); ?>"
                data-acceptUIFormHeaderTxt="<?php echo_html2view($text_credit_card); ?>"
                data-paymentOptions='{"showCreditCard": true, "showBankAccount": false}'
                data-billingAddressOptions='{"show": false, "required": false}'
                data-responseHandler="responseHandler">
        </button>
        <a id="<?php echo $submit->name ?>" class="btn btn-primary lock-on-click" title="<?php echo_html2view($submit->text); ?>">
            <i class="fa fa-check"></i>
            <?php echo $submit->text; ?>
        </a>
    </div>
    </form>
</div>

<script type="text/javascript">
    $(document).ready( function () {
        loadScript("<?php echo $acceptUiUrl;?>",
            function () {
                //validate submit
                $('#<?php echo $submit->name ?>').on('click', function () {
                    let form = $(this).parents('form');
                    if (!validateForm(form)) {
                        form.addClass('was-validated');
                        try {
                            resetLockedButton($(this));
                        } catch (e) {}
                        return false;
                    }
                    $('.alert').remove();
                    $('#anetBtn').click();                    
                    return true;
                });
            }
        );
    });

    function responseHandler(response) {
        if (response.messages.resultCode === "Error") {
            let i = 0;
            let alert = '';
            while (i < response.messages.message.length) {
                alert = '<div class="alert alert-warning"><i class="bi bi-exclamation-triangle me-3"></i>'
                    + 'AuthorizeNet: '
                    + response.messages.message[i].text
                    + '( ' + response.messages.message[i].code + ' )</div>';
                $('#<?php echo $form_open->name?>').before(alert);
                i++;
            }
            scrollToElementTop('#<?php echo $form_open->name?>');
            try { 
                resetLockedButton($('#<?php echo $submit->name ?>')); 
            }
            catch (e) {
                console.log(e);
            }
        } else {
            paymentFormUpdate(response.opaqueData);
        }
    }

    function paymentFormUpdate(opaqueData) {
        scrollToElementTop('#<?php echo $form_open->name?>');
        $("#dataDescriptor").val(opaqueData.dataDescriptor);
        $("#dataValue").val(opaqueData.dataValue);
        confirmSubmit($('#authorizenet'), <?php js_echo($callback_url);?>);
    }

    function confirmSubmit(formElm, url) {
        $.ajax(
            {
                type: 'POST',
                url: url,
                data: formElm.serialize(),
                dataType: 'json',
                success: function (data) {
                    if (!data) {
                        formElm.before('<div class="alert alert-warning"><i class="bi bi-bug me-2"></i>' +
                        <?php js_echo($error_unknown); ?>
                            + '</div>'
                        );
                        try { resetLockedButton($('#<?php echo $submit->name ?>')); } catch (e) {
                            console.log(e);
                        }
                    } else if (data.success) {
                        location = data.success;                       
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    jqXHR.responseJSON
                    formElm.before(
                        '<div class="alert alert-warning"><i class="bi bi-exclamation-triangle me-2"></i>'
                        + jqXHR.responseJSON.error_text 
                        + '</div>'
                    );
                    updateCsrfTokens(formElm, jqXHR.responseJSON);
                    try { resetLockedButton($('#<?php echo $submit->name ?>')); } catch (e) {
                        console.log(e);
                    }
                }
            }
        );
    }
</script>

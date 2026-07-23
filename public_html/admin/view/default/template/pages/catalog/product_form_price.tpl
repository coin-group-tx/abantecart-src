<table class="border-0">
    <tr>
        <td>
            <div class="input-group afield">
                <div class="input-group-addon"><?php echo $data['currency']['symbol_left']; ?></div>
                <?php echo $data['price']; ?>
                <div class="input-group-addon"><?php echo $data['currency']['symbol_right']; ?></div>
            </div>
        </td>
        <td>
            <b class="fa-2x ml10 mr10">&plus;</b>
        </td>
        <td>
            <div class="input-group">
                <div class="input-group-addon">
                    <?php echo $data['entry_tax_rule']; ?>
                </div>
                <?php echo $data['tax_selector']; ?>
            </div>
        </td>
        <td><b class="fa-2x ml10 mr10">&equals;</b></td>
        <td>
            <div class="input-group">
                <div class="input-group-addon"><?php echo $data['entry_price_with_tax']; ?></div>
                <div class="input-group-addon"><?php echo $data['currency']['symbol_left']; ?></div>
                <?php echo $data['price_with_tax']; ?>
                <div class="input-group-addon"><?php echo $data['currency']['symbol_right']; ?></div>
            </div>
        </td>
    </tr>
</table>

<script type="text/javascript">
    let priorElm;
    $(document).on(
        'change blur drop focus',
        'input[name="price"], input[name="price_with_tax"]',
        function (e) {
            priorElm = e.type === 'drop' ? $(this) : priorElm;
        }
    );
    $(document).on('change', 'select[name="tax_selector"]', onKUp);

    let timer;
    const waitTime = 500;

    $('input[name="price"]')[0].addEventListener('keyup', onKUp);
    $('input[name="price_with_tax"]')[0].addEventListener('keyup', onKUp);
    function onKUp(event){
        clearTimeout(timer);
        timer = setTimeout(() => {
            if($(event.target).attr('name')!=='tax_selector') {
                priorElm = $(event.target).change();
            }
            getTaxedPrice($(event.target));
        }, waitTime);
    }

    function getTaxedPrice(initiator, sendData = {}) {
        let priceElm = $('input[name="price"]');
        let priceWithTaxElm = $('input[name="price_with_tax"]');
        let precision = 0;
        if (initiator.attr('name') === 'tax_selector') {
            initiator = priceElm;
        }
        if (initiator.val().length > 0) {
            let arr = initiator.val().split('.');
            let end = arr.length>1 ? initiator.val().split('.').slice(-1)[0] : 0;
            precision = end.length > precision ? end.length : precision;
        }
        numberSeparators = {precision: precision, dec_point: '.', thousands_point: null};

        formatPrice(priceElm.get(0));
        formatPrice(priceWithTaxElm.get(0));
        if (initiator.val() !== null && initiator.val() !== '') {
            sendData[initiator.attr('name')] = initiator.val();
            sendData['tax_class_id'] = $('select[name="tax_selector"]').val();
            let urlParams = new URLSearchParams(sendData).toString();
            let value = urlParams ? '&' + urlParams : '';
            $.get(
                '<?php echo $data['price_calc_url']?>' + value,
                function (res) {
                    if (initiator.attr('name') === 'price') {
                        priceWithTaxElm.val(res);
                        formatPrice(priceWithTaxElm.get(0), <?php echo (int)$data['currency']['decimal_place']; ?>);
                    } else {
                        priceElm.val(res);
                        formatPrice(priceElm.get(0), <?php echo (int)$data['currency']['decimal_place']; ?>);
                        priceElm.aform().change();
                    }
                }
            );
        }
    }

    $(document).ready(function () {
        let price = $('input[name="price"]');
        let p = price.val();
        if (p !== '' && p !== '0.0' && p !== '0.00') {
            getTaxedPrice(price);
        }
    });
</script>

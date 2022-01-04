define([
    'jquery',
    'Magento_Ui/js/modal/modal',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/cart/totals-processor/default',
    'Magento_Checkout/js/model/cart/cache',
], function (
    $,
    modal,
    quote,
    totalsDefaultProvider,
    cartCache
    ) {
    'use strict';

    return {
        sendPromoAddToCartRequest: function (ajaxUrl, displayPopup, params, modalSelector, modalOptions, toRunExtraCallbackFunction, extraCallbackFunction) {
            $.ajax({
                url: ajaxUrl,
                type: 'post',
                data: params,
                showLoader: true,
                dataType: 'json',
                success: function (response) {
                    if(displayPopup == true){
                        if(!$.isEmptyObject(response)){
                            if(response.added == 'false' && response.hasOwnProperty('html')){
                                modal(modalOptions, $(modalSelector));
                                $(modalSelector).modal('openModal').html(response.html).trigger('contentUpdated');
                            }
                        }
                    }else{
                        if(response.hasOwnProperty('status')
                            && (response['status'] == 'success')){
                            if(response.hasOwnProperty('cart_html')){
                                $("form.form.form-cart").replaceWith($(response['cart_html']).find("form.form.form-cart")).trigger('contentUpdated');
                            }
                        }

                        cartCache.clear('totals');
                        totalsDefaultProvider.estimateTotals(quote.shippingAddress());

                        if(toRunExtraCallbackFunction){
                            extraCallbackFunction();
                        }
                        try {
                            reLoadHints();
                        }
                        catch(err) {}
                    }
                }
            });
        },
        isAllConfigurationOptionsSelected: function(selectionsPerStep, currentVisibleStep, promoWrapperSelector, that, productItemInfoSelector){
            var allConfigurationsHaveSelectedOption = true;
            try{
                for(var productId in selectionsPerStep[currentVisibleStep]){
                    $(promoWrapperSelector + '[data-step-index="' + currentVisibleStep + '"] ' + productItemInfoSelector +'[data-product-id="' + productId + '"] .swatch-attribute')
                        .each(
                        function(){
                            if($(this).find(".swatch-option.selected").length == 0){
                                allConfigurationsHaveSelectedOption = false;
                            }
                        });
                }
            }catch(err) {}
            return allConfigurationsHaveSelectedOption;
        },
        getStepSelectionsQtyExclProduct: function(stepNumber, exclProductId, selectionsPerStep){
            var stepSelectedQty = 0;

            if(typeof selectionsPerStep[stepNumber] != 'undefined'){
                for (var productId in selectionsPerStep[stepNumber]) {
                    if((exclProductId == 0) || (productId != exclProductId)){
                        if (selectionsPerStep[stepNumber].hasOwnProperty(productId)) {
                            stepSelectedQty += parseInt(selectionsPerStep[stepNumber][productId]);
                        }
                    }
                }
            }

            return stepSelectedQty;
        },
        isRequiredNumberOfProductsSelected: function(selectionsPerStep, currentVisibleStep, promoWrapperSelector){
            try{
                var requiredNumberOfProductsForStep = $(promoWrapperSelector + '[data-step-index="' + currentVisibleStep + '"]').attr("data-group-qty");
                var stepSelectionsLength = 0;
                try{
                    stepSelectionsLength = this.getStepSelectionsQtyExclProduct(currentVisibleStep, 0, selectionsPerStep);
                }catch(error){}

                if(stepSelectionsLength < requiredNumberOfProductsForStep){
                    return false;
                }
            }catch(err) {}
            return true;
        },
        isAtLeastOneProductSelected: function(selectionsPerStep, currentVisibleStep){
            try{
                var stepSelectionsLength = 0;
                try{
                    stepSelectionsLength = this.getStepSelectionsQtyExclProduct(currentVisibleStep, 0, selectionsPerStep);
                }catch(error){}

                if(stepSelectionsLength < 1){
                    return false;
                }
            }catch(err) {}
            return true;
        },
        isAllCustomOptionsSelected : function(selectionsPerStep, currentVisibleStep, promoWrapperSelector, that, productItemInfoSelector, wrapperSelector){
            var allCustomOptionsConfigured = true;
            try{
                for(var productId in selectionsPerStep[currentVisibleStep]){
                    $(promoWrapperSelector + '[data-step-index="' + currentVisibleStep + '"] '+ productItemInfoSelector +'[data-product-id="' + productId + '"] '+ wrapperSelector +' div.field.required select').each(function(){
                        if($(this).find("option:selected").val() == ''){
                            $(that).addClass("mage-error");
                            allCustomOptionsConfigured = false;
                        }else{
                            $(that).removeClass("mage-error");
                        }
                    });

                    $(promoWrapperSelector +'[data-step-index="' + currentVisibleStep + '"] '+ productItemInfoSelector +'[data-product-id="' + productId + '"] '+ wrapperSelector +' div.field.required input').each(function(){
                        if($(this).val() == ''){
                            $(that).addClass("mage-error");
                            allCustomOptionsConfigured = false;
                        }else{
                            $(that).removeClass("mage-error");
                        }
                    });
                }
            }catch(err) {}
            return allCustomOptionsConfigured;
        },
        getJSONProductsAddData: function(checkboxContainerSelector, itemInfoSelector, itemQtySelector){
            var productsAddData = [];
            var productAddData;
            var productOptions;
            var productContainer;
            var optionId;
            var matches;
            var productQty;
            var productQtyInputValue;
            $(checkboxContainerSelector + " input:checked").each(function(){
                productContainer = $(this).closest(itemInfoSelector);
                productOptions = [];
                productContainer.find(".swatch-attribute").each(function(){
                    productOptions.push({
                        "attribute_id" : $(this).attr("attribute-id"),
                        "option_id" : $(this).find(".swatch-option.selected").attr("option-id")
                    });
                });
                productContainer.find(".product-custom-option").each(function(){
                    matches = $(this).attr('name').match(/options\[(.*?)\]/);
                    if(matches.length > 1){
                        optionId = matches[1];
                        productOptions[optionId] = $(this).val();
                    }
                });

                productQtyInputValue = productContainer.find(itemQtySelector + " input").val();

                productQty = 1;
                if($.isNumeric(productQtyInputValue)
                    && productQtyInputValue > 0){
                    productQty = productQtyInputValue;
                }

                productAddData = {
                    'product_id' : productContainer.attr("data-product-id"),
                    'qty' : productQty,
                    'options' : productOptions
                };
                productsAddData.push(productAddData);
            });
            return JSON.stringify(productsAddData);
        },
        swatchOptionClickedHandle: function(that, checkboxContainer){
            if($(that).hasClass("selected")
                && !(checkboxContainer.find("input").is(":checked"))){
                checkboxContainer.trigger("click");
            }
            if(this.isAllConfigurationOptionsSelected()){
                $(".cart-add-promo-wrapper-error-configurations").hide();
            }
        },
        performAddToCartRequest: function(ajaxUrl, promoCheckboxContainerSelector, promoItemInfoContainerSelector, promoItemQtySelector, toRunExtraCallbackFunction, extraCallbackFunction, currentGiftTriggerItemsOfSameGroup){
            $.ajax({
                url: ajaxUrl,
                type: 'post',
                dataType: 'json',
                showLoader: true,
                data: {
                    products_add_data: this.getJSONProductsAddData(promoCheckboxContainerSelector, promoItemInfoContainerSelector, promoItemQtySelector),
                    gift_trigger_item_ids_qtys_of_same_group: currentGiftTriggerItemsOfSameGroup
                },
                success: function (response) {
                    if(response.hasOwnProperty('status')
                        && (response['status'] == 'success')){
                        if(response.hasOwnProperty('cart_html')){
                            $("form.form.form-cart").replaceWith($(response['cart_html']).find("form.form.form-cart"));
                            $("form.form.form-cart").trigger('contentUpdated');
                        }
                    }

                    cartCache.clear('totals');
                    totalsDefaultProvider.estimateTotals(quote.shippingAddress());
                    if(toRunExtraCallbackFunction){
                        extraCallbackFunction();
                    }
                    try {
                        reLoadHints();
                    }
                    catch(err) {}
                }
            });
        },
        updateCurrentStepSelectedText: function(currentVisibleStep, selectionsPerStep, currentGroupDivSelector, promoChosenSelector){
            var currentStepSelectedQty = this.getStepSelectionsQtyExclProduct(currentVisibleStep, 0, selectionsPerStep);
            var currentGroupDiv = $(currentGroupDivSelector + '[data-step-index="' + currentVisibleStep + '"]');
            var currentGroupQty = currentGroupDiv.attr("data-group-qty");
            var selectionStepText = "Selected " + currentStepSelectedQty + " out of " + currentGroupQty;

            currentGroupDiv.find(promoChosenSelector).text(selectionStepText);
        }
    };
});

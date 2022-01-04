define([
    'jquery',
    'Magento_Ui/js/modal/modal',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/cart/totals-processor/default',
    'Mexbs_ApBase/js/model/promo-add'
], function (
    $,
    modal,
    quote,
    totalsDefaultProvider,
    promoAdd
    ) {
    'use strict';

    return function(options){
        $(document).ready(function(){
            var showPromoBlockTitleIfPromoBlockNotEmpty = function(){
                if($(".cart-promos-wrapper").html().trim() != ''){
                    $(".cart-promos-wrapper-title").show();
                }
            };
            showPromoBlockTitleIfPromoBlockNotEmpty();


            var reloadPromoProductsBlock = function(){
                $(".cart-promos-wrapper").html('');
                $(".cart-promos-wrapper-title").hide();
                $.ajax({
                    url: options.promoProductsUrl,
                    type: 'post',
                    dataType: 'json',
                    success: function (response) {
                        var promosHtml = '';
                        var promoHtml;
                        for(var promoHtmlIndex in response){
                            promoHtml = "";
                            if(response.hasOwnProperty(promoHtmlIndex)){
                                promoHtml = response[promoHtmlIndex];
                            }
                            if(promoHtml != ""){
                                promosHtml = promosHtml + promoHtml;
                            }
                        }

                        promosHtml += '</div> ';
                        $(".cart-promos-wrapper").html(promosHtml);
                        if(promosHtml.trim() != ''){
                            $(".cart-promos-wrapper-title").show();
                        }
                    }
                });
            };

            var popupTpl = '<aside ' +
                'class="modal-<%= data.type %> <%= data.modalClass %> ' +
                '<% if(data.responsive){ %><%= data.responsiveClass %><% } %> ' +
                '<% if(data.innerScroll){ %><%= data.innerScrollClass %><% } %>"'+
                'data-role="modal"' +
                'data-type="<%= data.type %>"' +
                'tabindex="0">'+
                '    <div data-role="focusable-start" tabindex="0"></div>'+
                '    <div class="modal-inner-wrap"'+
                'data-role="focusable-scope">'+
                '    <div '+
                'class="modal-content" '+
                'data-role="content">' +
                '<button '+
                'class="action-close" '+
                'data-role="closeBtn" '+
                'type="button">'+
                '<span><%= data.closeText %></span>' +
                '</button>' +
                '</div>'+
                '   </div>'+
                '   </aside>';

            var modalOptions = {
                type: 'popup',
                responsive: false,
                innerScroll: false,
                buttons: [],
                popupTpl: popupTpl,
                modalClass: 'cart-promo-add-to-cart-modal-wrapper',
                closeText: 'X'
            };

            $(".cart-promos-wrapper").on("click", "button[data-action='promo-add-to-cart']", function(){
                var ajaxUrl = options.promoAddToCartHtmlUrl;
                var params = { rule_id: $(this).attr("data-promo-id")};
                var displayPopup = true;
                if(($(this).closest(".cart-promo-wrapper").attr("data-some-products-has-options") == 0)
                    && ($(this).closest(".cart-promo-wrapper").attr("data-rule-has-selections") == 0)
                    ){
                    displayPopup = false;
                    ajaxUrl = options.promoAddToCartUrl;
                    var productsAddData = [];
                    $(this).closest(".cart-promo-wrapper").find(".cart-promo-product-group-wrapper").each(function(){
                        productsAddData.push({'product_id' : $(this).attr("data-first-product-id"), 'qty' : $(this).attr('data-group-qty-left-to-select')});
                    });

                    params = { products_add_data: JSON.stringify(productsAddData)};
                }

                promoAdd.sendPromoAddToCartRequest(ajaxUrl, displayPopup, params, '.cart-promo-add-to-cart-modal', modalOptions, true, reloadPromoProductsBlock);
            });
            $(".cart-promos-wrapper").on("mouseover", ".cart-promo-product-group-wrapper",
                function(){
                    var groupTitleDiv = $(this).find(".cart-promo-product-group-title");
                    if(groupTitleDiv.html() != ''){
                        groupTitleDiv.show();
                    }
                });
            $(".cart-promos-wrapper").on("mouseout", ".cart-promo-product-group-wrapper",
                function(){
                    $(this).find(".cart-promo-product-group-title").hide();
                });

            var currentVisibleStep = 1;

            var hideStep = function(stepNumber){
                $('.cart-add-promo-group-wrapper[data-step-index="'+stepNumber+'"]').hide();
            };
            var showStep = function(stepNumber){
                $('.cart-add-promo-group-wrapper[data-step-index="'+stepNumber+'"]').show();
            };

            var selectionsPerStep = [];


            var getMissingProductsText = function(){
                var currentGroupSelected = 0;
                if(typeof selectionsPerStep[currentVisibleStep] != 'undefined'){
                    currentGroupSelected = promoAdd.getStepSelectionsQtyExclProduct(currentVisibleStep, 0, selectionsPerStep);
                }
                var currentGroupQty = $('.cart-add-promo-group-wrapper[data-step-index="' + currentVisibleStep + '"]').attr("data-group-qty");
                var addS = "s";
                if((currentGroupQty - currentGroupSelected) == 1){
                    addS = "";
                }
                return "Please select " + (currentGroupQty - currentGroupSelected) + " more product" + addS;

            };

            var updateProductQtyOfCurrentStep = function(productId){
                if(typeof selectionsPerStep[currentVisibleStep] == 'undefined'){
                    selectionsPerStep[currentVisibleStep] = [];
                }
                var currentGroupQty = $('.cart-add-promo-group-wrapper[data-step-index="' + currentVisibleStep + '"]').attr("data-group-qty");
                var qtyInput = $('.cart-add-promo-product-item-info[data-product-id="'+productId+'"]').find(".cart-add-promo-product-item-qty input");
                var checkbox = $('.cart-add-promo-product-item-info[data-product-id="'+productId+'"]').find(".cart-add-promo-product-checkbox-container input");

                var productQty = 1;
                if(qtyInput.length > 0
                    && $.isNumeric(qtyInput.val())){
                    productQty = qtyInput.val();
                }

                var qtyLeftToSelect = currentGroupQty - promoAdd.getStepSelectionsQtyExclProduct(currentVisibleStep, productId, selectionsPerStep);

                if(productQty > qtyLeftToSelect){
                    productQty = qtyLeftToSelect;
                }

                if(checkbox.is(":checked")
                    && (productQty > 0)){
                    selectionsPerStep[currentVisibleStep][productId] = productQty;
                    qtyInput.val(productQty);
                }else{
                    checkbox.prop("checked", false);
                    if(typeof selectionsPerStep[currentVisibleStep][productId] != 'undefined'){
                        selectionsPerStep[currentVisibleStep].splice(productId, 1);
                    }

                    qtyInput.val("");
                }
            };

            $("body").on("click", 'button[data-action="go-to-previous-step"]',
                function(){
                    hideStep(currentVisibleStep);
                    currentVisibleStep--;
                    showStep(currentVisibleStep);
                }).on("click", 'div.swatch-option',
                function(){
                    var checkboxContainer = $(this).closest(".cart-add-promo-product-item-info").find(".cart-add-promo-product-checkbox-container");

                    promoAdd.swatchOptionClickedHandle(this, checkboxContainer);
                }).on("click", '.cart-add-promo-product-checkbox-container',
                function(event){
                    if(!($(this).find('input').is(":checked"))){
                        $(this).find("input").prop("checked", true);
                    }else{
                        $(this).find("input").prop("checked", false);
                    }

                    var productId = $(this).closest('.cart-add-promo-product-item-info').attr("data-product-id");

                    updateProductQtyOfCurrentStep(productId);
                    promoAdd.updateCurrentStepSelectedText(currentVisibleStep, selectionsPerStep, '.cart-add-promo-group-wrapper', '.cart-add-promo-group-chosen');

                    event.stopPropagation();
                    event.preventDefault();
                }).on("click", '.cart-add-promo-wrapper-button-done',
                function(){
                    var allConfigurationOptionsSelected = promoAdd.isAllConfigurationOptionsSelected(selectionsPerStep, currentVisibleStep,'.cart-add-promo-group-wrapper', this, '.cart-add-promo-product-item-info');
                    var requiredNumberOfProductsSelected = promoAdd.isRequiredNumberOfProductsSelected(selectionsPerStep, currentVisibleStep,'.cart-add-promo-group-wrapper');
                    var allCustomOptionsSelected = promoAdd.isAllCustomOptionsSelected(selectionsPerStep, currentVisibleStep,'.cart-add-promo-group-wrapper', this, '.cart-add-promo-product-item-info');
                    if(!allConfigurationOptionsSelected || !allCustomOptionsSelected){
                        $('.cart-add-promo-wrapper-error-configurations[data-step-index="' + currentVisibleStep + '"]').show();
                    }else{
                        $('.cart-add-promo-wrapper-error-configurations[data-step-index="' + currentVisibleStep + '"]').hide();
                    }
                    if(!requiredNumberOfProductsSelected){
                        $('.cart-add-promo-wrapper-error-products[data-step-index="' + currentVisibleStep + '"]').text(getMissingProductsText()).show();
                    }else{
                        $('.cart-add-promo-wrapper-error-products[data-step-index="' + currentVisibleStep + '"]').first().hide();
                    }
                    if(allConfigurationOptionsSelected && allCustomOptionsSelected && requiredNumberOfProductsSelected){
                        $(".cart-promo-add-to-cart-modal-wrapper button.action-close").trigger("click");
                        selectionsPerStep = [];
                        currentVisibleStep = 1;

                        promoAdd.performAddToCartRequest(options.promoAddToCartUrl, '.cart-add-promo-product-checkbox-container', '.cart-add-promo-product-item-info', '.cart-add-promo-product-item-qty', true, reloadPromoProductsBlock);
                    }

                }).on("click", '.cart-add-promo-wrapper-button-next',
                function(){
                    var allConfigurationOptionsSelected = promoAdd.isAllConfigurationOptionsSelected(selectionsPerStep, currentVisibleStep,'.cart-add-promo-group-wrapper', this, '.cart-add-promo-product-item-info');
                    var allCustomOptionsSelected = promoAdd.isAllCustomOptionsSelected(selectionsPerStep, currentVisibleStep,'.cart-add-promo-group-wrapper', this, '.cart-add-promo-product-item-info');
                    var requiredNumberOfProductsSelected = promoAdd.isRequiredNumberOfProductsSelected(selectionsPerStep, currentVisibleStep,'.cart-add-promo-group-wrapper');
                    if(!allConfigurationOptionsSelected || !allCustomOptionsSelected){
                        $('.cart-add-promo-wrapper-error-configurations[data-step-index="' + currentVisibleStep + '"]').show();
                    }else{
                        $('.cart-add-promo-wrapper-error-configurations[data-step-index="' + currentVisibleStep + '"]').hide();
                    }
                    if(!requiredNumberOfProductsSelected){
                        $('.cart-add-promo-wrapper-error-products[data-step-index="' + currentVisibleStep + '"]').text(getMissingProductsText()).show();
                    }else{
                        $('.cart-add-promo-wrapper-error-products[data-step-index="' + currentVisibleStep + '"]').hide();
                    }

                    if(allConfigurationOptionsSelected
                        && requiredNumberOfProductsSelected
                        && allCustomOptionsSelected){
                        hideStep(currentVisibleStep);
                        currentVisibleStep++;
                        showStep(currentVisibleStep);
                    }
                })
                .on("change", ".cart-add-promo-product-item-qty input", function(){
                    var newProductQty = 0;
                    if($.isNumeric($(this).val())){
                        newProductQty = $(this).val();
                    }
                    var checkboxContainer = $(this).closest(".cart-add-promo-product-item-info").find(".cart-add-promo-product-checkbox-container");
                    var productId = $(this).closest(".cart-add-promo-product-item-info").attr("data-product-id");
                    if(newProductQty > 0){
                        if(!(checkboxContainer.find("input").is(":checked"))){
                            checkboxContainer.find("input").prop("checked", true);
                        }
                    }else{
                        if(checkboxContainer.find("input").is(":checked")){
                            checkboxContainer.find("input").prop("checked", false);
                        }
                    }
                    updateProductQtyOfCurrentStep(productId);
                    promoAdd.updateCurrentStepSelectedText(currentVisibleStep, selectionsPerStep, '.cart-add-promo-group-wrapper', '.cart-add-promo-group-chosen');
                });
        });
    };

});
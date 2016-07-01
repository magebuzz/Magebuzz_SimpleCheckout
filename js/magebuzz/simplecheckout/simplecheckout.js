function $RF(el, radioGroup) {
  if ($(el).type && $(el).type.toLowerCase() == 'radio') {
    var radioGroup = $(el).name;
    var el = $(el).form;
  } else if ($(el).tagName.toLowerCase() != 'form') {
    return false;
  }

  var checked = $(el).getInputs('radio', radioGroup).find(
    function (re) {
      return re.checked;
    }
  );
  return (checked) ? $F(checked) : null;
}

function save_address_information(save_address_url) {
  var form = $('simplecheckout-form');
  var parameters = {};
  var items = get_form_data($$('input[name^=billing]', 'select[name^=billing]'));
  var names = items.pluck('name');
  var values = items.pluck('value');
  var street_count = 0;
  for (var x = 0; x < names.length; x++) {
    if (names[x] != 'payment[method]') {
      var current_name = names[x];
      if (names[x] == 'billing[street][]') {
        current_name = 'billing[street][' + street_count + ']';
        street_count = street_count + 1;
      }
      parameters[current_name] = values[x];
    }
  }

  var use_for_shipping = $('billing:use_for_shipping');
  if (use_for_shipping && use_for_shipping.getValue() != '1') {
    // Get shipping information
    var items = get_form_data($$('input[name^=shipping]', 'select[name^=shipping]'));
    var shipping_names = items.pluck('name');
    var shipping_values = items.pluck('value');
    var street_count = 0;

    for (var x = 0; x < shipping_names.length; x++) {
      if (shipping_names[x] != 'shipping_method') {
        var current_name = shipping_names[x];
        if (shipping_names[x] == 'shipping[street][]') {
          current_name = 'shipping[street][' + street_count + ']';
          street_count = street_count + 1;
        }

        parameters[current_name] = shipping_values[x];
      }
    }
  }

  var shipping_method = $('shipping-method-load');
  if (shipping_method) {
    shipping_method.update('<div class="ajax-loader" id="ajax-loader">&nbsp;</div>');
  }

  var payment_method_load = $('payment-method-load');
  if (payment_method_load && isUpdatePayment) {
    payment_method_load.update('<div class="ajax-loader" id="ajax-loader">&nbsp;</div>');
  }

  var order_review = $('checkout-review-load');
  if (order_review) {
    order_review.update('<div class="ajax-loader" id="ajax-loader">&nbsp;</div>');
  }

  $('simplecheckout-button-place-order').disabled = true;
  var request = new Ajax.Request(
    save_address_url,
    {
      method: 'post',
      parameters: parameters,
      onSuccess: function (transport) {
        if (transport.status == 200) {
          var data = transport.responseText.evalJSON();
          if (shipping_method)
            shipping_method.update(data.shipping_method);
          if (isUpdatePayment) {
            payment_method_load.update(data.payment_method);
            if ($RF(form, 'payment[method]') != null) {
              try {
                var payment_method = $RF(form, 'payment[method]');
                $('container_payment_method_' + payment_method).show();
                $('payment_form_' + payment_method).show();
              } catch (err) {

              }
            }
          }
          order_review.update(data.order_review);
          $('simplecheckout-button-place-order').disabled = false;
        }
      }
    }
  );
}

function save_shipping_method(update_payment) {
  var form = $('simplecheckout-form');
  var shipping_method = $RF(form, 'shipping_method');
  var payment_method = $RF(form, 'payment[method]');

  var payment_method_reload = $('payment-method-load');
  if (payment_method_reload && update_payment) {
    payment_method_reload.update('<div class="ajax-loader" id="ajax-loader">&nbsp;</div>');
  }

  var order_review = $('checkout-review-load');
  if (order_review) {
    order_review.update('<div class="ajax-loader" id="ajax-loader">&nbsp;</div>');
  }

  var parameters = {
    shipping_method: shipping_method,
    payment_method: payment_method
  }

  //disable checkout button on reloading
  $('simplecheckout-button-place-order').disabled = true;

  var request = new Ajax.Request(
    save_shipping_method_url,
    {
      method: 'post',
      onSuccess: function (transport) {
        if (transport.status == 200) {
          var data = transport.responseText.evalJSON();
          if (update_payment) {
            payment_method_reload.update(data.payment_method);
            if ($RF(form, 'payment[method]') != null) {
              try {
                var payment_method = $RF(form, 'payment[method]');
                $('container_payment_method_' + payment_method).show();
                $('payment_form_' + payment_method).show();
              } catch (err) {

              }
            }
          }
          order_review.update(data.order_review);
          $('simplecheckout-button-place-order').disabled = false;
        }
      },
      parameters: parameters
    }
  );
}

function get_form_data(data) {
  var items = [];
  for (var x = 0; x < data.length; x++) {
    var item = data[x];
    if (item.type == 'checkbox') {
      if (item.checked) {
        items.push(item);
      }
    }
    else {
      items.push(item);
    }
  }
  return items;
}

function setNewAddress(isNew, type) {
  var element = $(type + '-new-address-form');
  if (isNew) {
    element.show();
    save_address_information(save_address_url);
  }
  else {
    element.hide();
    save_address_information(save_address_url);
  }
}

function myPopupRelocate(element_id) {
  var scrolledX, scrolledY;
  if (self.pageYOffset) {
    scrolledX = self.pageXOffset;
    scrolledY = self.pageYOffset;
  } else if (document.documentElement && document.documentElement.scrollTop) {
    scrolledX = document.documentElement.scrollLeft;
    scrolledY = document.documentElement.scrollTop;
  } else if (document.body) {
    scrolledX = document.body.scrollLeft;
    scrolledY = document.body.scrollTop;
  }

  var centerX, centerY;
  if (self.innerHeight) {
    centerX = self.innerWidth;
    centerY = self.innerHeight;
  } else if (document.documentElement && document.documentElement.clientHeight) {
    centerX = document.documentElement.clientWidth;
    centerY = document.documentElement.clientHeight;
  } else if (document.body) {
    centerX = document.body.clientWidth;
    centerY = document.body.clientHeight;
  }

  var leftOffset = scrolledX + (centerX - 250) / 2;
  var topOffset = scrolledY + (centerY - 200) / 2;

  document.getElementById(element_id).style.top = topOffset + "px";
  document.getElementById(element_id).style.left = leftOffset + "px";
}

function fireMyPopup(element_id) {
  myPopupRelocate(element_id);
  document.getElementById(element_id).style.display = "block";
  document.body.onscroll = myPopupRelocate(element_id);
  window.onscroll = myPopupRelocate(element_id);
}

function close_popup(element) {
  $(element).hide();
}

function validate_payment() {
  var methods = document.getElementsByName('payment[method]');
  if (methods.length == 0) {
    alert(Translator.translate('Your order cannot be completed at this time as there is no payment methods available for it.'));
    return false;
  }
  for (var i = 0; i < methods.length; i++) {
    if (methods[i].checked) {
      return true;
    }
  }
  alert(Translator.translate('Please specify payment method.'));
  return false;
}

function validate_shipping() {
  if (!showShippingMethod) {
    return true;
  }
  var methods = document.getElementsByName('shipping_method');
  if (methods.length == 0) {
    alert(Translator.translate('Your order cannot be completed at this time as there is no shipping methods available for it. Please make necessary changes in your shipping address.'));
    return false;
  }
  for (var i = 0; i < methods.length; i++) {
    if (methods[i].checked) {
      return true;
    }
  }
  alert(Translator.translate('Please specify shipping method.'));
  return false;
}

function login_user() {
  var parameters = {
    username: $('mini-username').value,
    password: $('mini-password').value
  };
  //$('login-field-load').update('<div id="login-form-ajax">&nbsp;</div>');
  show_loading(true);
  var request = new Ajax.Request(
    login_user_url,
    {
      method: 'post',
      onSuccess: function (transport) {
        var data = transport.responseText.evalJSON();
        if (data.error) {
          //$('login-field-load').update(data.html);
          show_loading(false);
          $('login-result-message').update(data.message);
        }
        else {
          window.location.reload(false);
        }
      },
      parameters: parameters
    }
  );
}

function show_loading(is_show) {
  if (is_show) {
    $('mini-login-form').hide();
    $('login-form-ajax').show();
  }
  else {
    $('mini-login-form').show();
    $('login-form-ajax').hide();
  }
}


// Xboy update Add Coupon code

function save_couponcode_method(url_update, coupon_code, cancel) {

  var order_review = $('checkout-review-load');
  if (order_review) {
    order_review.update('<div class="ajax-loader" id="ajax-loader">&nbsp;</div>');
  }

  var parameters = {
    coupon_code: coupon_code,
    cancel_code: cancel
  }

  //disable checkout button on reloading
  $('simplecheckout-button-place-order').disabled = true;

  var request = new Ajax.Request(
    url_update,
    {
      method: 'post',
      onSuccess: function (transport) {
        if (transport.status == 200) {
          var data = transport.responseText.evalJSON();
          if (data.error) {
            showMessageForCouponcode(data.message, status = 'error');
          }
          if (data.success) {
            showMessageForCouponcode(data.message, status = 'success');
          }          
          
          if (data.cancel) {
            $('cancel_couponcode').show();
            $('apply_couponcode').hide();
          } else {
            $('cancel_couponcode').hide();
            $('apply_couponcode').show();
            $('coupon_code').value = '';
          }
          order_review.update(data.order_review);
          $('simplecheckout-button-place-order').disabled = false;
        }
      },
      parameters: parameters
    }
  );
}

function showMessageForCouponcode(message, status) {
  if (status == 'success') {
    $('coupon_code_message').addClassName('success_message');
    $('coupon_code_message').removeClassName('error_message');

  }
  if (status == 'error') {
    $('coupon_code_message').addClassName('error_message');
    $('coupon_code_message').removeClassName('success_message');
  }
  $('coupon_code_message').update(message);
  //window.setTimeout($('coupon_code_message').update(''), 5000);
  window.setTimeout(function () {
    $('coupon_code_message').update('')
  }, 5000);
}

function use_point_and_update() {
  var order_review = $('checkout-review-load');
  var ajax = new Ajax.Request(
    use_point_url,
    {
      parameters: {
        'number_of_point': $('number_of_point').value
      },

      onCreate: function () {
        order_review.update('<div class="ajax-loader" id="ajax-loader">&nbsp;</div>');
      },

      onSuccess: function (transport) {
        var data = transport.responseText.evalJSON();
        order_review.update(data.order_review);
      }
    }
  )
}

//ajax for quantity in simplecheckout
function qtyUp(itemId, upqty_url){
  var qty = parseInt($('soc-qty-'+itemId).getValue()) + 1;
  if($('ajax_loading_updateqty').hasClassName('simplecheckout_hidden')){
    $('ajax_loading_updateqty').removeClassName('simplecheckout_hidden');
  }
  updateQty(itemId, upqty_url, qty);
}

function qtyDown(itemId, upqty_url){
  var qty = parseInt($('soc-qty-'+itemId).getValue()) - 1;
  if($('ajax_loading_updateqty').hasClassName('simplecheckout_hidden')){
      $('ajax_loading_updateqty').removeClassName('simplecheckout_hidden');
  }
  if(qty > 0){
    updateQty(itemId, upqty_url, qty);
  }
  else{
    $('ajax_loading_updateqty').update('<span class="message_qty_is_0">Qty is not 0!</span>');
    setInterval(function(){
      $('ajax_loading_updateqty').addClassName('simplecheckout_hidden');
    }, 6000);
  }
}

function updateQty(itemId, upqty_url, qty){
  var ajax = new Ajax.Request(
    upqty_url,
    {
      method: 'post',
      
      parameters: {
        'itemId': itemId,
        'qty': qty
      },

      onCreate: function () {
        $('ajax_loading_updateqty').update('<span class="ajax-loader" id="ajax-loader">&nbsp;</span>');
      },

      onSuccess: function (transport) {
        $('ajax_loading_updateqty').update();
        if(transport.responseJSON.message == true){
          $('soc-qty-'+itemId).setValue(transport.responseJSON.qty);
          $('simplecheckout_subtotal_'+itemId).update(transport.responseJSON.subtotal);
          $('simplecheckout_totals').update(transport.responseJSON.totals);
        }
        else{
          $('ajax_loading_updateqty').update('<span>Have an error when update qty!</span>');
          setInterval(function(){
          $('ajax_loading_updateqty').addClassName('simplecheckout_hidden');
          }, 6000);
        }
      }
    }
  )
}
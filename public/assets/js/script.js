$(document).ready(function () {
  let ccNumberInput = $(".card-number"),
    ccNumberPattern = /^\d{0,16}$/g,
    ccNumberSeparator = " ",
    ccNumberInputOldValue,
    ccNumberInputOldCursor,
    ccExpiryInput = $(".expiry-date"),
    ccExpiryPattern = /^\d{0,4}$/g,
    ccExpirySeparator = "/",
    ccExpiryInputOldValue,
    ccCVCInput = $(".cvc"),
    ccCVCPattern = /^\d{0,3}$/g,
    ccCVCInputOldValue,
    ccCVCInputOldCursor,
    mask = (value, limit, separator) => {
      var output = [];
      for (let i = 0; i < value.length; i++) {
        if (i !== 0 && i % limit === 0) {
          output.push(separator);
        }
        output.push(value[i]);
      }
      return output.join("");
    },
    unmask = (value) => value.replace(/[^\d]/g, ""),
    checkSeparator = (position, interval) =>
      Math.floor(position / (interval + 1)),
    ccNumberInputKeyDownHandler = (e) => {
      let el = $(e.target);
      ccNumberInputOldValue = el.val();
      ccNumberInputOldCursor = el[0].selectionEnd;
    },
    ccNumberInputInputHandler = (e) => {
      let el = $(e.target),
        newValue = unmask(el.val()),
        newCursorPosition;

      if (newValue.match(ccNumberPattern)) {
        newValue = mask(newValue, 4, ccNumberSeparator);

        newCursorPosition =
          ccNumberInputOldCursor -
          checkSeparator(ccNumberInputOldCursor, 4) +
          checkSeparator(
            ccNumberInputOldCursor +
              (newValue.length - ccNumberInputOldValue.length),
            4
          ) +
          (unmask(newValue).length - unmask(ccNumberInputOldValue).length);
        el.val(newValue !== "" ? newValue : "");
      } else {
        el.val(ccNumberInputOldValue);
        newCursorPosition = ccNumberInputOldCursor;
      }

      el[0].setSelectionRange(newCursorPosition, newCursorPosition);
      highlightCC(el.val());
    },
    highlightCC = (ccValue) => {
      let ccCardType = "",
        ccCardTypePatterns = {
          amex: /^3/,
          visa: /^4/,
          mastercard: /^5/,
          disc: /^6/,
          genric: /(^1|^2|^7|^8|^9|^0)/,
        };

      for (const cardType in ccCardTypePatterns) {
        if (ccCardTypePatterns[cardType].test(ccValue)) {
          ccCardType = cardType;
          if (cardType != "visa" && cardType != "mastercard") {
            $.toast({
              title: "Warning",
              message: "Please input VISA/MASTER CARD.",
              type: "warning",
              duration: 2000,
            });
          }
          if (cardType == "mastercard") {
            $(".mastercard").css("display", "block");
            $(".visa").css("display", "none");
          } else {
            $(".mastercard").css("display", "none");
            $(".visa").css("display", "block");
          }
          break;
        }
      }
    },
    ccExpiryInputKeyDownHandler = (e) => {
      let el = $(e.target);
      ccExpiryInputOldValue = el.val();
      ccExpiryInputOldCursor = el[0].selectionEnd;
    },
    ccExpiryInputInputHandler = (e) => {
      let el = $(e.target),
        newValue = el.val();

      newValue = unmask(newValue);
      if (newValue.match(ccExpiryPattern)) {
        newValue = mask(newValue, 2, ccExpirySeparator);
        el.val(newValue);
      } else {
        el.val(ccExpiryInputOldValue);
      }
    },
    ccCVCInputKeyDownHandler = (e) => {
      let el = $(e.target);
      ccCVCInputOldValue = el.val();
      ccCVCInputOldCursor = el[0].selectionEnd;
    },
    ccCVCInputInputHandler = (e) => {
      let el = $(e.target),
        newValue = unmask(el.val()),
        newCursorPosition;

      if (newValue.match(ccCVCPattern)) {
        el.val(newValue);
        newCursorPosition = ccCVCInputOldCursor + 1;
      } else {
        el.val(ccCVCInputOldValue);
        newCursorPosition = ccCVCInputOldCursor;
      }
      el[0].setSelectionRange(newCursorPosition, newCursorPosition);
    };

  ccNumberInput.on("keydown", ccNumberInputKeyDownHandler);
  ccNumberInput.on("input", ccNumberInputInputHandler);

  ccExpiryInput.on("keydown", ccExpiryInputKeyDownHandler);
  ccExpiryInput.on("input", ccExpiryInputInputHandler);

  ccCVCInput.on("keydown", ccCVCInputKeyDownHandler);
  ccCVCInput.on("input", ccCVCInputInputHandler);
});

function maskCardNumber(value) {
  const strNumber = value.toString();
  const maskedPart = strNumber.slice(0, 15).replace(/\d/g, "•");
  const unmaskedPart = strNumber.slice(15);
  return maskedPart + unmaskedPart;
}

$(".card-holder").on("input", function () {
  $(".c-holder").html($(this).val());
});

$(".card-number").on("keyup", function () {
  let cNum = maskCardNumber($(".card-number").val());
  $(".c-number").html(cNum);
});

$(".btn-pay").click(function () {
  const cardNumber = $("input[name=card_number]").val();
  const cardHolder = $("input[name=card_holder]").val();
  const expiryDate = $("input[name=expiry_date]").val();
  const cvc = $("input[name=cvc]").val();
  if (!cardNumber || !cardHolder || !expiryDate || !cvc) {
    $.toast({
      title: "Warning",
      message: "Please input all infos.",
      type: "warning",
      duration: 2000,
    });
    return;
  }
  const data = {
    cart_id: cartId,
    card_number: cardNumber,
    card_holder: cardHolder,
    expiry_date: expiryDate,
    cvc: cvc,
  };

  $.ajax({
    method: "POST",
    url: "/checkout/checkout_process.php",
    data: JSON.stringify(data),
    dataType: "json",
    contentType: "application/json",
    success: function (res) {
      if (res.statusCode == 201) {
        alert(res.response_message);
        window.location.href = `${res.reservationData.properties.feedback_url}&mode=payment_confirmation&reservation_id=${res.reservationId}&planyo_lang=en&ppp_user_id=${res.reservationData.user_id}&ppp_rs=${res.reservationData.ppp_rs}`;
      } else {
        alert(res.message);
      }
    },
    error: function (err) {
      alert("Something went wrong.");
    },
  });
});

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <title>Credit Card Checkout</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/styles.css">
  </head>
  <body>
    <div class='container'>
      <div class="logo-container">
          <img src="/assets/images/logo.png" />
      </div>
      <!-- <hr class="thicker"> -->
      <hr>
      <div class="checkout-form-container">
          <div class="form-container">
              <div class="form-grid">
                  <div class="form-input">
                      <label>HOLDER NAME</label>
                      <input name="card_holder" class="card-holder" value="{!!  trim($data['data']['items'][0]['first_name'] ). ' ' . trim($data['data']['items'][0]['last_name']) !!}"/>
                  </div>
                  <div class="form-input">
                      <label>EXPIRRATION DATE</label>
                      <input name="expiry_date" class="expiry-date"/>
                  </div>
                  <div class="form-input">
                      <label>CARD NUMBER</label>
                      <input name="card_number" class="card-number"/>
                  </div>
                  <div class="form-input">
                      <label>CVC</label>
                      <input name="cvc" class="cvc"/>
                  </div>
              </div>
              <div class="order-summary-container">
                  <div class="order-summary-title-container">
                      <h3>ORDER SUMMARY</h3>
                      <h3>TOTAL</h3>
                  </div>
                  <div class="order-summar-content-container">
                      @foreach($data["data"]["items"] as $item)
                      <div class="item">
                          <h5>
                              {!! $item['name'] !!} X {!! $item['quantity'] !!}
                          </h5>
                      </div>
                      @endforeach
                      <div class="total">
                          <h4>
                              {!! htmlspecialchars($data['data']['items'][0]['currency'] ?? 'SGD') !!} {!! htmlspecialchars($items_total ?? '0.00') !!}
                          </h4>
                      </div>
                  </div>
              </div>
          </div>
          <div class="card-container">
              <div class="card-content-container">
                  <div class="card-item">
                      <label>CARD TYPE</label>
                      <div class="mastercard">
                          <?xml version="1.0" encoding="utf-8"?>
                              <svg width="90px" height="90px" viewBox="0 -140 780 780" enable-background="new 0 0 780 500" version="1.1" xml:space="preserve" xmlns="http://www.w3.org/2000/svg"><path d="m736.04 0h-694.58c-22.887 0-41.458 18.975-41.458 42.383v414.23c0 23.413 18.562 42.384 41.458 42.384h694.58c22.889 0 41.459-18.976 41.459-42.384v-414.23c0-23.412-18.562-42.383-41.459-42.383zm-217.94 465.4c-48.683 0-93.562-16.89-129.37-45.135-35.782 28.246-80.662 45.135-129.35 45.135-116.72 0-211.68-96.879-211.68-215.92 0-119.05 94.959-215.88 211.68-215.88 48.686 0 93.564 16.827 129.35 45.113 35.804-28.286 80.683-45.113 129.37-45.113 116.72 0 211.68 96.834 211.68 215.88-1e-3 119.04-94.966 215.92-211.68 215.92z" fill="#ffffff"/><path d="m218.07 263.3c-2.088-0.219-2.998-0.303-4.431-0.303-11.265 0-16.94 3.942-16.94 11.709 0 4.81 2.785 7.871 7.089 7.871 8.102 0 13.922-7.871 14.282-19.277z" fill="#ffffff"/><path d="m549 263.3c-2.067-0.219-2.994-0.303-4.452-0.303-11.244 0-16.939 3.942-16.939 11.709 0 4.81 2.786 7.871 7.134 7.871 8.079 0 13.922-7.871 14.257-19.277z" fill="#ffffff"/><path d="m379.67 250.16c0.127-1.596 2.087-13.805-9.177-13.805-6.286 0-10.799 4.939-12.611 13.805h21.788z" fill="#ffffff"/><path d="m645.93 279.85c9.238 0 15.758-10.722 15.758-25.987 0-9.812-3.689-15.118-10.53-15.118-9.008 0-15.399 10.718-15.399 25.879-1e-3 10.112 3.421 15.226 10.171 15.226z" fill="#ffffff"/><path d="m517.44 52.958c-42.883 0-82.473 14.363-114.46 38.6 29.009 27.599 50.462 63.438 60.712 103.83h-19.592c-10.039-35.707-29.662-67.233-55.864-91.495-26.173 24.262-45.811 55.787-55.811 91.495h-19.623c10.274-40.389 31.727-76.228 60.736-103.83-32.002-24.237-71.578-38.6-114.48-38.6-106.3 0-192.46 88.086-192.46 196.77 0 108.66 86.169 196.77 192.46 196.77 42.904 0 82.479-14.363 114.48-38.6-27.296-25.987-47.887-59.282-58.773-96.759h19.812c10.525 32.815 29.21 61.781 53.658 84.424 24.475-22.643 43.185-51.608 53.711-84.424h19.806c-10.903 37.479-31.491 70.771-58.772 96.759 31.983 24.236 71.573 38.6 114.46 38.6 106.29 0 192.46-88.114 192.46-196.77 0-108.69-86.171-196.77-192.46-196.77zm-371.49 244.71l11.371-72.89-25.376 72.89h-13.542l-1.667-72.457-11.937 72.457h-18.587l15.502-94.839h28.561l0.802 58.698 19.261-58.698h30.82l-15.358 94.839h-19.85zm92.476-40.927c-1.729 11.146-5.422 35.082-5.929 40.927h-16.454l0.383-8c-5.023 6.317-11.71 9.34-20.798 9.34-10.781 0-18.12-8.604-18.12-21.049 0-18.806 12.801-29.737 34.824-29.737 2.257 0 5.146 0.215 8.1 0.603 0.613-2.566 0.761-3.644 0.761-5.025 0-5.088-3.441-7.007-12.722-7.007-9.701-0.13-17.718 2.351-21.009 3.472 0.213-1.293 2.764-17.338 2.764-17.338 9.875-2.975 16.41-4.097 23.75-4.097 17.046 0 26.074 7.806 26.053 22.6 0.021 3.966-0.612 8.861-1.603 15.311zm53.768-18.504c-5.021-0.733-10.357-1.167-14.237-1.167-6.433 0-9.745 2.115-9.745 6.298 0 3.601 0.971 4.464 9.279 8.388 9.958 4.683 13.988 10.85 13.988 21.479 0 17.596-9.663 25.771-30.608 25.771-12.13-0.35-16.137-1.293-20.672-2.285l2.741-17.813c6.351 2.109 11.878 3.017 17.784 3.017 7.867 0 11.412-2.156 11.412-6.94 0-3.52-1.245-4.639-9.282-8.52-10.507-5.068-15.104-11.773-15.104-21.543v-1e-3c-0.085-14.254 7.593-26.093 29.804-26.093 4.537 0 12.32 0.69 17.468 1.51l-2.828 17.899zm41.494 0.861h-10.184c-2.28 14.644-5.55 32.887-5.593 35.344 0 3.99 2.088 5.713 6.812 5.713 2.258 0 4.007-0.256 5.336-0.731l-2.611 17.082c-5.45 1.722-9.684 2.502-14.286 2.502-10.144 0-15.673-5.974-15.673-16.954-0.124-3.405 1.478-12.353 2.744-20.57 1.118-7.182 8.583-52.571 8.583-52.571h19.726l-2.298 11.64h10.146l-2.702 18.545zm61.975 26.634h-39.279c-1.329 11.146 5.677 15.806 17.154 15.806 7.063 0 13.416-1.468 20.501-4.83l-3.269 19.192c-6.792 2.116-13.335 3.107-20.25 3.107-22.111-0.048-33.624-11.84-33.624-34.418 0-26.354 14.659-45.762 34.55-45.762 16.264 0 26.667 10.847 26.667 27.926 0 5.651-0.737 11.15-2.45 18.979zm44.046-23.271c-11.116-1.164-12.823 8.045-19.934 55.204h-19.85l0.909-5.193c3.438-23.896 7.866-48.069 10.359-71.921h18.222c0.165 3.925-0.696 7.743-1.183 11.711 6.06-9.121 10.737-13.953 19.03-12.182-2.449 4.27-5.76 12.699-7.553 22.381zm59.591 53.742c-7.299 2.068-12.045 2.805-17.528 2.805-21.366 0-34.684-15.744-34.684-40.929 0-33.903 18.396-57.579 44.703-57.579 8.667 0 18.899 3.753 21.81 4.916l-3.251 20.572c-7.087-3.667-12.233-5.135-17.74-5.135-14.825 0-25.189 14.645-25.189 35.538 0 14.386 6.964 23.098 18.501 23.098 4.902 0 10.299-1.57 16.859-4.872l-3.481 21.586zm70.054-39.462c-1.708 11.146-5.416 35.082-5.927 40.927h-16.43l0.379-8c-5.042 6.317-11.752 9.34-20.824 9.34-10.757 0-18.143-8.604-18.143-21.049 0-18.806 12.849-29.737 34.85-29.737 2.258 0 5.148 0.215 8.104 0.603 0.605-2.566 0.757-3.644 0.757-5.025 0-5.088-3.438-7.007-12.701-7.007-9.722-0.13-17.715 2.351-21.009 3.472 0.189-1.293 2.744-17.338 2.744-17.338 9.892-2.975 16.43-4.097 23.729-4.097 17.065 0 26.098 7.806 26.073 22.6 0.043 3.966-0.609 8.861-1.602 15.311zm23.923 40.924h-19.831l0.909-5.193c3.438-23.896 7.848-48.069 10.336-71.921h18.227c0.189 3.925-0.677 7.743-1.16 11.711 6.052-9.121 10.696-13.953 19.022-12.182-2.487 4.27-5.78 12.699-7.551 22.381-11.115-1.164-12.842 8.045-19.952 55.204zm63.158 0l0.946-7.178c-5.465 5.933-11.052 8.521-18.309 8.521-14.432 0-23.964-12.704-23.964-31.981 0-25.688 14.782-47.294 32.29-47.294 7.683 0 13.544 3.192 18.97 10.503l4.386-27.408h19.58l-15.11 94.837h-18.789z" fill="#ffffff"/></svg>
                      </div>
                      <div class="visa">
                          <?xml version="1.0" encoding="utf-8"?>
                              <svg width="90px" height="90px" viewBox="0 -140 780 780" enable-background="new 0 0 780 500" version="1.1" xml:space="preserve" xmlns="http://www.w3.org/2000/svg"><path d="m736.04 0h-694.58c-22.887 0-41.458 18.994-41.458 42.426v414.65c0 23.437 18.562 42.426 41.458 42.426h694.58c22.888 0 41.459-18.994 41.459-42.426v-414.65c0-23.436-18.562-42.426-41.459-42.426zm-581.62 353.64l-49.177-180.32c-17.004-9.645-36.407-17.397-58.104-22.77l0.706-4.319h89.196c12.015 0.457 21.727 4.38 25.075 17.527l19.392 95.393 4e-3 0.011 5.77 28.77 54.155-141.57h58.594l-87.085 207.2-58.526 0.07zm188.7 0.177h-55.291l-1e-3 -1e-3 34.585-207.61h55.315l-34.608 207.61zm96.259 3.08c-24.807-0.26-48.697-5.28-61.618-11.075l7.764-46.475 7.126 3.299c18.167 7.751 29.929 10.897 52.068 10.897 15.899 0 32.957-6.357 33.094-20.272 0.103-9.088-7.136-15.577-28.666-25.753-20.982-9.932-48.777-26.572-48.47-56.403 0.328-40.355 38.829-68.514 93.487-68.514 21.445 0 38.618 4.514 49.577 8.72l-7.498 44.998-4.958-2.397c-10.209-4.205-23.312-8.24-41.399-7.954-21.655 0-31.678 9.229-31.678 17.858-0.126 9.724 11.715 16.134 31.05 25.736 31.913 14.818 46.65 32.791 46.44 56.407-0.428 43.094-38.174 70.928-96.319 70.928zm239.65-3.014s-5.074-23.841-6.729-31.108c-8.067 0-64.494-0.09-70.842-0.09-2.147 5.615-11.646 31.198-11.646 31.198h-58.086l82.151-190.26c5.815-13.519 15.724-17.216 28.967-17.216h42.742l44.772 207.48h-51.329z" fill="#ffffff"/><path d="m617.38 280.22c4.574-11.963 22.038-58.036 22.038-58.036-0.327 0.554 4.54-12.019 7.333-19.813l3.741 17.898s10.59 49.557 12.804 59.949h-45.917l1e-3 2e-3z" fill="#ffffff"/></svg>
                      </div>
                  </div>
                  <div class="card-item">
                      <label>HOLDER NAME</label>
                      <h4 class="c-holder">{!! trim($data['data']['items'][0]['first_name'] ). ' ' . trim($data['data']['items'][0]['last_name']) !!}</h4>
                  </div>
                  <div class="card-item">
                      <label>CARD NUMBER</label>
                      <h4 class="c-number"></h4>
                  </div>
                  <button class="btn-pay" data-flag="true">
                      COMPLETE ORDER
                  </button>
                  <div style="width: 100%">
                  <p>You can edit reservation with 24 hrs</p>
                  <button class="btn-pay" data-flag="false">
                      COMPLETE ORDER AFTER 24 HRS
                  </button>
                  </div>
              </div>
          </div>
      </div>
  </div>

    <script type="text/javascript" src="/assets/js/jquery-3.7.1.min.js"></script>
    <script type="text/javascript" src="/assets/js/toast-plugin-min.js"></script>
    <script type="text/javascript">
      const cartId = {!! $card_id !!};
    </script>
    <script type="text/javascript" src="/assets/js/script.js"></script>
  </body>
</html>
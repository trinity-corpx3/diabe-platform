<!DOCTYPE html>
<html data-report-errors="{{ $report_errors }}" data-rc="{{ $rc }}" data-user-agent="{{ $user_agent }}"
  data-login="{{ $login }}" data-signup="{{ $signup }}" data-white-label="{{ $white_label }}"
  data-microsoft-client-id="{{ config('services.microsoft.client_id') }}">

<head>
  <!-- Source: https://github.com/invoiceninja/invoiceninja -->
  <!-- Version: {{ config('ninja.app_version') }} -->
  <meta charset="UTF-8">
  <title>{{ $white_label ? "" : config('ninja.app_name')  }}</title>
  <meta name="google-signin-client_id" content="{{ config('services.google.client_id') }}">
  <link rel="manifest" href="manifest.json?v={{ config('ninja.app_version') }}">
  <script src="{{ asset('js/pdf.min.js') }}"></script>

  @if(config('services.microsoft.client_id'))
    <!-- Microsoft OAuth library -->
    <script type="text/javascript" src="https://alcdn.msauth.net/browser/2.14.2/js/msal-browser.min.js"
      integrity="sha384-ggh+EF1aSqm+Y4yvv2n17KpurNcZTeYUZUvhPziElsstmIEubyEB6AIVpKLuZgr" crossorigin="anonymous">
      </script>
  @endif

  @if(\App\Utils\Ninja::isHosted())

    <!-- Apple OAuth Library -->
    <script type="text/javascript"
      src="https://appleid.cdn-apple.com/appleauth/static/jsapi/appleid/1/en_US/appleid.auth.js"></script>


    <!-- G Tag Manager -->
    <script>(function (w, d, s, l, i) {
        w[l] = w[l] || []; w[l].push({
          'gtm.start':
            new Date().getTime(), event: 'gtm.js'
        }); var f = d.getElementsByTagName(s)[0],
          j = d.createElement(s), dl = l != 'dataLayer' ? '&l=' + l : ''; j.async = true; j.src =
            'https://www.googletagmanager.com/gtm.js?id=' + i + dl; f.parentNode.insertBefore(j, f);
      })(window, document, 'script', 'dataLayer', 'GTM-WMJ5W23');</script>
    <!-- End G Tag Manager -->

  @endif
  <script type="text/javascript">
    pdfjsLib.GlobalWorkerOptions.workerSrc = "{{ asset('js/pdf.worker.min.js') }}";
  </script>
  <script>
    window.flutterConfiguration = {
      @if(!\App\Utils\Ninja::isHosted())
        canvasKitBaseUrl: "{{ $canvas_path }}/canvaskit/"
      @endif
    };
  </script>
</head>

<body style="background-color:#888888;">

  @if(\App\Utils\Ninja::isHosted())
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-WMJ5W23" height="0" width="0"
        style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
  @endif

  <style>
    /* fix for blurry fonts 
    flt-glass-pane {
        image-rendering: pixelated;
    }
    */

    /* https://projects.lukehaas.me/css-loaders/ */
    .loader,
    .loader:before,
    .loader:after {
      border-radius: 50%;
      width: 2.5em;
      height: 2.5em;
      -webkit-animation-fill-mode: both;
      animation-fill-mode: both;
      -webkit-animation: load7 1.8s infinite ease-in-out;
      animation: load7 1.8s infinite ease-in-out;
    }

    .loader {
      color: #ffffff;
      font-size: 10px;
      margin: 80px auto;
      position: relative;
      text-indent: -9999em;
      -webkit-transform: translateZ(0);
      -ms-transform: translateZ(0);
      transform: translateZ(0);
      -webkit-animation-delay: -0.40s;
      animation-delay: -0.40s;
    }

    .loader:before,
    .loader:after {
      content: '';
      position: absolute;
      top: 0;
    }

    .loader:before {
      left: -3.5em;
      -webkit-animation-delay: -0.80s;
      animation-delay: -0.80s;
    }

    .loader:after {
      left: 3.5em;
    }

    @-webkit-keyframes load7 {

      0%,
      80%,
      100% {
        box-shadow: 0 2.5em 0 -1.3em;
      }

      40% {
        box-shadow: 0 2.5em 0 0;
      }
    }

    @keyframes load7 {

      0%,
      80%,
      100% {
        box-shadow: 0 2.5em 0 -1.3em;
      }

      40% {
        box-shadow: 0 2.5em 0 0;
      }
    }
  </style>

  <script>
    @if (request()->clear_local)
      window.onload = function () {
        window.localStorage.clear();
      }
    @endif
    
    if ('serviceWorker' in navigator) {
      window.addEventListener('load', function () {
        navigator.serviceWorker.register('flutter_service_worker.js?v={{ config('ninja.app_version') }}');
      });
    }

    document.addEventListener('DOMContentLoaded', function (event) {
      document.getElementById('loader').style.display = 'none';
    });


    function invokeServiceWorkerUpdateFlow() {
      // you have a better UI here, reloading is not a great user experince here.
      const confirmed = alert('New version of the app is available. Refresh now');
      if (confirmed == true) {
        window.location.reload();
      }
    }
    async function handleServiceWorker() {
      if ('serviceWorker' in navigator) {
        // get the ServiceWorkerRegistration instance
        const registration = await navigator.serviceWorker.getRegistration();
        // (it is also returned from navigator.serviceWorker.register() function)

        if (registration) {
          // detect Service Worker update available and wait for it to become installed
          registration.addEventListener('updatefound', () => {
            if (registration.installing) {
              // wait until the new Service worker is actually installed (ready to take over)
              registration.installing.addEventListener('statechange', () => {
                if (registration.waiting) {
                  // if there's an existing controller (previous Service Worker), show the prompt
                  if (navigator.serviceWorker.controller) {
                    invokeServiceWorkerUpdateFlow(registration);
                  } else {
                    // otherwise it's the first install, nothing to do
                    console.log('Service Worker initialized for the first time');
                  }
                }
              });
            }
          });

          let refreshing = false;

          // detect controller change and refresh the page
          navigator.serviceWorker.addEventListener('controllerchange', () => {
            if (!refreshing) {
              window.location.reload();
              refreshing = true;
            }
          });
        }
      }
    }

    handleServiceWorker();

  </script>

  <script defer src="{{ $path }}?v={{ config('ninja.app_version') }}" type="application/javascript"></script>

  <center style="padding-top: 150px" id="loader">
    <div class="loader"></div>
  </center>


  <style>
    @import url('https://fonts.cdnfonts.com/css/sf-pro-display');

    /* Hide Invoice Ninja Branding - BUT NOT DIABE logos */
    img[src*="invoiceninja"]:not([src*="diabe"]),
    img[alt*="Invoice Ninja"]:not([src*="diabe"]),
    .invoiceninja-logo {
      display: none !important;
    }

    /* Hide 2FA and Secret Fields (Optional) */
    input[placeholder="(optional)"] {
      display: none !important;
    }

    /* Hide Labels for Optional Fields */
    label:has(+ div > input[placeholder="(optional)"]),
    label:has(+ input[placeholder="(optional)"]) {
      display: none !important;
    }
  </style>
  @php
      $companyLogo = '';
      try {
          $company = \App\Models\Company::whereNotNull('settings->company_logo')
              ->where('settings->company_logo', '!=', '')
              ->first() ?? \App\Models\Company::first();
          if ($company) {
              $logoUrl = $company->present()->logo();
              if ($logoUrl && !str_contains($logoUrl, 'diabe_logo.jpg')) {
                  $companyLogo = $logoUrl;
              }
          }
      } catch (\Exception $e) {}
  @endphp
  <script>
    window.__diabeLogoUrl = {!! json_encode($companyLogo ?: "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 200 60'%3E%3Ctext x='100' y='42' text-anchor='middle' font-family='SF Pro Display,-apple-system,sans-serif' font-size='36' font-weight='700' fill='%2325a70c'%3EDIABE%3C/text%3E%3C/svg%3E") !!};

    document.addEventListener('DOMContentLoaded', () => {
      // Continuous observer to handle dynamic Flutter/React rendering
      const observer = new MutationObserver(() => {
        // 1. Replace Invoice Ninja Logo with DIABE Logo Image
        const images = document.querySelectorAll('img');
        images.forEach(img => {
          const src = img.getAttribute('src') || '';
          const alt = img.getAttribute('alt') || '';

          // Skip if already a DIABE logo
          if (src.includes('diabe') || src.includes('storage')) return;

          // Check if it's an Invoice Ninja logo that needs replacing
          if (src.includes('logo') || src.includes('invoiceninja') || alt.toLowerCase().includes('invoice ninja')) {
            if (!img.dataset.replaced) {
              const parentRect = img.parentElement ? img.parentElement.getBoundingClientRect() : { width: 1000 };
              const isCompact = parentRect.width < 320;

              img.src = window.__diabeLogoUrl;
              img.alt = 'DIABE Platform';

              img.style.setProperty('max-height', isCompact ? '40px' : '80px', 'important');
              img.style.setProperty('width', 'auto', 'important');
              img.style.setProperty('object-fit', 'contain', 'important');
              img.style.setProperty('display', 'block', 'important');

              img.dataset.replaced = "true";
            }
          }
        });

        // 2. Hide "v Latest Build" and labels via Text Content
        // This is expensive but necessary for canvas/dynamic rendering sometimes
        const walker = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT);
        while (walker.nextNode()) {
          const node = walker.currentNode;
          const text = node.nodeValue.trim();

          if (text.includes('v Latest Build') || text.includes('2026-01-26')) {
            if (node.parentElement) node.parentElement.style.display = 'none';
          }

          // Hide 2FA labels
          if (text === '2FA - One Time Password' || text === 'Secret') {
            // Try to find the container to hide
            if (node.parentElement) {
              // Usually these are labels or spans above inputs
              node.parentElement.style.display = 'none';
              // Try to hide the following input if possible (best effort)
              const next = node.parentElement.nextElementSibling;
              if (next && (next.tagName === 'INPUT' || next.querySelector('input'))) {
                next.style.display = 'none';
              }
            }
          }
        }

        // 3. Fallback for inputs if labels weren't text nodes
        const optionalInputs = document.querySelectorAll('input[placeholder="(optional)"]');
        optionalInputs.forEach(input => {
          input.style.display = 'none';
          // Hide parent container if it looks like a wrapper
          if (input.parentElement && input.parentElement.classList.contains('input-group')) {
            input.parentElement.style.display = 'none';
          }
          // Hide previous sibling if it's a label (and not handled above)
          let prev = input.previousElementSibling;
          if (prev && (prev.tagName === 'LABEL' || prev.innerText.includes('2FA') || prev.innerText.includes('Secret'))) {
            prev.style.display = 'none';
          }
          // If parent is a label wrapper
          if (input.parentElement && input.parentElement.tagName === 'LABEL') {
            input.parentElement.style.display = 'none';
          }
        });

      });

      observer.observe(document.body, { childList: true, subtree: true });
    });
  </script>
</body>

</html>
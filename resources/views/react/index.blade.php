<!DOCTYPE html>
<html data-report-errors="{{ $report_errors }}" data-rc="{{ $rc }}" data-user-agent="{{ $user_agent }}"
  data-login="{{ $login }}">

<head>
  <!-- Source: https://github.com/invoiceninja/invoiceninja -->
  <!-- Version: {{ config('ninja.app_version') }} -->
  <meta charset="UTF-8">
  <title>{{ config('ninja.app_name') }}</title>
  <meta name="google-signin-client_id" content="{{ config('services.google.client_id') }}">

  @include('react.head')

</head>
<style>
  @import url('https://fonts.cdnfonts.com/css/sf-pro-display');

  /* Hide Invoice Ninja Branding */
  img[src*="logo"],
  img[alt*="Invoice Ninja"] {
    display: none !important;
  }

  /* Hide 2FA and Secret Fields (Optional) */
  input[placeholder="(optional)"] {
    display: none !important;
  }

  /* Hide Labels for Optional Fields (Chrome/Edge/Safari/Firefox) */
  label:has(+ div > input[placeholder="(optional)"]),
  label:has(+ input[placeholder="(optional)"]) {
    display: none !important;
  }
</style>
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const observer = new MutationObserver(() => {
      // 1. Replace Logo with DIABE Platform
      const images = document.querySelectorAll('img');
      images.forEach(img => {
        const src = img.getAttribute('src');
        if (src && (src.includes('logo') || img.alt?.includes('Invoice Ninja'))) {
          if (!img.dataset.replaced) {
            const title = document.createElement('div');
            title.innerText = "DIABE Platform";
            title.style.fontFamily = "'SF Pro Display', sans-serif";
            title.style.fontSize = "32px";
            title.style.fontWeight = "600";
            title.style.textAlign = "center";
            title.style.marginBottom = "24px";
            title.style.color = "#1f2937";

            img.parentNode.insertBefore(title, img);
            img.style.display = 'none';
            img.dataset.replaced = "true";
          }
        }
      });

      // 2. Hide "v Latest Build" and specific labels if CSS missed them
      const walker = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT);
      while (walker.nextNode()) {
        const node = walker.currentNode;
        if (node.nodeValue.includes('v Latest Build')) {
          node.parentElement.style.display = 'none';
        }
        if (node.nodeValue.includes('2FA - One Time Password') || node.nodeValue.includes('Secret')) {
          if (node.parentElement.tagName === 'LABEL') {
            node.parentElement.style.display = 'none';
          }
        }
      }
    });

    observer.observe(document.body, { childList: true, subtree: true });
  });
</script>

<body class="h-full">
  <noscript>You need to enable JavaScript to run this app.</noscript>
  <div id="root"></div>

</body>

<!--

If you are reading this, there is a fair change that the react application has not loaded for you. There are a couple of solutions:

1. Download the release file from https://github.com/invoiceninja/invoiceninja and overwrite your current installation.
2. Switch back to the Flutter application by editing the database, you can do this with the following SQL

UPDATE accounts SET
set_react_as_default_ap = 0;

-->

</html>
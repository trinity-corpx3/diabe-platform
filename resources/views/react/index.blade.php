<!DOCTYPE html>
<html data-report-errors="{{ $report_errors }}" data-rc="{{ $rc }}" data-user-agent="{{ $user_agent }}"
  data-login="{{ $login }}">

<head>
  <!-- Source: https://github.com/invoiceninja/invoiceninja -->
  <!-- Version: {{ config('ninja.app_version') }} -->
  <meta charset="UTF-8">
  <title>DIABE Platform</title>
  <meta name="google-signin-client_id" content="{{ config('services.google.client_id') }}">

  @include('react.head')

  <style>
    @import url('https://fonts.cdnfonts.com/css/sf-pro-display');

    /* Robust hiding of Invoice Ninja branding */
    img[src*="logo"],
    img[alt*="Invoice Ninja"],
    .invoiceninja-logo {
      opacity: 0 !important;
      /* Opacity 0 often better than display:none to prevent layout shift before replacement */
      width: 0 !important;
      height: 0 !important;
      pointer-events: none !important;
      position: absolute !important;
    }

    /* Hide 2FA and Secret Inputs & Labels */
    input[placeholder="(optional)"],
    label:has(+ div > input[placeholder="(optional)"]),
    label:has(+ input[placeholder="(optional)"]) {
      display: none !important;
    }

    /* Hide the specific text for v Latest Build if it appears in a specific container */
    /* This is tricky in CSS alone, we rely on JS */
  </style>
  <script>
    console.log('DIABE Platform Customization Loaded');
    document.addEventListener('DOMContentLoaded', () => {
      const observer = new MutationObserver(() => {
        // 1. Branding Replacement
        const images = document.querySelectorAll('img');
        images.forEach(img => {
          const src = img.getAttribute('src');
          const alt = img.getAttribute('alt');

          if ((src && (src.includes('logo') || src.includes('invoiceninja'))) ||
            (alt && alt.toLowerCase().includes('invoice ninja'))) {

            if (!img.dataset.replaced) {
              const container = document.createElement('div');
              container.innerText = "DIABE Platform";
              container.style.fontFamily = "'SF Pro Display', -apple-system, BlinkMacSystemFont, sans-serif";
              container.style.fontSize = "32px";
              container.style.fontWeight = "600";
              container.style.textAlign = "center";
              container.style.marginBottom = "24px";
              container.style.color = "#1f2937";
              container.style.width = "100%";
              container.className = "diabe-branding";

              if (img.parentNode) {
                img.parentNode.insertBefore(container, img);
                img.dataset.replaced = "true"; // Mark as handled
                // We use CSS to hide the original img, but let's reinforce
                img.style.display = 'none';
              }
            }
          }
        });

        // 2. Hide Content by Text (Version & 2FA Labels)
        const walker = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT);
        while (walker.nextNode()) {
          const node = walker.currentNode;
          const text = node.nodeValue.trim();

          // Hide Version Footer
          if (text.includes('v Latest Build') || text.includes('2026-')) {
            if (node.parentElement) node.parentElement.style.display = 'none';
          }

          // Hide 2FA Labels and associated inputs
          if (text === '2FA - One Time Password' || text === 'Secret') {
            if (node.parentElement) {
              node.parentElement.style.display = 'none';
              // Try to find the next input sibling
              let sibling = node.parentElement.nextElementSibling;
              if (sibling && (sibling.tagName === 'INPUT' || sibling.querySelector('input'))) {
                sibling.style.display = 'none';
              }
            }
          }
        }

        // 3. Fallback for Inputs
        const inputs = document.querySelectorAll('input[placeholder="(optional)"]');
        inputs.forEach(input => {
          input.style.display = 'none';
          if (input.previousElementSibling) input.previousElementSibling.style.display = 'none'; // Label
        });
      });

      observer.observe(document.body, { childList: true, subtree: true });
    });
  </script>

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
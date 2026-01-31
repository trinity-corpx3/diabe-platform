<!DOCTYPE html>
<html data-report-errors="{{ $report_errors }}" data-rc="{{ $rc }}" data-user-agent="{{ $user_agent }}" data-login="{{ $login }}">
<head>
  <!-- Source: https://github.com/invoiceninja/invoiceninja -->
  <!-- Version: {{ config('ninja.app_version') }} -->
  <meta charset="UTF-8">
  <title>DIABE Platform</title>
  <meta name="google-signin-client_id" content="{{ config('services.google.client_id') }}">

  @include('react.head')

  <!-- FontAwesome for the new Logo Icon -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

  <style>
      @import url('https://fonts.cdnfonts.com/css/sf-pro-display');

      /* Hide Invoice Ninja Branding */
      img[src*="logo"], 
      img[alt*="Invoice Ninja"],
      .invoiceninja-logo {
          display: none !important;
      }

      /* Hide 2FA and Secret Fields (Optional) */
      input[name="one_time_password"],
      input[name="secret"],
      input[placeholder="(optional)"] {
          display: none !important;
      }
      
      /* Hide Labels for Optional Fields */
      label:has(+ div > input[placeholder="(optional)"]),
      label:has(+ input[placeholder="(optional)"]) {
          display: none !important;
      }

      /* Hide stray eye/password-toggle icons */
      .input-group-text:has(+ input[placeholder="(optional)"]), 
      div:has(> input[placeholder="(optional)"]) + div,
      input[placeholder="(optional)"] ~ svg,
      input[placeholder="(optional)"] + div {
          display: none !important;
      }
      
      /* Extra safety for 2FA containers */
      div:has(> input[name="one_time_password"]),
      div:has(> input[name="secret"]) {
          display: none !important;
      }
  </style>

  <script>
      // Update Title dynamically
      document.title = "DIABE Platform";

      document.addEventListener('DOMContentLoaded', () => {
          // Attempt to change favicon dynamically
          try {
              let link = document.querySelector("link[rel*='icon']");
              if (!link) {
                  link = document.createElement('link');
                  link.rel = 'icon';
                  document.head.appendChild(link);
              }
              link.type = 'image/svg+xml';
              link.href = 'data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><rect width="100" height="100" rx="20" fill="%2325a70c"/></svg>';
          } catch(e) { console.log('Favicon update failed', e); }

          const observer = new MutationObserver(() => {
              // 1. Replace Logo with DIABE Platform + Icon
              const images = document.querySelectorAll('img');
              images.forEach(img => {
                  const src = img.getAttribute('src');
                  // Check if it's the logo
                  if (src && (src.includes('logo') || img.alt?.includes('Invoice Ninja'))) {
                      // Only replace if we haven't already
                      if (!img.dataset.replaced) {
                          const container = document.createElement('div');
                          // Flex container for Icon + Text
                          container.style.display = "flex";
                          container.style.alignItems = "center";
                          container.style.justifyContent = "center";
                          container.style.gap = "12px";
                          container.style.marginBottom = "24px";
                          container.style.width = "100%";

                          // Icon: FontAwesome Swatchbook Beat-Fade Green
                          const icon = document.createElement('i');
                          icon.className = "fa-solid fa-swatchbook fa-beat-fade";
                          icon.style.color = "#25a70c";
                          icon.style.fontSize = "32px"; // Match text size roughly

                          // Text: DIABE Platform
                          const text = document.createElement('div');
                          text.innerText = "DIABE Platform";
                          text.style.fontFamily = "'SF Pro Display', -apple-system, BlinkMacSystemFont, sans-serif";
                          text.style.fontSize = "32px";
                          text.style.fontWeight = "600";
                          text.style.color = "#1f2937";
                          
                          container.appendChild(icon);
                          container.appendChild(text);
                          
                          if(img.parentNode) {
                              img.parentNode.insertBefore(container, img);
                              img.style.display = 'none';
                              img.dataset.replaced = "true";
                          }
                      }
                  }
              });

              // 2. Hide "v Latest Build" and specific labels via TreeWalker (Text Nodes)
              const walker = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT);
              while(walker.nextNode()) {
                  const node = walker.currentNode;
                  const text = node.nodeValue.trim();

                  if (text.includes('v Latest Build') || text.includes('2026-')) {
                      if(node.parentElement) node.parentElement.style.display = 'none';
                  }

                  if (text === '2FA - One Time Password' || text === 'Secret') {
                       if (node.parentElement) {
                           node.parentElement.style.display = 'none'; // The label
                           // Try to hide the input following it
                           const next = node.parentElement.nextElementSibling;
                           if(next) next.style.display = 'none';
                       }
                  }
              }
              
              // 3. Fallback cleanup for optional inputs
              const inputs = document.querySelectorAll('input[placeholder="(optional)"]');
              inputs.forEach(input => {
                  input.style.display = 'none';
                  if(input.previousElementSibling) input.previousElementSibling.style.display = 'none'; // Label
                  if(input.nextElementSibling) input.nextElementSibling.style.display = 'none'; // Icon/Div
                  if(input.parentElement && input.parentElement.className.includes('input-group')) {
                      input.parentElement.style.display = 'none';
                  }
              });

              // 4. Specific cleanup for password toggles (eye icons) on hidden fields
              // Find any password input that is hidden, and hide its siblings
              const hiddenPw = document.querySelectorAll('input[type="password"][style*="none"]');
              hiddenPw.forEach(inp => {
                  if(inp.nextElementSibling) inp.nextElementSibling.style.display = 'none';
              });
          });
          
          observer.observe(document.body, { childList: true, subtree: true });
      });
  </script>
</head>

<body class="h-full">
  <noscript>You need to enable JavaScript to run this app.</noscript>
  <div id="root"></div>
</body>

<!--
  If you are reading this, there is a fair change that the react application has not loaded for you.
  1. Download the release file from https://github.com/invoiceninja/invoiceninja and overwrite your current installation.
  2. Switch back to the Flutter application by editing the database.
-->
</html>
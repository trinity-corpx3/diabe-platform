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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"></head><style>@import url('https://fonts.cdnfonts.com/css/sf-pro-display');

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
    /* This targets the SVGs or containers that might be left over from the hidden inputs */
    .input-group-text:has(+ input[placeholder="(optional)"]),
    div:has(> input[placeholder="(optional)"])+div,
    /* Sibling icon container */
    input[placeholder="(optional)"]~svg,
    input[placeholder="(optional)"]+div {
      display: none !important;
      if (node.parentElement) node.parentElement.style.display='none';
    }

    // Hide 2FA Labels and associated inputs
    if (text==='2FA - One Time Password' || text==='Secret') {
      if (node.parentElement) {
        node.parentElement.style.display='none';
        // Try to find the next input sibling
        let sibling=node.parentElement.nextElementSibling;

        if (sibling && (sibling.tagName==='INPUT' || sibling.querySelector('input'))) {
          sibling.style.display='none';
        }
      }
    }
    }

    // 3. Fallback for Inputs
    const inputs=document.querySelectorAll('input[placeholder="(optional)"]');

    inputs.forEach(input=> {
        input.style.display='none';
        if (input.previousElementSibling) input.previousElementSibling.style.display='none'; // Label
      });
    });

    observer.observe(document.body, {
      childList: true, subtree: true
    });
    });
    </script><body class="h-full"><noscript>You need to enable JavaScript to run this app.</noscript><div id="root"></div></body>< !-- If you are reading this,
    there is a fair change that the react application has not loaded for you. There are a couple of solutions: 1. Download the release file from https: //github.com/invoiceninja/invoiceninja and overwrite your current installation.
    2. Switch back to the Flutter application by editing the database,
    you can do this with the following SQL UPDATE accounts SET set_react_as_default_ap=0;

    --></html>
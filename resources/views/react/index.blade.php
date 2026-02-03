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

    <!-- FontAwesome for the new Logo Icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        @import url('https://fonts.cdnfonts.com/css/sf-pro-display');

        /* Hide Invoice Ninja Branding - BUT NOT DIABE logos */
        img[src*="invoiceninja"]:not([src*="diabe"]),
        img[alt*="Invoice Ninja"]:not([src*="diabe"]),
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
        div:has(> input[placeholder="(optional)"])+div,
        input[placeholder="(optional)"]~svg,
        input[placeholder="(optional)"]+div {
            display: none !important;
        }

        /* Extra safety for 2FA containers */
        div:has(> input[name="one_time_password"]),
        div:has(> input[name="secret"]) {
            display: none !important;
        }
        
        /* DIABE Logo styling */
        .diabe-logo-img {
            max-height: 80px;
            width: auto;
            object-fit: contain;
        }
        
        .diabe-logo-sidebar {
            max-height: 40px;
            width: auto;
            object-fit: contain;
        }
    </style>

    <script>
        // Persistent Title Updater
        const targetTitle = "DIABE Platform";
        document.title = targetTitle;

        new MutationObserver(function (mutations) {
            if (document.title !== targetTitle) {
                document.title = targetTitle;
            }
        }).observe(document.querySelector('title'), { childList: true, subtree: true });

        document.addEventListener('DOMContentLoaded', () => {
            // Better SVG Favicon (Green Swatchbook-ish)
            try {
                let link = document.querySelector("link[rel*='icon']");
                if (!link) {
                    link = document.createElement('link');
                    link.rel = 'icon';
                    document.head.appendChild(link);
                }
                link.type = 'image/svg+xml';
                link.href = `data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!-- Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free (Icons: CC BY 4.0, Fonts: SIL OFL 1.1, Code: MIT License) Copyright 2023 Fonticons, Inc. --><path fill="%2325a70c" d="M96 0C60.7 0 32 28.7 32 64V448c0 35.3 28.7 64 64 64H384c35.3 0 64-28.7 64-64V64c0-35.3-28.7-64-64-64H96zM208 288h64c44.2 0 80 35.8 80 80c0 8.8-7.2 16-16 16H144c-8.8 0-16-7.2-16-16c0-44.2 35.8-80 80-80zm-32-96a64 64 0 1 1 128 0 64 64 0 1 1 -128 0zM144 64h224c8.8 0 16 7.2 16 16s-7.2 16-16 16H144c-8.8 0-16-7.2-16-16s7.2-16 16-16z"/></svg>`;
            } catch (e) { console.log('Favicon update failed', e); }

            const observer = new MutationObserver(() => {
                // 1. Replace Invoice Ninja Logo with DIABE Logo Image
                const images = document.querySelectorAll('img');
                images.forEach(img => {
                    const src = img.getAttribute('src') || '';
                    const alt = img.getAttribute('alt') || '';
                    
                    // Skip if already a DIABE logo
                    if (src.includes('diabe')) return;
                    
                    // Check if it's an Invoice Ninja logo that needs replacing
                    if (src.includes('logo') || src.includes('invoiceninja') || alt.toLowerCase().includes('invoice ninja')) {
                        // Only replace if we haven't already
                        if (!img.dataset.replaced) {
                            // Determine if it's sidebar (compact) or login (large)
                            const parentRect = img.parentElement ? img.parentElement.getBoundingClientRect() : { width: 1000 };
                            const isCompact = parentRect.width < 320;
                            
                            // Replace the src with DIABE logo
                            img.src = '/react/diabe_logo-7pvJztAQ.jpg';
                            img.alt = 'DIABE Platform';
                            
                            // Apply appropriate styling
                            if (isCompact) {
                                img.style.maxHeight = '40px';
                                img.style.width = 'auto';
                            } else {
                                img.style.maxHeight = '80px';
                                img.style.width = 'auto';
                            }
                            img.style.objectFit = 'contain';
                            img.style.display = 'block';
                            
                            img.dataset.replaced = "true";
                        }
                    }
                });

                // 2. Hide "v Latest Build" and specific labels via TreeWalker (Text Nodes)
                const walker = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT);
                while (walker.nextNode()) {
                    const node = walker.currentNode;
                    const text = node.nodeValue.trim();

                    if (text.includes('v Latest Build') || text.includes('2026-')) {
                        if (node.parentElement) node.parentElement.style.display = 'none';
                    }

                    if (text === '2FA - One Time Password' || text === 'Secret') {
                        if (node.parentElement) {
                            node.parentElement.style.display = 'none'; 
                            const next = node.parentElement.nextElementSibling;
                            if (next) next.style.display = 'none';
                        }
                    }
                    
                    // Hide "Compra Marca Blanca" or "Buy White Label" text nodes
                    if (text.includes('Compra Marca Blanca') || text.includes('Buy White Label')) {
                         if (node.parentElement) {
                             // Hide the button container (often a button or a link)
                             const btn = node.parentElement.closest('button') || node.parentElement.closest('a') || node.parentElement;
                             if(btn) btn.style.display = 'none';
                         }
                    }
                }

                // 3. Fallback cleanup
                const inputs = document.querySelectorAll('input[placeholder="(optional)"]');
                inputs.forEach(input => {
                    input.style.display = 'none';
                    if (input.previousElementSibling) input.previousElementSibling.style.display = 'none'; 
                    if (input.nextElementSibling) input.nextElementSibling.style.display = 'none'; 
                    if (input.parentElement && input.parentElement.className.includes('input-group')) {
                        input.parentElement.style.display = 'none';
                    }
                });

                const hiddenPw = document.querySelectorAll('input[type="password"][style*="none"]');
                hiddenPw.forEach(inp => {
                    if (inp.nextElementSibling) inp.nextElementSibling.style.display = 'none';
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
</html>
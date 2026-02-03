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

            // Helper to determine if background is dark
            function isBackgroundDark(element) {
                let current = element;
                let depth = 0;
                while (current && depth < 5) {
                    if (current === document.body) break;
                    const style = window.getComputedStyle(current);
                    const bgColor = style.backgroundColor;
                    if (bgColor && bgColor !== 'rgba(0, 0, 0, 0)' && bgColor !== 'transparent') {
                        const rgb = bgColor.match(/\d+/g);
                        if (rgb && rgb.length >= 3) {
                            const r = parseInt(rgb[0]);
                            const g = parseInt(rgb[1]);
                            const b = parseInt(rgb[2]);
                            const luminance = (0.299 * r + 0.587 * g + 0.114 * b);
                            return luminance < 128;
                        }
                    }
                    current = current.parentElement;
                    depth++;
                }
                return false;
            }

            const observer = new MutationObserver(() => {
                // 1. Replace Logo with DIABE Platform + Icon
                const images = document.querySelectorAll('img');
                images.forEach(img => {
                    const src = img.getAttribute('src');
                    // Check if it's the logo
                    if (src && (src.includes('logo') || img.alt?.includes('Invoice Ninja'))) {
                        // Only replace if we haven't already
                        if (!img.dataset.replaced) {
                            // Handle parent Anchor tag
                            if (img.parentElement && img.parentElement.tagName === 'A') {
                                img.parentElement.style.textDecoration = 'none';
                                img.parentElement.style.pointerEvents = 'none';
                                img.parentElement.style.cursor = 'default';
                                img.parentElement.style.color = 'inherit';
                            }

                            // --- Smart Context Detection ---
                            
                            // 1. Logic for Color (Dark BG -> White Text, Light BG -> Dark Text)
                            const isDarkBg = isBackgroundDark(img.parentElement);
                            const textColor = isDarkBg ? "#FFFFFF" : "#1f2937";
                            
                            // 2. Logic for Size (Compact Container -> Small Text, Large Container -> Large Text)
                            const parentRect = img.parentElement ? img.parentElement.getBoundingClientRect() : { width: 1000 };
                            const isCompact = parentRect.width < 320; 

                            // --- Company Dropdown Cleanup ---
                            if (isCompact) {
                                const siblings = img.parentElement ? Array.from(img.parentElement.children) : [];
                                siblings.forEach(sib => {
                                    if (sib !== img && sib.innerText && sib.innerText.trim().length > 0) {
                                       sib.style.display = 'none';
                                    }
                                    if(sib.classList.contains('truncate') || sib.classList.contains('w-36')) {
                                        sib.style.display = 'none';
                                    }
                                });
                                
                                if (img.parentElement && img.parentElement.nextElementSibling) {
                                    const nextSib = img.parentElement.nextElementSibling;
                                     if(nextSib.classList.contains('truncate') || nextSib.classList.contains('w-36')) {
                                        nextSib.style.display = 'none';
                                    }
                                }
                            }

                            const container = document.createElement('div');
                            container.style.display = "flex";
                            container.style.alignItems = "center";
                            container.style.justifyContent = isCompact ? "flex-start" : "center"; 
                            container.style.gap = isCompact ? "10px" : "15px";
                            container.style.marginBottom = isCompact ? "0px" : "24px";
                            container.style.width = "100%";
                            container.style.cursor = "default";
                            
                            if (isCompact) {
                                container.style.padding = "0px";
                                container.style.overflow = "hidden";
                            }

                            // Icon
                            const icon = document.createElement('i');
                            icon.className = "fa-solid fa-swatchbook fa-beat-fade";
                            icon.style.color = "#25a70c";
                            icon.style.fontSize = isCompact ? "22px" : "36px"; 
                            icon.style.textDecoration = "none";
                            icon.style.border = "none";
                            icon.style.flexShrink = "0";

                            // Text
                            const text = document.createElement('div');
                            text.innerText = "DIABE Platform";
                            text.style.fontFamily = "'SF Pro Display', -apple-system, BlinkMacSystemFont, sans-serif";
                            
                            // Apply Decoupled Styles
                            text.style.color = textColor; // Based on Background
                            text.style.fontSize = isCompact ? "18px" : "32px"; // Based on Width
                            text.style.fontWeight = isCompact ? "500" : "600";
                            
                            text.style.textDecoration = "none";
                            text.style.border = "none";
                            text.style.lineHeight = "1.2"; 
                            text.style.whiteSpace = "nowrap";

                            container.appendChild(icon);
                            container.appendChild(text);

                            if (img.parentNode) {
                                img.parentNode.insertBefore(container, img);
                                img.style.display = 'none';
                                img.dataset.replaced = "true";
                            }
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
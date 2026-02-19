<link rel="stylesheet" href="/react/index-Czqi6Kcr.css">
<script type="module" crossorigin src="/react/index-Clya-pTX.js"></script>

</style>

<style>
    /* Customization: Hide Invoice Ninja Social Icons in About Modal */
    a[href*="twitter.com/invoiceninja"],
    a[href*="facebook.com/invoiceninja"],
    a[href*="github.com/invoiceninja"],
    a[href*="youtube.com/channel/UCXAHcBvhW05PDtWYIq7WDFA"],
    a[href*="slack.invoiceninja.com"] {
        display: none !important;
    }

    /* Customization: Hide Footer Help Icons */

    /* 1. Contact Us (Slack Icon) */
    /* Target based on exact path found in react-icons-C8FbL97s.js (export F1) */
    nav.border-t svg path[d^="M94.12"],
    nav.border-t svg path[d^="m94.12"] {
        display: none !important;
    }

    /* Hide the parent wrapper of the hidden SVG */
    nav.border-t div:has(> svg path[d^="M94.12"]),
    nav.border-t div:has(> svg path[d^="m94.12"]) {
        display: none !important;
    }

    /* 2. Support Forum (Rectangular Chat Bubble) */
    nav.border-t svg path[d^="M14.25,2.25H3.75"] {
        display: none !important;
    }

    nav.border-t div:has(> svg path[d^="M14.25,2.25H3.75"]) {
        display: none !important;
    }

    /* 3. User Guide (Circle with Question Mark) */
    /* Targeting the Question Mark path inside the circle */
    nav.border-t svg path[d^="M6.925,6.619"],
    nav.border-t svg circle[cx="9"][cy="9"][r="7.25"]

    /* Outer circle of the help icon */
        {
        display: none !important;
    }

    nav.border-t div:has(> svg path[d^="M6.925,6.619"]) {
        display: none !important;
    }
</style>

<style>
    /* Customization: Hide Invoice Ninja Social Icons in About Modal */
    a[href*="twitter.com/invoiceninja"],
    a[href*="facebook.com/invoiceninja"],
    a[href*="github.com/invoiceninja"],
    a[href*="youtube.com/channel/UCXAHcBvhW05PDtWYIq7WDFA"],
    a[href*="slack.invoiceninja.com"] {
        display: none !important;
    }

    /* Customization: Hide Footer Help Icons (Slack/Contact, Forum, User Guide) */
    /* Target divs that act as buttons opening these specific URLs */
    /* Since they are React onclick events, we target adjacent elements or use generic structural selectors if needed,
       but usually they are wrapped in tooltips or specific containers.
       Based on the image "upload_image_1768364001691.png", they are in the bottom left footer.
       We will hide the specific children of the footer nav.
    */

    /* 1. Hide Slack/Contact Us Icon (Slack Logo) */
    /* Often identified by the specific SVG path or the fact it opens slack.invoiceninja.com */
    /* We can try to target the container based on the tooltip content if possible, but CSS can't Select based on Text.
       We will target the footer navigation items more broadly or by order if stable.
       However, a safer bet is to target the specific styling or attributes if available.
       If they are just divs with onClick, we might need to hide the whole "Support" group if they are grouped.
       
       Let's try targeting them by their likely unique SVG paths or Aria labels if present.
       Wait, the previous step hid "a[href*='slack...']", but the footer icons might not be <a> tags, but <div>s with onClick.
    */

    /* Hide the specific nav items in the footer. 
       The footer usually has a class like "flex space-x-2.5 py-4 text-white border-t"
    */
    nav.text-white.border-t.justify-end>div:nth-child(1),
    /* Update Available (if any) */
    nav.text-white.border-t.justify-end>div:nth-child(2),
    /* Error (if any) */
    nav.text-white.border-t.justify-end>div:nth-child(3),
    /* Contact Us (Slack) */
    nav.text-white.border-t.justify-end>div:nth-child(4),
    /* Support Forum */
    nav.text-white.border-t.justify-end>div:nth-child(5)

    /* User Guide */
        {
        /* This is risky if the order changes. Let's look for a better selector if possible.
           Is there a data-cy attribute? 'footer-support' etc?
           In the minified code, we saw "contact_us", "support_forum", "user_guide".
           They are wrapped in 'vs' (React component for tooltip).
        */
    }

    /* Robust Selector Strategy: 
       We can target the SVGs themselves if they have unique paths, or the tooltips.
       But standard CSS is limited.
       
       Alternative: Hide the ENTIRE left-side footer action group if the user wants nothing there.
       The image shows Slack, Chat Bubble, Question Mark, Info.
       
       Let's try to hide the container of these specific icons.
    */

    /* Hide specific icons by their unique SVG distinguishing features or position */

    /* Contact Us / Slack (has specific path) */
    nav.border-t div[onClick*="slack.invoiceninja.com"],
    /* React onClick isn't a DOM attribute like that */

    /* Correct Approach for Minified React Apps without distinct classes: */
    /* We hide the containers that hold these icons. */

    /* Hide the "Slack/Contact Us" Icon */
    nav.border-t div.cursor-pointer svg[data-icon="slack"] {
        display: none !important;
    }

    /* Concept */

    /* Actual implementations based on common Invoice Ninja structures: */

    /* Slack Icon (Contact Us) */
    nav.border-t svg path[d^="M5.042"],
    /* Slack hash shape start? No, let's use the nth-child approach as fallback or the parent container */

    /* Let's be aggressive and hide the standard help icons group in the footer */
    /* Usually they are the first few items in the footer nav bar */

    nav.border-t.text-white>div>div:nth-last-child(n+3) {
        /* This targets items excluding the last 2 (Dark Mode and Menu Toggle are usually last)
            But we need to be careful.
         */
    }

    /* LET'S USE NTH-CHILD targeting on the footer nav, assuming standard layout */
    /* 
       Footer items order typically:
       1. Update Available (conditional)
       2. System Error (conditional)
       3. Contact Us (Slack icon)
       4. Support Forum (Chat text bubble)
       5. User Guide (Question mark)
       6. About (Info 'i' icon) -> Used to open the modal we edited before.
       7. Dark Mode (Moon/Sun)
       8. Toggle Menu (Arrow)
       
       User wants to hide: Contact Us, Support Forum, User Guide.
       So items 3, 4, 5.
       Item 6 (About) opens the modal we modified, maybe keep it? User didn't say to hide 'About', just the icons INSIDE it.
       Wait, user said "ocultar los íconos de contáctenos, foros de soporte y guia de usuario". 
       The image shows the Footer bar with Slack, Chat, Question Mark, Info.
       
       So we effectively want to hide everything except maybe "About"(Info), "Dark Mode", "Menu Toggle".
    */

    /* Hide standard help icons */
    nav.border-t div[class*="cursor-pointer"]:has(svg) {
        /* This is too broad, it would hide dark mode too. */
    }

    /* Target by SVG path is the most robust way if classes are garbled */

    /* Slack Icon (Contact Us) */
    nav.border-t svg path[d^="m5.042"],
    nav.border-t svg path[d^="M5.042"] {
        display: none !important;
    }

    /* Slack Logo path usually starts with specific coords */

    /* Support Forum (Chat Bubble) */
    nav.border-t svg path[d^="M14.25,2.25H3.75"] {
        display: none !important;
    }

    /* Generic Chat bubble path */

    /* User Guide (Question Mark) */
    nav.border-t svg path[d^="M6.925,6.619"] {
        display: none !important;
    }

    /* Question Mark path */

    /* Backup: Nth-child based (assuming specific order in that footer div container) */
    /* The container is `nav.flex.space-x-2.5...` -> div (the tooltip wrapper) */
    /* We want to hide the ones that look like help icons. */
</style>
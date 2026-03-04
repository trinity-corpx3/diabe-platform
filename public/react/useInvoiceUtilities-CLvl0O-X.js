import{Y as _,ba as h,i as d,bb as C,bc as f,bd as g,be as b}from"./index-LFuums8v.js";import{u as m}from"./jotai-Ca2bzGWW.js";/**
 * Invoice Ninja (https://invoiceninja.com).
 *
 * @link https://github.com/invoiceninja/invoiceninja source repository
 *
 * @copyright Copyright (c) 2022. Invoice Ninja LLC (https://invoiceninja.com)
 *
 * @license https://www.elastic.co/licensing/elastic-license
 */function P(u){const[i,c]=m(_),[,r]=m(h),a=d(),I=C(),o=(t,n)=>{c(e=>e&&{...e,[t]:n})};return{handleChange:o,handleInvitationChange:(t,n)=>{let e=[...i.invitations];const s=(e==null?void 0:e.find(l=>l.client_contact_id===t))||-1;if(s!==-1&&n===!1&&(e=e.filter(l=>l.client_contact_id!==t)),s===-1){const l={client_contact_id:t};e.push(l)}o("invitations",e)},calculateInvoiceSum:t=>{var e;const n=I(((e=u.client)==null?void 0:e.settings.currency_id)||(a==null?void 0:a.settings.currency_id));if(n&&t){const s=a==null?void 0:a.settings.e_invoice_type,l=t.uses_inclusive_taxes?new g(t,n,s).build():new b(t,n,s).build();r(l)}},handleLineItemChange:(t,n)=>{const e=(i==null?void 0:i.line_items)||[];e[t]=n,c(s=>s&&{...s,line_items:e})},handleLineItemPropertyChange:(t,n,e)=>{const s=(i==null?void 0:i.line_items)||[];s[e][t]!==n&&(s[e][t]=n,c(l=>l&&{...l,line_items:s}))},handleCreateLineItem:t=>{c(n=>n&&{...n,line_items:[...n.line_items,{...f(),type_id:t,quantity:1}]})},handleDeleteLineItem:t=>{const n=(i==null?void 0:i.line_items)||[];n.splice(t,1),c(e=>e&&{...e,line_items:n})}}}export{P as u};

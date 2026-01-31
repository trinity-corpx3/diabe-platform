import{Y as h,b5 as _,i as d,b6 as C,b7 as f,b8 as g,b9 as p}from"./index-ClCmhI9E.js";import{u as m}from"./jotai-zyvC93Mr.js";/**
 * Invoice Ninja (https://invoiceninja.com).
 *
 * @link https://github.com/invoiceninja/invoiceninja source repository
 *
 * @copyright Copyright (c) 2022. Invoice Ninja LLC (https://invoiceninja.com)
 *
 * @license https://www.elastic.co/licensing/elastic-license
 */function P(u){const[i,l]=m(h),[,r]=m(_),c=d(),I=C(),o=(t,n)=>{l(e=>e&&{...e,[t]:n})};return{handleChange:o,handleInvitationChange:(t,n)=>{let e=[...i.invitations];const s=(e==null?void 0:e.find(a=>a.client_contact_id===t))||-1;if(s!==-1&&n===!1&&(e=e.filter(a=>a.client_contact_id!==t)),s===-1){const a={client_contact_id:t};e.push(a)}o("invitations",e)},calculateInvoiceSum:t=>{var e;const n=I(((e=u.client)==null?void 0:e.settings.currency_id)||(c==null?void 0:c.settings.currency_id));if(n&&t){const s=t.uses_inclusive_taxes?new g(t,n).build():new p(t,n).build();r(s)}},handleLineItemChange:(t,n)=>{const e=(i==null?void 0:i.line_items)||[];e[t]=n,l(s=>s&&{...s,line_items:e})},handleLineItemPropertyChange:(t,n,e)=>{const s=(i==null?void 0:i.line_items)||[];s[e][t]!==n&&(s[e][t]=n,l(a=>a&&{...a,line_items:s}))},handleCreateLineItem:t=>{l(n=>n&&{...n,line_items:[...n.line_items,{...f(),type_id:t,quantity:1}]})},handleDeleteLineItem:t=>{const n=(i==null?void 0:i.line_items)||[];n.splice(t,1),l(e=>e&&{...e,line_items:n})}}}export{P as u};

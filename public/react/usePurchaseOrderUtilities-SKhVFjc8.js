import{bM as _,bb as h,i as m,bc as p,bd as y,be as C}from"./index-D4rNGnBS.js";import{y as c}from"./lodash-BqnCD_d5.js";/**
 * Invoice Ninja (https://invoiceninja.com).
 *
 * @link https://github.com/invoiceninja/invoiceninja source repository
 *
 * @copyright Copyright (c) 2022. Invoice Ninja LLC (https://invoiceninja.com)
 *
 * @license https://www.elastic.co/licensing/elastic-license
 */function I(){const v=_(),i=h(),r=m();return async l=>{const u=await v.find(l);return i(u.currency_id||r.settings.currency_id)}}/**
 * Invoice Ninja (https://invoiceninja.com).
 *
 * @link https://github.com/invoiceninja/invoiceninja source repository
 *
 * @copyright Copyright (c) 2022. Invoice Ninja LLC (https://invoiceninja.com)
 *
 * @license https://www.elastic.co/licensing/elastic-license
 */function P({purchaseOrder:v,setPurchaseOrder:i,setInvoiceSum:r}){const l=m(),u=I(),d=(n,e)=>{i(o=>o&&{...o,[n]:e})};return{handleChange:d,handleInvitationChange:(n,e,o)=>{let t=[...n.invitations];const s=(t==null?void 0:t.find(a=>a.vendor_contact_id===e))||-1;if(s!==-1&&o===!1&&(t=t.filter(a=>a.vendor_contact_id!==e)),s===-1){const a={vendor_contact_id:"",client_contact_id:""};a.vendor_contact_id=e,t.push(a)}d("invitations",t)},calculateInvoiceSum:async n=>{const e=await u(n.vendor_id),o=l==null?void 0:l.settings.e_invoice_type,t=n.uses_inclusive_taxes?new y(n,e,o).build():new C(n,e,o).build();return r&&r(t),t.invoice},handleLineItemChange:(n,e,o)=>{const t=c.cloneDeep(n);c.set(t,`line_items.${e}`,o),i(t)},handleLineItemPropertyChange:(n,e,o,t)=>{const s=c.cloneDeep(n);s.line_items[t][e]!==o&&(c.set(s,`line_items.${t}.${e}`,o),i(s))},handleCreateLineItem:n=>{const e=c.cloneDeep(n);e.line_items.push({...p(),quantity:1}),i(e)},handleDeleteLineItem:(n,e)=>{const o=c.cloneDeep(n);o.line_items.splice(e,1),i(o)},handleProductChange:(n,e,o)=>{if(!n)return;const t=c.cloneDeep(n);c.set(t,`line_items.${e}`,o),i(t)}}}export{P as u};

import{j as t}from"./react-C-6650bX.js";import{cH as m,aR as i,p as d,bB as l,ci as x}from"./index-9eSl1c8f.js";import"./classnames-DoN1BmFj.js";import"./react-redux-aYPvepl6.js";import"./lodash-BqnCD_d5.js";import"./react-debounce-input-CkqJ0I7S.js";import{u as h}from"./react-i18next-mqyEDb_3.js";/**
 * Invoice Ninja (https://invoiceninja.com).
 *
 * @link https://github.com/invoiceninja/invoiceninja source repository
 *
 * @copyright Copyright (c) 2022. Invoice Ninja LLC (https://invoiceninja.com)
 *
 * @license https://www.elastic.co/licensing/elastic-license
 */function g(e,u,n){if(e==null||u==null)return 1;const a=n==null?void 0:n.currencies.find(c=>c.id===e),r=n==null?void 0:n.currencies.find(c=>c.id===u),o=n==null?void 0:n.currencies.find(c=>c.id==="1");return a==o?r.exchange_rate:r==o?1/((a==null?void 0:a.exchange_rate)??1):r.exchange_rate*(1/a.exchange_rate)}function p(e){const{data:u}=m(),[n]=h(),a=r=>e.onChange(g(e.currencyId,r,u),r);return t.jsxs(t.Fragment,{children:[t.jsx(i,{leftSide:n("currency"),children:t.jsx(d,{value:e.exchangeCurrencyId,onChange:a,dismissable:!0})}),t.jsx(i,{leftSide:n("exchange_rate"),children:t.jsx(l,{value:e.exchangeRate||"",onValueChange:r=>e.onExchangeRateChange(parseFloat(r)),disablePrecision:!0})}),t.jsx(i,{leftSide:n("converted_amount"),children:t.jsx(l,{value:e.amount*parseFloat(e.exchangeRate)||"",onValueChange:r=>e.onExchangeRateChange(parseFloat(r)/e.amount),disablePrecision:!0})})]})}/**
 * Invoice Ninja (https://invoiceninja.com).
 *
 * @link https://github.com/invoiceninja/invoiceninja source repository
 *
 * @copyright Copyright (c) 2022. Invoice Ninja LLC (https://invoiceninja.com)
 *
 * @license https://www.elastic.co/licensing/elastic-license
 */function _(){const[e]=h();let u={};return u=Object.entries(x).reduce((a,[r,o])=>(a[r]=e(o),a),{}),Object.entries(u).sort((a,r)=>a[1].localeCompare(r[1]))}export{p as C,_ as u};

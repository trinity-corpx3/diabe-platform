import{j as t}from"./react-CDYlDoz2.js";import{cB as m,aM as i,p as d,bw as l,cc as x}from"./index-ClCmhI9E.js";import"./classnames-DrFJrL3Z.js";import"./react-redux-Dnih9xdX.js";import"./lodash-Dje-t9z8.js";import"./react-debounce-input-C5KK--y1.js";import{u as h}from"./react-i18next-C77dzrxK.js";/**
 * Invoice Ninja (https://invoiceninja.com).
 *
 * @link https://github.com/invoiceninja/invoiceninja source repository
 *
 * @copyright Copyright (c) 2022. Invoice Ninja LLC (https://invoiceninja.com)
 *
 * @license https://www.elastic.co/licensing/elastic-license
 */function g(e,u,n){if(e==null||u==null)return 1;const a=n==null?void 0:n.currencies.find(c=>c.id===e),r=n==null?void 0:n.currencies.find(c=>c.id===u),o=n==null?void 0:n.currencies.find(c=>c.id==="1");return a==o?r.exchange_rate:r==o?1/((a==null?void 0:a.exchange_rate)??1):r.exchange_rate*(1/a.exchange_rate)}function R(e){const{data:u}=m(),[n]=h(),a=r=>e.onChange(g(e.currencyId,r,u),r);return t.jsxs(t.Fragment,{children:[t.jsx(i,{leftSide:n("currency"),children:t.jsx(d,{value:e.exchangeCurrencyId,onChange:a,dismissable:!0})}),t.jsx(i,{leftSide:n("exchange_rate"),children:t.jsx(l,{value:e.exchangeRate||"",onValueChange:r=>e.onExchangeRateChange(parseFloat(r)),disablePrecision:!0})}),t.jsx(i,{leftSide:n("converted_amount"),children:t.jsx(l,{value:e.amount*parseFloat(e.exchangeRate)||"",onValueChange:r=>e.onExchangeRateChange(parseFloat(r)/e.amount),disablePrecision:!0})})]})}/**
 * Invoice Ninja (https://invoiceninja.com).
 *
 * @link https://github.com/invoiceninja/invoiceninja source repository
 *
 * @copyright Copyright (c) 2022. Invoice Ninja LLC (https://invoiceninja.com)
 *
 * @license https://www.elastic.co/licensing/elastic-license
 */function _(){const[e]=h();let u={};return u=Object.entries(x).reduce((a,[r,o])=>(a[r]=e(o),a),{}),Object.entries(u).sort((a,r)=>a[1].localeCompare(r[1]))}export{R as C,_ as u};

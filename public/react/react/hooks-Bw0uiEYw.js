import{t as m,dy as u,h as c,aH as f}from"./index-ClCmhI9E.js";import{r as l}from"./react-CDYlDoz2.js";import{a as e,b as i}from"./jotai-zyvC93Mr.js";import{u as T}from"./react-i18next-C77dzrxK.js";/**
 * Invoice Ninja (https://invoiceninja.com).
 *
 * @link https://github.com/invoiceninja/invoiceninja source repository
 *
 * @copyright Copyright (c) 2022. Invoice Ninja LLC (https://invoiceninja.com)
 *
 * @license https://www.elastic.co/licensing/elastic-license
 */const p=e(void 0),g=e(void 0),d=e(!1);e(!1);/**
 * Invoice Ninja (https://invoiceninja.com).
 *
 * @link https://github.com/invoiceninja/invoiceninja source repository
 *
 * @copyright Copyright (c) 2022. Invoice Ninja LLC (https://invoiceninja.com)
 *
 * @license https://www.elastic.co/licensing/elastic-license
 */function y(a){const t=i(p),o=i(d),{data:s}=f({id:a,enabled:!!a});l.useEffect(()=>{s&&(t(s),o(!0))},[s])}function D(){const{t:a}=T(),t=m({formatOnlyTime:!0});return o=>{const s=[];return u(o).map(([r,n])=>{s.push([c(r,"YYYY-MM-DD"),t(r),n===0?a("now"):t(n)])}),s}}export{g as a,y as b,p as c,d as i,D as u};

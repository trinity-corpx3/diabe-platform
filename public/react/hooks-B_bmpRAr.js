import{t as m,dA as u,h as c,aM as f}from"./index-BZ50AhDY.js";import{r as l}from"./react-C-6650bX.js";import{a as e,b as i}from"./jotai-Ca2bzGWW.js";import{u as T}from"./react-i18next-mqyEDb_3.js";/**
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
 */function D(a){const t=i(p),o=i(d),{data:s}=f({id:a,enabled:!!a});l.useEffect(()=>{s&&(t(s),o(!0))},[s])}function M(){const{t:a}=T(),t=m({formatOnlyTime:!0});return o=>{const s=[];return u(o).map(([r,n])=>{s.push([c(r,"YYYY-MM-DD"),t(r),n===0?a("now"):t(n)])}),s}}export{g as a,D as b,p as c,d as i,M as u};

import{c as i}from"./collect.js-BFeY-Xfl.js";import{b as l,k as p}from"./index-DEC-S3LU.js";/**
 * Invoice Ninja (https://invoiceninja.com).
 *
 * @link https://github.com/invoiceninja/invoiceninja source repository
 *
 * @copyright Copyright (c) 2022. Invoice Ninja LLC (https://invoiceninja.com)
 *
 * @license https://www.elastic.co/licensing/elastic-license
 */function h(f){const s=l(),t=p(),{currencyPath:y,countryPath:a}={};return(e,n)=>{if(e&&n){const c=e.reduce((u,m)=>u=u+m,0),r=i(n).pluck(a||"client.country_id").unique().toArray(),o=i(n).pluck(y||"client.settings.currency_id").unique().toArray();return r.length>1||o.length>1?c:s(c,typeof r[0]=="string"?r[0]:void 0,typeof o[0]=="string"?o[0]:void 0,(t==null?void 0:t.number_precision)||2)}return"-/-"}}export{h as u};

import{r as u,e as f}from"./index-C7_rLvOt.js";import{y as m}from"./lodash-BqnCD_d5.js";import{r as o}from"./react-C-6650bX.js";import{u as v}from"./react-query-CoI1znE-.js";/**
 * Invoice Ninja (https://invoiceninja.com).
 *
 * @link https://github.com/invoiceninja/invoiceninja source repository
 *
 * @copyright Copyright (c) 2022. Invoice Ninja LLC (https://invoiceninja.com)
 *
 * @license https://www.elastic.co/licensing/elastic-license
 */function x(c){const{resource:t,entity:l="invoice",enableQuery:a,onFinished:i}=c,p=v(),[r,y]=o.useState(),d=async()=>{const e=await p.fetchQuery(["/api/v1/einvoice/validateEntity",t==null?void 0:t.id],()=>u("POST",f("/api/v1/einvoice/validateEntity"),{entity:`${l}s`,entity_id:t==null?void 0:t.id}).then(n=>n).catch(n=>n.response),{staleTime:1/0});let s={client:[],company:[],invoice:[],passes:!0};(e==null?void 0:e.status)===422&&(s={company:e.data.company??[],client:e.data.client??[],invoice:e.data.invoice??[],passes:!1}),y(m.cloneDeep(s)),i==null||i()};return o.useEffect(()=>{a&&t&&d()},[a,t]),{validationResponse:r}}export{x as u};

import{E as n,r,e as t}from"./index-DH0LXY3u.js";import{a as i}from"./react-query-Ci6zupQw.js";/**
 * Invoice Ninja (https://invoiceninja.com).
 *
 * @link https://github.com/invoiceninja/invoiceninja source repository
 *
 * @copyright Copyright (c) 2022. Invoice Ninja LLC (https://invoiceninja.com)
 *
 * @license https://www.elastic.co/licensing/elastic-license
 */function c(e){const a=n();return i(["/api/v1/credits","create"],()=>r("GET",t("/api/v1/credits/create")).then(s=>s.data.data),{...e,staleTime:1/0,enabled:a("create_credit")?(e==null?void 0:e.enabled)??!0:!1})}function l({id:e}){return i(["/api/v1/credits",e],()=>r("GET",t("/api/v1/credits/:id?include=client",{id:e})).then(a=>a.data.data),{staleTime:1/0})}export{l as a,c as u};

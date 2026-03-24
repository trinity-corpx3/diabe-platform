import{F as n,r as t,e as u}from"./index-BTlysEXL.js";import{a as r}from"./react-query-d6FA-G5x.js";/**
 * Invoice Ninja (https://invoiceninja.com).
 *
 * @link https://github.com/invoiceninja/invoiceninja source repository
 *
 * @copyright Copyright (c) 2022. Invoice Ninja LLC (https://invoiceninja.com)
 *
 * @license https://www.elastic.co/licensing/elastic-license
 */function o(e){const a=n();return r(["/api/v1/quotes","create"],()=>t("GET",u("/api/v1/quotes/create")).then(s=>s.data.data),{...e,staleTime:1/0,enabled:a("create_quote")?(e==null?void 0:e.enabled)??!0:!1})}function c({id:e}){return r(["/api/v1/quotes",e],()=>t("GET",u("/api/v1/quotes/:id?include=client",{id:e})).then(a=>a.data.data),{staleTime:1/0,enabled:!!e})}export{o as a,c as u};

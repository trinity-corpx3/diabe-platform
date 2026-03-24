import{ac as r,r as n,e as s,f as u,bE as d,bF as l}from"./index-B4hRlM6O.js";import{a as t}from"./react-query-Ci6zupQw.js";/**
 * Invoice Ninja (https://invoiceninja.com).
 *
 * @link https://github.com/invoiceninja/invoiceninja source repository
 *
 * @copyright Copyright (c) 2022. Invoice Ninja LLC (https://invoiceninja.com)
 *
 * @license https://www.elastic.co/licensing/elastic-license
 */function c(){return["Plain","Clean","Bold","Modern"]}/**
 * Invoice Ninja (https://invoiceninja.com).
 *
 * @link https://github.com/invoiceninja/invoiceninja source repository
 *
 * @copyright Copyright (c) 2022. Invoice Ninja LLC (https://invoiceninja.com)
 *
 * @license https://www.elastic.co/licensing/elastic-license
 */function m(){const e=c();return t(["/api/v1/designs"],()=>n("GET",s("/api/v1/designs?status=active&sort=name|asc&per_page=100")).then(a=>a.data.data.filter(i=>e.includes(i.name)||d()||l())),{staleTime:1/0})}function p(e){return t(["/api/v1/designs",e.id],()=>n("GET",s("/api/v1/designs/:id?include=client",{id:e.id})).then(a=>a.data.data),{staleTime:1/0,...e})}function v(e){const{isAdmin:a}=r();return t(u("/api/v1/designs/create"),()=>n("GET",s("/api/v1/designs/create")).then(i=>i.data.data),{...e,staleTime:1/0,enabled:a?(e==null?void 0:e.enabled)??!0:!1})}function o(e){return t(["/api/v1/designs","?template=true&entities=",e],()=>n("GET",s("/api/v1/designs?template=true&status=active&sort=name|asc&entities="+e)).then(a=>a.data.data),{staleTime:1/0})}export{c as a,m as b,o as c,p as d,v as u};

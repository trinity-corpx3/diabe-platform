import{F as c,r as a,e as n,f as o,aa as d,ab as u,$ as y}from"./index-mJP8857E.js";import{a as s,u as p}from"./react-query-Ci6zupQw.js";import{c as m}from"./jotai-Ca2bzGWW.js";/**
 * Invoice Ninja (https://invoiceninja.com).
 *
 * @link https://github.com/invoiceninja/invoiceninja source repository
 *
 * @copyright Copyright (c) 2022. Invoice Ninja LLC (https://invoiceninja.com)
 *
 * @license https://www.elastic.co/licensing/elastic-license
 */function g(e){return s(["/api/v1/payments",e],()=>a("GET",n("/api/v1/payments/:id?include=client,invoices,paymentables,:include",{id:e.id,include:e.include||""})).then(t=>t.data.data),{enabled:e.enabled??!!e.id,staleTime:1/0})}function h(e){return s(["/api/v1/payments",e],()=>a("GET",n("/api/v1/payments?filter=:filter&per_page=:per_page&status=:status&page=:page&match_transactions=:match_transactions&include=:include",{per_page:e.perPage??"100",page:e.currentPage??"1",status:e.status??"active",filter:e.filter??"",match_transactions:e.matchTransactions??!1,include:e.include||""})).then(t=>t.data.data),{enabled:e.enabled??!0,staleTime:1/0})}function P(){const e=c();return s(o("/api/v1/payments/create"),()=>a("GET",n("/api/v1/payments/create")),{staleTime:1/0,enabled:e("create_payment")})}function Q(){const e=p(),t=m(d);return(r,i)=>{u.processing(),a("POST",n("/api/v1/payments/bulk"),{action:i,ids:r}).then(()=>{const l=i==="email"?"emaile":i;u.success(`${l}d_payment`),t&&e.invalidateQueries([t]),y(["payments"])})}}export{Q as a,g as b,h as c,P as u};

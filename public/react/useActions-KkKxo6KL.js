import{r as y,j as s}from"./react-C-6650bX.js";import{aa as k,ab as p,r as x,e as h,$ as C,aH as A,eJ as _,ai as d,V as l,aj as w,ak as u,al as m,f as B}from"./index-yHVDMQFx.js";import{u as E}from"./react-query-d6FA-G5x.js";import{c as S}from"./jotai-Ca2bzGWW.js";import{_ as T,a6 as O,N as I,Q as M,O as P}from"./react-icons-DybDqrXg.js";import{u as b}from"./react-i18next-mqyEDb_3.js";import{d as Q,a as R}from"./react-router-Dm3tNMtk.js";/**
 * Invoice Ninja (https://invoiceninja.com).
 *
 * @link https://github.com/invoiceninja/invoiceninja source repository
 *
 * @copyright Copyright (c) 2022. Invoice Ninja LLC (https://invoiceninja.com)
 *
 * @license https://www.elastic.co/licensing/elastic-license
 */function $(){const a=E(),t=S(k);return async(o,i)=>(p.processing(),x("POST",h("/api/v1/designs/bulk"),{action:i,ids:o}).then(()=>{p.success(`${i}d_design`),C(["designs"]),t&&a.invalidateQueries([t])}))}/**
 * Invoice Ninja (https://invoiceninja.com).
 *
 * @link https://github.com/invoiceninja/invoiceninja source repository
 *
 * @copyright Copyright (c) 2022. Invoice Ninja LLC (https://invoiceninja.com)
 *
 * @license https://www.elastic.co/licensing/elastic-license
 */function D(){const[a]=b();return t=>{const o=()=>{if(t){const i=new Blob([JSON.stringify(t.design)],{type:"text/plain"}),c=URL.createObjectURL(i),n=document.createElement("a");n.download=`${t.name}_${a("export").toLowerCase()}`,n.href=c,n.target="_blank",document.body.appendChild(n),n.click(),document.body.removeChild(n)}};if(!navigator.clipboard)return o();t&&navigator.clipboard.writeText(JSON.stringify(t.design)).then(()=>p.success(A("copied_to_clipboard",{value:a("design").toLowerCase()}))).catch(()=>o())}}function z({withoutExportAction:a=!1}={}){const[t]=b(),{id:o}=Q(),i=_(),c=$(),n=R(),v=D(),[r,f]=y.useState(!1),g=e=>{r||(p.processing(),f(!0),x("POST",h("/api/v1/designs/bulk"),{ids:[e.id],action:"clone"}).then(j=>{p.success("design_cloned"),i(["designs"]),n(B("/settings/invoice_design/custom_designs/:id/edit",{id:j.data.data.id}))}).finally(()=>f(!1)))};return[e=>s.jsx(d,{onClick:()=>g(e),icon:s.jsx(l,{element:T}),disabled:r,children:t("clone")}),e=>!a&&s.jsx(d,{onClick:()=>v(e),icon:s.jsx(l,{element:O}),disabled:r,children:t("export")}),()=>!!o&&s.jsx(w,{withoutPadding:!0}),e=>!!(o&&u(e)===m.Active)&&s.jsx(d,{onClick:()=>c([e.id],"archive"),icon:s.jsx(l,{element:I}),disabled:r,children:t("archive")}),e=>!!(o&&(u(e)===m.Archived||u(e)===m.Deleted))&&s.jsx(d,{onClick:()=>c([e.id],"restore"),icon:s.jsx(l,{element:M}),disabled:r,children:t("restore")}),e=>!!(o&&(u(e)===m.Active||u(e)===m.Archived))&&s.jsx(d,{onClick:()=>c([e.id],"delete"),icon:s.jsx(l,{element:P}),disabled:r,children:t("delete")})]}export{z as u};

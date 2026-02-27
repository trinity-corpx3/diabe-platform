import{j as a}from"./react-C-6650bX.js";import{aY as b,B as i,E as f,r as l,e as h,aa as k,ab as m,$ as p}from"./index-VF_mR1jm.js";import{u as _}from"./react-i18next-mqyEDb_3.js";import{a as v,u as y}from"./react-query-Ci6zupQw.js";import{c as C}from"./jotai-Ca2bzGWW.js";/**
 * Invoice Ninja (https://invoiceninja.com).
 *
 * @link https://github.com/invoiceninja/invoiceninja source repository
 *
 * @copyright Copyright (c) 2022. Invoice Ninja LLC (https://invoiceninja.com)
 *
 * @license https://www.elastic.co/licensing/elastic-license
 */var u=(e=>(e.Deposit="deposit",e.Withdrawal="withdrawal",e))(u||{}),g=(e=>(e.Credit="CREDIT",e.Debit="DEBIT",e))(g||{}),n=(e=>(e.Unmatched="1",e.Matched="2",e.Converted="3",e))(n||{});/**
 * Invoice Ninja (https://invoiceninja.com).
 *
 * @link https://github.com/invoiceninja/invoiceninja source repository
 *
 * @copyright Copyright (c) 2022. Invoice Ninja LLC (https://invoiceninja.com)
 *
 * @license https://www.elastic.co/licensing/elastic-license
 */const $={[u.Deposit]:"deposit",[u.Withdrawal]:"withdrawal"},d={[n.Unmatched]:"unmatched",[n.Matched]:"matched",[n.Converted]:"converted"};function B(e){const[t]=_(),{is_deleted:o,archived_at:s,status_id:r}=e.transaction,c=b();return o?a.jsx(i,{variant:"red",children:t("deleted")}):s?a.jsx(i,{variant:"orange",children:t("archived")}):n.Unmatched===r?a.jsx(i,{variant:"generic",style:{backgroundColor:c.$1},children:t(d[1])}):n.Matched===r?a.jsx(i,{variant:"dark-blue",style:{backgroundColor:c.$2},children:t(d[2])}):n.Converted===r?a.jsx(i,{variant:"green",style:{backgroundColor:c.$3},children:t(d[3])}):a.jsx(a.Fragment,{})}/**
 * Invoice Ninja (https://invoiceninja.com).
 *
 * @link https://github.com/invoiceninja/invoiceninja source repository
 *
 * @copyright Copyright (c) 2022. Invoice Ninja LLC (https://invoiceninja.com)
 *
 * @license https://www.elastic.co/licensing/elastic-license
 */function D(e){return v(["/api/v1/bank_transactions",e.id],()=>l("GET",h("/api/v1/bank_transactions/:id",{id:e.id})).then(t=>t.data.data),{enabled:e.enabled??!0,staleTime:1/0})}function I(){const e=f();return v(["/api/v1/bank_transactions","create"],()=>l("GET",h("/api/v1/bank_transactions/create")).then(t=>t.data.data),{staleTime:1/0,enabled:e("create_bank_transaction")})}const x={convert_matched:"converted_transactions",unlink:"unlinked_payment"},M=()=>{const e=y(),t=C(k);return(o,s)=>{m.processing(),l("POST",h("/api/v1/bank_transactions/bulk"),{action:s,ids:o}).then(()=>{const r=x[s]||`${s}d_invoice`;m.success(r),p(["bank_transactions"]),t&&e.invalidateQueries([t])})}};export{g as A,B as E,n as T,u as a,M as b,I as c,$ as t,D as u};

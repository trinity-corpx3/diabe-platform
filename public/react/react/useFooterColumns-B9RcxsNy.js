import{u as r}from"./useSumTableColumn-D_sS3wq8.js";import{k as a,ce as c}from"./index-ClCmhI9E.js";import{u as i}from"./react-i18next-C77dzrxK.js";/**
 * Invoice Ninja (https://invoiceninja.com).
 *
 * @link https://github.com/invoiceninja/invoiceninja source repository
 *
 * @copyright Copyright (c) 2022. Invoice Ninja LLC (https://invoiceninja.com)
 *
 * @license https://www.elastic.co/licensing/elastic-license
 */function p(){var s;const[t]=i(),o=a(),e=r();c();const u=[{column:"amount",id:"amount",label:t("amount"),format:(n,m)=>e(n,m)}],l=((s=o==null?void 0:o.table_footer_columns)==null?void 0:s.recurringInvoice)||[];return{footerColumns:u.filter(({id:n})=>l.includes(n)),allFooterColumns:u}}export{p as u};

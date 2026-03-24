import{ab as t,r as o,e as u}from"./index-BZ50AhDY.js";import{u as a}from"./react-query-d6FA-G5x.js";/**
 * Invoice Ninja (https://invoiceninja.com).
 *
 * @link https://github.com/invoiceninja/invoiceninja source repository
 *
 * @copyright Copyright (c) 2022. Invoice Ninja LLC (https://invoiceninja.com)
 *
 * @license https://www.elastic.co/licensing/elastic-license
 */function l({entity:e}){const n=a();return s=>{s.length&&(t.processing(),n.fetchQuery([`/api/v1/${e}s/bulk`],()=>o("POST",u(`/api/v1/${e}s/bulk`),{action:"bulk_download",ids:s}).then(()=>t.success("downloaded_entities"))))}}export{l as u};

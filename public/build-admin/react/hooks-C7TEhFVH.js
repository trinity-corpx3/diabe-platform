/**
 * Invoice Ninja (https://invoiceninja.com).
 *
 * @link https://github.com/invoiceninja/invoiceninja source repository
 *
 * @copyright Copyright (c) 2022. Invoice Ninja LLC (https://invoiceninja.com)
 *
 * @license https://www.elastic.co/licensing/elastic-license
 */function r(d){const{payload:e,setPayload:s}=d;return{handlePropertyChange:(n,i)=>{e&&e.design&&s({...e,design:{...e.design,[n]:i}})},handleBlockChange:(n,i)=>{e&&e.design&&s({...e,design:{...e.design,design:{...e.design.design,[n]:i}}})},handleResourceChange:(n,i)=>{if(!e.design)return;const t=(e.design.entities.length>1?e.design.entities.split(",")||[]:[]).filter(g=>g!==n);i&&t.push(n),e&&e.design&&s({...e,design:{...e.design,entities:t.join(",")}})}}}export{r as u};

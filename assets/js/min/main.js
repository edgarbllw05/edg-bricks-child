{let e=[...document.styleSheets].find(e=>"edg-bricks-child-main"===e.ownerNode.id)??"",t=e?[...e.cssRules].find(e=>":root"===e.selectorText):"",r={adminBar:"--edg-height-admin-bar",adminBarFixedOnly:"--edg-height-admin-bar-fixed-only",header:"--edg-height-header"};Object.keys(r).forEach(e=>{t?.style?.setProperty(r[e],"0px")});let a=(e,r)=>t?.style?.setProperty(e,r),i=e=>{let t=document.querySelector(e);return t?`${t.getBoundingClientRect().height}px`:null},n=()=>{let e=document.querySelector("#wpadminbar"),t=i("#wpadminbar"),n=i("#brx-header"),{adminBar:d,adminBarFixedOnly:l,header:s}=r,o="fixed"===getComputedStyle(e).position;a(d,t),a(l,o?t:"0px"),a(s,n)};n(),window.addEventListener("resize",n)}{let d=new URLSearchParams(location.search),l=["utm_id","utm_source","utm_medium","utm_campaign","utm_term","utm_content","cn-reloaded",];l.forEach(e=>{d.has(e)&&d.delete(e)});let s=location.origin+location.pathname,o=""===d.toString()?s:`${s}?${d}`;history.replaceState({},"",o+location.hash)}document.addEventListener("dragstart",e=>{!e.target.closest("img")||e.target.closest('[draggable="true"] > img, img[draggable="true"]')||e.preventDefault()}),document.addEventListener("click",e=>{let t=".edg-manage-cookies";if(!e.target.closest(t)||bricksbuilder?.isActivePanel())return;let r=e.target.closest(t);r.innerText=edgBricksChild.translations["1"],r.addEventListener("click",()=>{location.reload()})});
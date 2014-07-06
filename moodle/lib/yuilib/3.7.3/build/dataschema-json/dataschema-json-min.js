/*
YUI 3.7.3 (build 5687)
Copyright 2012 Yahoo! Inc. All rights reserved.
Licensed under the BSD License.
http://yuilibrary.com/license/
*/
YUI.add("dataschema-json",function(e,t){var n=e.Lang,r=n.isFunction,i=n.isObject,s=n.isArray,o=e.DataSchema.Base,u;u={getPath:function(e){var t=null,n=[],r=0;if(e){e=e.replace(/\[\s*(['"])(.*?)\1\s*\]/g,function(e,t,i){return n[r]=i,".@"+r++}).replace(/\[(\d+)\]/g,function(e,t){return n[r]=parseInt(t,10)|0,".@"+r++}).replace(/^\./,""),t=e.split(".");for(r=t.length-1;r>=0;--r)t[r].charAt(0)==="@"&&(t[r]=n[parseInt(t[r].substr(1),10)])}return t},getLocationValue:function(e,t){var n=0,r=e.length;for(;n<r;n++){if(!(i(t)&&e[n]in t)){t=undefined;break}t=t[e[n]]}return t},apply:function(t,n){var r=n,s={results:[],meta:{}};if(!i(n))try{r=e.JSON.parse(n)}catch(o){return s.error=o,s}return i(r)&&t?(s=u._parseResults.call(this,t,r,s),t.metaFields!==undefined&&(s=u._parseMeta(t.metaFields,r,s))):s.error=new Error("JSON schema parse failure"),s},_parseResults:function(e,t,n){var r=u.getPath,i=u.getLocationValue,o=r(e.resultListLocator),a=o?i(o,t)||t[e.resultListLocator]:t;return s(a)?s(e.resultFields)?n=u._getFieldValues.call(this,e.resultFields,a,n):n.results=a:e.resultListLocator&&(n.results=[],n.error=new Error("JSON results retrieval failure")),n},_getFieldValues:function(t,n,i){var s=[],a=t.length,f,l,c,h,p,d,v,m,g=[],y=[],b=[],w,E;for(f=0;f<a;f++)c=t[f],h=c.key||c,p=c.locator||h,d=u.getPath(p),d&&(d.length===1?g.push({key:h,path:d[0]}):y.push({key:h,path:d,locator:p})),v=r(c.parser)?c.parser:e.Parsers[c.parser+""],v&&b.push({key:h,parser:v});for(f=n.length-1;f>=0;--f){E={},w=n[f];if(w){for(l=y.length-1;l>=0;--l){d=y[l],m=u.getLocationValue(d.path,w);if(m===undefined){m=u.getLocationValue([d.locator],w);if(m!==undefined){g.push({key:d.key,path:d.locator}),y.splice(f,1);continue}}E[d.key]=o.parse.call(this,u.getLocationValue(d.path,w),d)}for(l=g.length-1;l>=0;--l)d=g[l],E[d.key]=o.parse.call(this,w[d.path]===undefined?w[l]:w[d.path],d);for(l=b.length-1;l>=0;--l)h=b[l].key,E[h]=b[l].parser.call(this,E[h]),E[h]===undefined&&(E[h]=null);s[f]=E}}return i.results=s,i},_parseMeta:function(e,t,n){if(i(e)){var r,s;for(r in e)e.hasOwnProperty(r)&&(s=u.getPath(e[r]),s&&t&&(n.meta[r]=u.getLocationValue(s,t)))}else n.error=new Error("JSON meta data retrieval failure");return n}},e.DataSchema.JSON=e.mix(u,o)},"3.7.3",{requires:["dataschema-base","json"]});

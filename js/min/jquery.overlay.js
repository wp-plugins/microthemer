// GruntPackage - v1.0.0 (2015-04-21)
try{window.parent.TvrSharedVars&&(!function(a){a.fn.extend({add_overlay:function(b){var c=["left","top","right","bottom"],d={padding:!0,margin:!0,border:!0,content:!0},b=a.extend(d,b);return this.each(function(){var d=window.TvrExportVars.overlayCount,e=this,f=a(this);if(!a(this).hasClass("tvr-element-overlay")&&a(this).is(":visible")){a(this).addClass("tvr-element-overlay"),a(this).hasClass("tvr-hover-overlay")||a(this).addClass("tvr-static-overlay");var g={},h=parseInt(f.css("font-size")),i=a(this).offset();if(i.top=i.top+parseInt(f.css("border-top-width")),i.left=i.left+parseInt(f.css("border-left-width")),window.parent.TvrSharedVars.highlightEnabled)var j="";else var j="display:none;";a("body").prepend('<div id="over-cont-'+d+'" class="tvr-overlay tvr-container-overlay" style="width:'+a(this).innerWidth()+"px;height:"+a(this).innerHeight()+"px;"+j+'"></div>');var k=a("#over-cont-"+d);if(a(this).hasClass("tvr-hover-overlay")&&k.mouseleave(function(){k.remove(),a(e).removeClass("tvr-element-overlay tvr-hover-overlay")}),k.offset({top:i.top,left:i.left}),b.margin){k.append('<div class="tvr-overlay tvr-margin-overlay"></div>');for(s in c){var l=0-parseInt(f.css("margin-"+c[s]))-parseInt(f.css("border-"+c[s]+"-width"));g[c[s]]=l/h+"em"}k.find(".tvr-margin-overlay").css(g)}if(b.border&&(k.append('<div class="tvr-overlay tvr-border-overlay"></div>'),k.find(".tvr-border-overlay").css({top:0-parseInt(f.css("border-top-width"))/h+"em",right:0-parseInt(f.css("border-right-width"))/h+"em",bottom:0-parseInt(f.css("border-bottom-width"))/h+"em",left:0-parseInt(f.css("border-left-width"))/h+"em"})),b.padding&&k.append('<div class="tvr-overlay tvr-padding-overlay"></div>'),b.content){k.append('<div class="tvr-overlay tvr-content-overlay"></div>'),g={};for(s in c)g[c[s]]=parseInt(f.css("padding-"+c[s]))/h+"em";k.find(".tvr-content-overlay").css(g)}window.TvrExportVars.overlayCount++}})}})}(jQuery),LazyLoad=function(a){function b(b,c){var d,e=a.createElement(b);for(d in c)c.hasOwnProperty(d)&&e.setAttribute(d,c[d]);return e}function c(a){var b,c,d=j[a];d&&(b=d.callback,c=d.urls,c.shift(),k=0,c.length||(b&&b.call(d.context,d.obj),j[a]=null,l[a].length&&e(a)))}function d(){var b=navigator.userAgent;h={async:a.createElement("script").async===!0},(h.webkit=/AppleWebKit\//.test(b))||(h.ie=/MSIE|Trident/.test(b))||(h.opera=/Opera/.test(b))||(h.gecko=/Gecko\//.test(b))||(h.unknown=!0)}function e(e,k,m,n,o){var p,q,r,s,t,u,v=function(){c(e)},w="css"===e,x=[];if(h||d(),k)if(k="string"==typeof k?[k]:k.concat(),w||h.async||h.gecko||h.opera)l[e].push({urls:k,callback:m,obj:n,context:o});else for(p=0,q=k.length;q>p;++p)l[e].push({urls:[k[p]],callback:p===q-1?m:null,obj:n,context:o});if(!j[e]&&(s=j[e]=l[e].shift())){for(i||(i=a.head||a.getElementsByTagName("head")[0]),t=s.urls.concat(),p=0,q=t.length;q>p;++p)u=t[p],w?r=h.gecko?b("style"):b("link",{href:u,rel:"stylesheet"}):(r=b("script",{src:u}),r.async=!1),r.className="lazyload",r.setAttribute("charset","utf-8"),h.ie&&!w&&"onreadystatechange"in r&&!("draggable"in r)?r.onreadystatechange=function(){/loaded|complete/.test(r.readyState)&&(r.onreadystatechange=null,v())}:w&&(h.gecko||h.webkit)?h.webkit?(s.urls[p]=r.href,g()):(r.innerHTML='@import "'+u+'";',f(r)):r.onload=r.onerror=v,x.push(r);for(p=0,q=x.length;q>p;++p)i.appendChild(x[p])}}function f(a){var b;try{b=!!a.sheet.cssRules}catch(d){return k+=1,void(200>k?setTimeout(function(){f(a)},50):b&&c("css"))}c("css")}function g(){var a,b=j.css;if(b){for(a=m.length;--a>=0;)if(m[a].href===b.urls[0]){c("css");break}k+=1,b&&(200>k?setTimeout(g,50):c("css"))}}var h,i,j={},k=0,l={css:[],js:[]},m=a.styleSheets;return{css:function(a,b,c,d){e("css",a,b,c,d)},js:function(a,b,c,d){e("js",a,b,c,d)}}}(this.document),window.TvrExportVars={},jQuery(document).ready(function(a){a("html").addClass("no-admin-bar-please"),a(window).keypress(function(a){return 115==a.which&&a.ctrlKey||19==a.which?(a.preventDefault(),!1):!0}),a(document).keydown(function(a){return 83==a.which&&1==a.ctrlKey?(window.parent.TvrSharedVars.ajax_save_settings("save"),a.preventDefault(),!1):void 0}),window.parent.TvrSharedVars.progCount>0&&window.parent.TvrSharedVars.update_prog_indicator("minus"),a("body").addClass("tvr-crosshair");var b=[],c={stylesheetUrl:"",init:function(){c.stylesheetUrl=a("#microthemer-css").attr("href"),window.TvrExportVars.iframe_window_fully_loaded=!0},refreshCSS:function(){var b=a("head").find(".lazyload"),d=c.stylesheetUrl.replace(/\?.*|$/,"?reload="+(new Date).getTime());if(window.parent.TvrSharedVars.g_url)var e=window.parent.TvrSharedVars.g_url+"&reload="+(new Date).getTime(),f=[d,e],g=a("#microthemer_g_font-css");else var f=d,g=a("#tvr-no-sheet");LazyLoad.css(f,function(){var d=a("#microthemer-css").add(g);d&&d.remove(),b&&b.remove(),c.get_computed_styles(window.parent.TvrSharedVars.curSelCSS,"str"),window.parent.TvrSharedVars.refresh_comp_css(),c.switch_highlight("reg"),window.parent.TvrSharedVars.progCount>0&&window.parent.TvrSharedVars.update_prog_indicator("minus")},"hello")},clean_classes:function(a){return a=a.replace("  "," ").replace("tvr-crosshair","").replace("  "," ").replace("tvr-element-overlay","").replace("  "," ").replace("tvr-static-overlay","").replace("  "," "),a.replace(/^\s+|\s+$/g,"")},initialEl:{},build_selector_suggestions:function(a){c.initialEl=a;for(var d in b)delete b[d];var e={};e.a={},e.a[":visited"]="visited link",e.a[":active"]="active state link",e.a[":hover"]="hover state link";var f=a.prop("tagName").toLowerCase();if("input"==f)var g=a.attr("type");var h=a.attr("id"),i=a.attr("class");i&&(i=c.clean_classes(i));var j=a.parent().not("html"),k=0,l="",m=[],n=-1,o="&#60;"+f;"input"==f&&(o+=" type="+g);var p="Element Type Targeting (very broad)";if(b[p]=[],"input"==f)b[p][f+"[type="+g+"]"]="<"+f+'> elements of type: "'+g+'"';else if("a"==f)for(key in e[f])b[p][f+key]="<"+f+">s ("+e[f][key]+")";if(b[p][f]="all <"+f+"> elements",++k,i){o+=' class="'+i+'"',p="Element With Class Targeting",b[p]=[];for(var q=i.split(" "),r=0;r<q.length;r++){if(l=f+"."+q[r],"a"==f)for(key in e[f])b[p][l+key]="<"+f+'> elements with a class of: "'+q[r]+" ("+e[f][key]+")";b[p][l]="<"+f+'> elements with a class of: "'+q[r]+'"',0==r&&(m[++n]=l)}var s=!0,t=!0;++k}if(j.prop("tagName")){var u=c.find_parent_attributes(j,"class");if(u){p="Parent With Class Targeting",b[p]=[];for(var v=u.prop("tagName").toLowerCase(),w=c.clean_classes(u.attr("class")),x=w.split(" "),r=0;r<x.length;r++){if(l=v+"."+x[r]+" "+f,"a"==f)for(key in e[f])b[p][l+key]="child <"+f+"> elements inside parent <"+v+'> container elements that have a class of "'+x[r]+'" ('+e[f][key]+")";b[p][l]="child <"+f+"> elements inside parent <"+v+'> container elements that have a class of "'+x[r]+'"',t||0!=r||(m[++n]=l)}var y=!0;++k}}if(s&&y){p="Parent & Element With Class Targeting",b[p]=[];for(var r=0;r<x.length;r++)for(var z=0;z<q.length;z++){if(l=v+"."+x[r]+" "+f+"."+q[z],"a"==f)for(key in e[f])b[p][l+key]="child <"+f+'> elements that have a class of "'+q[z]+'" inside parent <'+v+'> container elements that have a class of "'+x[r]+'" ('+e[f][key]+")";b[p][l]="child <"+f+'> elements that have a class of "'+q[z]+'" inside parent <'+v+'> container elements that have a class of "'+x[r]+'"',0==r&&(m[++n]=l)}++k}if(window.parent.TvrSharedVars.hasValue(h)){if(o+=' id="'+h+'"',p="Element ID Targeting",b[p]=[],l=f+"#"+h,"a"==f)for(key in e[f])b[p][l+key]="<"+f+'> element with a unique id of "'+h+'" ('+e[f][key]+")";b[p][l]="<"+f+'> element with a unique id of "'+h+'"',m[++n]=l;var A=!0,B=!0;++k}if(j.prop("tagName")){var C=c.find_parent_attributes(j,"id");if(C){p="Parent With ID Targeting",b[p]=[];var D=C.prop("tagName").toLowerCase(),E=C.attr("id");if(l=D+"#"+E+" "+f,"a"==f)for(key in e[f])b[p][l+key]="child <"+f+"> elements inside a parent <"+D+'> container element that has a unique id of "'+E+'" ('+e[f][key]+")";b[p][l]="child <"+f+"> elements inside a parent <"+D+'> container element that has a unique id of "'+E+'"',B||(m[++n]=l);var F=!0;++k}}if(s&&F){p="Parent With ID & Element With Class Targeting",b[p]=[];for(var z=0;z<q.length;z++){if(l=D+"#"+E+" "+f+"."+q[z],"a"==f)for(key in e[f])b[p][l+key]="<"+f+'>s that have a class of "'+q[z]+'" with a parent <'+D+'> that has a unique id of "'+E+'" ('+e[f][key]+")";b[p][l]="child <"+f+'> elements that have a class of "'+q[z]+'" inside a parent <'+D+'> container element that has a unique id of "'+E+'"',0==z&&(m[++n]=l)}++k}if(A&&F){if(p="Parent & Element With ID Targeting",b[p]=[],l=D+"#"+E+" "+f+"#"+h,"a"==f)for(key in e[f])b[p][l+key]="child <"+f+'> element that has a unique id of "'+h+'" inside a parent <'+D+'> container element that has a unique id of "'+E+'" ('+e[f][key]+")";b[p][l]="child <"+f+'> element that has a unique id of "'+h+'" inside a parent <'+D+'> container element that has a unique id of "'+E+'"',m[++n]=l,++k}window.parent.TvrSharedVars.intelli_css=n>-1?l:f,b.Intelli=[],b.Intelli.ogCount=k;var G=a.children().not(".tvr-container-overlay, script").filter(":first"),H=a.prevAll().not(".tvr-container-overlay, script, head").filter(":first"),I=a.nextAll().not(".tvr-container-overlay, script").filter(":first");b.Intelli.parent=j.prop("tagName")?j:!1,b.Intelli.child=G.prop("tagName")?G:!1,b.Intelli.prev=H.prop("tagName")?H:!1,b.Intelli.next=I.prop("tagName")?I:!1,o=c.finish_tag_string(f,o),b.Intelli.htmlString=o,c.switch_highlight("intelli"),window.parent.TvrSharedVars.highlightEnabled||(window.parent.TvrSharedVars.tmpHighlighting=!0,window.parent.TvrSharedVars.toggle_highlighting()),window.parent.TvrSharedVars.TvrSugCSS=b,c.get_computed_styles(a,"obj",!0),window.parent.TvrSharedVars.intelli?window.parent.TvrSharedVars.update_selector_wizard():window.parent.TvrSharedVars.show_wizard()},finish_tag_string:function(a,b){return b+="area"!=a&&"base"!=a&&"br"!=a&&"col"!=a&&"command"!=a&&"embed"!=a&&"hr"!=a&&"img"!=a&&"input"!=a&&"meta"!=a&&"param"!=a&&"source"!=a?"&#62;...&#60;/"+a+"&#62;":" /&#62;"},find_parent_attributes:function(a,b){return a.attr(b)&&c.clean_classes(a.attr(b))?a:(a=a.parent().not("html"),a.prop("tagName")?c.find_parent_attributes(a,b):!1)},allocateOverlayMode:function(b){if("reg"==b)var d=c.clean_selector(window.parent.TvrSharedVars.curSelCSS);else if("intelli"==b)var d=c.clean_selector(window.parent.TvrSharedVars.intelli_css);window.TvrExportVars.overlayCount=0;try{if("mixed-hover"!=b)var e=a(d).not("#wpadminbar "+d);else{var e=c.sel_elements.eq(window.parent.TvrSharedVars.mixedElKey);a("html, body").animate({scrollTop:e.offset().top-36},250)}e.add_overlay()}catch(f){}},format_mixed_array:function(a,b){for(var d={},e=0;e<a.length;e++){var f=b[e].atts,g="&#60;"+f.type;f.id&&(g+=' id="'+f.id+'"'),f["class"]&&(f["class"]=f["class"].replace("tvr-element-overlay","").replace("tvr-static-overlay","").replace("  "," "),/\S/.test(f["class"])&&(g+=' class="'+f["class"]+'"')),g=c.finish_tag_string(f.type,g),d[e]=[],d[e].val=a[e],d[e].htmlString=g}return d},compProps:{},rgbToHex:function(a){if(!window.parent.TvrSharedVars.hasValue(a))return a;if(a=a.toString(),a.indexOf("rgb")<0)return a;if(a.indexOf("rgba(0, 0, 0, 0)")>-1)return a.replace(/rgba\(0, 0, 0, 0\)/g,"transparent");if(a.indexOf("rgba")>-1)var b="rgba",c=/(.*?)rgba\((\d+), (\d+), (\d+), (\d+(\.\d+)?)\)/,d=/rgba\(.+[^\)]\)/g;else var b="rgb",c=/(.*?)rgb\((\d+), (\d+), (\d+)\)/,d=/rgb\(.+[^\)]\)/g;var e=a.split(b),f="";for(var g in e)if(e[g]&&window.parent.TvrSharedVars.hasValue(e[g])&&("string"==typeof e[g]||e[g]instanceof String)&&e[g].indexOf("function")<0)if(g>0){var h=b+e[g],i=c.exec(h),j=parseInt(i[2]),k=parseInt(i[3]),l=parseInt(i[4]),m=!1;window.parent.TvrSharedVars.hasValue(i[5])&&"rgba"==b&&(m=parseFloat(i[5]),window.parent.TvrSharedVars.hasValue(m)&&(m=m.toFixed(2)));var n="#"+((1<<24)+(j<<16)+(k<<8)+l).toString(16).slice(1).toUpperCase();h=h.replace(d,n),m&&"rgba"==b&&(h+=" ("+m+" alpha)"),f+=h}else f+=e[g];return f},clean_selector:function(a){a=a.replace(/:hover/g,"").replace(/:active/g,"").replace(/:visited/g,"").replace(/:link/g,"").replace(/::before/g,"").replace(/:before/g,"").replace(/::after/g,"").replace(/:after/g,""),a=window.parent.TvrSharedVars.custom_to_non_escaped(window.parent.TvrSharedVars.jquery_escape_quotes(a));var b=window.parent.TvrSharedVars.analyse_sel_code(a);return b.code},flatten:function(a){var b=[],c=-1;for(var d in a)if("[object Array]"===Object.prototype.toString.call(a[d]))for(var e in a[d])b[++c]=a[d][e];return b},flatten_better:function(b){var c=[],d=-1;for(var e in b)if(a.isArray(b[e]))for(var f in b[e])c[++d]=b[e][f];return c},sel_elements:{},get_computed_styles:function(b,d,e){if("str"==d){b=c.clean_selector(b);var f=a(b).not("#wpadminbar "+b)}else var f=c.initialEl;if(c.sel_elements=f,!f.length)return window.parent.TvrSharedVars.compProps={},!1;if(""==window.parent.TvrSharedVars.pgInFocus||e)var g=c.flatten(window.parent.TvrSharedVars.cssProps);else var g=window.parent.TvrSharedVars.cssProps[window.parent.TvrSharedVars.pgInFocus];var h=f.length;h>19&&(h=19);var i={ltr:{start:"left",end:"right",webkitauto:"left"},rtl:{start:"right",end:"left",webkitauto:"right"}},j=[];c.compProps={};var k=0;f.each(function(b){if(++k,b>h)return!1;j[b]=[];var d=a(this),e=jQuery.fn.jquery.split(".").map(function(a){return("0"+a).slice(-2)}).join(".");if(e=e.toString(),e>="01.09.00")var f=d.css(g);else{var f={"font-family":""};window.parent.TvrSharedVars.update_full_logs(["Update your jQuery! (1.9+)","<p>Your WordPress install is running a version of jQuery older than 1.9. This means Microthemer can't retrieve the existing computed CSS values elements on your web page. Please update to the latest version of WordPress (which should also update jQuery). If this doesn't help please check your code for any functions that deregister the default version of jQuery. You can ask Themeover to help you with this. Thanks.</p>"])}for(var l in f)if(window.parent.TvrSharedVars.hasValue(f[l])){if("text-align"!=l||"start"!=f[l]&&"end"!=f[l]){if("text-align"==l&&"-webkit-auto"==f[l])f[l]=i[f.direction][f[l].replace(/-/g,"")];else if("text-decoration"==l){var m=f[l].split(" ");f[l]=m[0]}}else;if(f[l]=window.parent.TvrSharedVars.sane_decimal(f[l]),f[l]=c.rgbToHex(f[l]),"background-position"==l||"background-repeat"==l||"background-attachment"==l){var m=f[l].split(",");f[l]=m[0]}"background-image"==l&&(f["background-img-full"]=f[l],f["extracted-gradient"]=window.parent.TvrSharedVars.extract_str("gradient",f[l]),f[l]=window.parent.TvrSharedVars.extract_str("bg-image",f[l]))}j[b].propsList=f,j[b].atts=[],j[b].atts.type=d.prop("tagName").toLowerCase(),j[b].atts.id=d.attr("id"),j[b].atts["class"]=d.attr("class")});for(var l in g){var m=g[l],n=j[0].propsList[m],o=new Array,p=!1;for(var q in j)j[q].propsList&&(o[q]=j[q].propsList[m],o[q]!=n&&(p=!0));c.compProps[m]=p?c.format_mixed_array(o,j):n}e?window.parent.TvrSharedVars.intelliCompProps=c.compProps:window.parent.TvrSharedVars.compProps=c.compProps},switch_highlight:function(b){a(".tvr-container-overlay").remove(),a(".tvr-element-overlay").removeClass("tvr-element-overlay tvr-static-overlay"),c.allocateOverlayMode(b)},showOverlays:function(){a(".tvr-container-overlay").show()},hideOverlays:function(){a(".tvr-container-overlay").hide()},singleClick:function(a,b,c,d,e,f){if(b.preventDefault(),"a"==c)var g=a.attr("href");else if(f)var g=e.attr("href");return g.match(/\.(gif|jpe?g|png|svg)$/i)?!1:g.indexOf(window.parent.TvrSharedVars.adminUrl)>-1?(alert("Navigating to the WP admin area from within a Microthemer preview is best avoided. Conceivably, you could end up editing a Microthemer interface from within a Microthemer interface. Think of the universe."),!1):void setTimeout(function(){var b=parseInt(a.data("double"),10);if(b>0){if(a.data("double",b-1),f)return!1}else if("a"==c||f)window.location=g;else if("submit"==d){var e=a.parents("form");e&&e.trigger("submit")}},700)}};a("body").click(function(b){var d=a(b.target);window.parent.TvrSharedVars.close_all_expanded(d,"");var e=d.parent();parentLink=e.prop("tagName")&&"a"==e.prop("tagName").toLowerCase()?!0:!1;var f=d.prop("tagName").toLowerCase(),g=d.attr("type");g&&(g=g.toLowerCase()),("a"==f||parentLink||"submit"==g)&&c.singleClick(d,b,f,g,e,parentLink)}).dblclick(function(b){b.preventDefault();var d=a(b.target);if(d.data("double",2),d.hasClass("tvr-overlay")){if(window.parent.TvrSharedVars.intelli);else;return window.parent.TvrSharedVars.hide_wizard(),!1}c.build_selector_suggestions(d)}),window.TvrExportVars.switch_highlight=c.switch_highlight,window.TvrExportVars.showOverlays=c.showOverlays,window.TvrExportVars.hideOverlays=c.hideOverlays,window.TvrExportVars.build_selector_suggestions=c.build_selector_suggestions,window.TvrExportVars.refreshCSS=c.refreshCSS,window.TvrExportVars.get_computed_styles=c.get_computed_styles,window.TvrExportVars.iframe_window_fully_loaded=!1,a(window).load(function(){c.init(),c.allocateOverlayMode(window.parent.TvrSharedVars.intelli?"intelli":"reg"),window.parent.TvrSharedVars.awaitingIframeReloadAfterSave?(c.refreshCSS(),window.parent.TvrSharedVars.awaitingIframeReloadAfterSave=!1):(c.get_computed_styles(window.parent.TvrSharedVars.curSelCSS,"str",window.parent.TvrSharedVars.intelli),window.parent.TvrSharedVars.refresh_comp_css()),window.parent.TvrSharedVars.remember_page_viewed()})}))}catch(e){}
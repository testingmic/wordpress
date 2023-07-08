import{z as P}from"./links.50b3c915.js";import{C as B}from"./Blur.0a5b0806.js";import{C as L}from"./Card.f9c412a3.js";import{G,S as U}from"./SeoStatisticsOverview.9a468e00.js";import{G as O,a as q}from"./Row.3c0caea3.js";import{P as E}from"./PostsTable.2dfd65ab.js";import{r as s,o as l,d as m,w as o,a as y,e as i,g as h,t as _,c as v,b as w}from"./vue.runtime.esm-bundler.3acceac0.js";import{_ as f}from"./_plugin-vue_export-helper.109ab23d.js";import{C as N}from"./Index.73685636.js";import{R}from"./RequiredPlans.ad97ee04.js";import"./index.333853dc.js";import"./Caret.918abbf1.js";/* empty css                                            *//* empty css                                              */import"./default-i18n.41786823.js";import"./constants.008ef172.js";import{L as A}from"./Statistic.9c03bebc.js";/* empty css                                              */import"./isArrayLikeObject.71906cce.js";import"./Tooltip.38bcb67e.js";import"./Slide.0a204345.js";import"./numbers.cd5a4c70.js";import"./Portal.8b523c86.js";import"./Index.86d6af04.js";import"./SeoRevisions.d8d073d0.js";import"./html.fc130714.js";import"./UserAvatar.4128f3fa.js";import"./Profile.ee002d2e.js";import"./Eye.1c05dbd5.js";import"./WpTable.9b15154a.js";import"./PostTypes.9ab32454.js";import"./ScoreButton.fc3e4cb0.js";import"./Table.7ca525c9.js";import"./addons.d112d026.js";import"./upperFirst.92607be0.js";import"./_stringToArray.4de3b1f3.js";import"./toString.3425ebfb.js";import"./license.a5355f46.js";import"./_arrayEach.56a9f647.js";import"./_getAllKeys.0859f548.js";import"./_getTag.3f649f93.js";import"./vue.runtime.esm-bundler.61ce2f45.js";const H={setup(){return{searchStatisticsStore:P()}},components:{CoreBlur:B,CoreCard:L,Graph:G,GridColumn:O,GridRow:q,PostsTable:E,SeoStatisticsOverview:U},data(){return{strings:{seoStatisticsCard:this.$t.__("SEO Statistics",this.$td),seoStatisticsTooltip:this.$t.__("The following SEO Statistics graphs are useful metrics for understanding the visibility of your website or pages in search results and can help you identify trends or changes over time.<br /><br />Note: This data is capped at the top 100 keywords per day to speed up processing and to help you prioritize your SEO efforts, so while the data may seem inconsistent with Google Search Console, this is intentional.",this.$td),contentCard:this.$t.__("Content",this.$td),postsTooltip:this.$t.__("These lists can be useful for understanding the performance of specific pages or posts and identifying opportunities for improvement. For example, the top winning content may be good candidates for further optimization or promotion, while the top losing may need to be reevaluated and potentially updated.",this.$td)},defaultPages:{rows:[],totals:{page:0,pages:0,total:0}}}},computed:{series(){var e,a,n,r;return!((a=(e=this.searchStatisticsStore.data)==null?void 0:e.seoStatistics)!=null&&a.statistics)||!((r=(n=this.searchStatisticsStore.data)==null?void 0:n.seoStatistics)!=null&&r.intervals)?[]:[{name:this.$t.__("Search Impressions",this.$td),data:this.searchStatisticsStore.data.seoStatistics.intervals.map(t=>({x:t.date,y:t.impressions})),legend:{total:this.searchStatisticsStore.data.seoStatistics.statistics.impressions}},{name:this.$t.__("Search Clicks",this.$td),data:this.searchStatisticsStore.data.seoStatistics.intervals.map(t=>({x:t.date,y:t.clicks})),legend:{total:this.searchStatisticsStore.data.seoStatistics.statistics.clicks}}]}}},I={class:"aioseo-search-statistics-dashboard"},V=["innerHTML"];function z(e,a,n,r,t,u){const c=s("seo-statistics-overview"),p=s("graph"),d=s("core-card"),k=s("posts-table"),x=s("grid-column"),C=s("grid-row"),T=s("core-blur");return l(),m(T,null,{default:o(()=>[y("div",I,[i(C,null,{default:o(()=>[i(x,null,{default:o(()=>[i(d,{class:"aioseo-seo-statistics-card",slug:"seoPerformance","header-text":t.strings.seoStatisticsCard,toggles:!1,"no-slide":""},{tooltip:o(()=>[y("span",{innerHTML:t.strings.seoStatisticsTooltip},null,8,V)]),default:o(()=>[i(c,{statistics:["impressions","clicks","ctr","position"],"show-graph":!1,view:"side-by-side"}),i(p,{"multi-axis":"",series:u.series,"legend-style":"simple"},null,8,["series"])]),_:1},8,["header-text"]),i(d,{slug:"posts","header-text":t.strings.contentCard,toggles:!1,"no-slide":""},{tooltip:o(()=>[h(_(t.strings.postsTooltip),1)]),default:o(()=>{var g,S,$;return[i(k,{posts:(($=(S=(g=r.searchStatisticsStore.data)==null?void 0:g.seoStatistics)==null?void 0:S.pages)==null?void 0:$.paginated)||t.defaultPages,columns:["postTitle","seoScore","clicksSortable","impressionsSortable","positionSortable","diffPositionSortable"],"show-items-per-page":"","show-table-footer":""},null,8,["posts"])]}),_:1},8,["header-text"])]),_:1})]),_:1})])]),_:1})}const D=f(H,[["render",z]]);const M={components:{Blur:D,Cta:N,RequiredPlans:R},data(){return{strings:{ctaButtonText:this.$t.sprintf(this.$t.__("Upgrade to %1$s and Unlock Search Statistics",this.$td),"Pro"),ctaHeader:this.$t.sprintf(this.$t.__("Search Statistics is only for licensed %1$s %2$s users.",this.$td),"AIOSEO","Pro"),ctaDescription:this.$t.__("Connect your site to Google Search Console to receive insights on how content is being discovered. Identify areas for improvement and drive traffic to your website.",this.$td),thisFeatureRequires:this.$t.__("This feature requires one of the following plans:",this.$td),feature1:this.$t.__("Search traffic insights",this.$td),feature2:this.$t.__("Track page rankings",this.$td),feature3:this.$t.__("Track keyword rankings",this.$td),feature4:this.$t.__("Speed tests for individual pages/posts",this.$td)}}}},F={class:"aioseo-search-statistics-seo-statistics"};function j(e,a,n,r,t,u){const c=s("blur"),p=s("required-plans"),d=s("cta");return l(),v("div",F,[i(c),i(d,{"cta-link":e.$links.getPricingUrl("search-statistics","search-statistics-upsell","seo-statistics"),"button-text":t.strings.ctaButtonText,"learn-more-link":e.$links.getUpsellUrl("search-statistics","seo-statistics","home"),"feature-list":[t.strings.feature1,t.strings.feature2,t.strings.feature3,t.strings.feature4],"align-top":""},{"header-text":o(()=>[h(_(t.strings.ctaHeader),1)]),description:o(()=>[i(p,{"core-feature":["search-statistics","seo-statistics"]}),h(" "+_(t.strings.ctaDescription),1)]),_:1},8,["cta-link","button-text","learn-more-link","feature-list"])])}const b=f(M,[["render",j]]),J={mixins:[A],components:{SeoStatistics:b,Lite:b}},K={class:"aioseo-search-statistics-seo-statistics"};function Q(e,a,n,r,t,u){const c=s("seo-statistics",!0),p=s("lite");return l(),v("div",K,[e.shouldShowMain("search-statistics","seo-statistics")?(l(),m(c,{key:0})):w("",!0),e.shouldShowUpgrade("search-statistics","seo-statistics")||e.shouldShowLite?(l(),m(p,{key:1})):w("",!0)])}const At=f(J,[["render",Q]]);export{At as default};
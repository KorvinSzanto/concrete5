(function(){!function(a){var n;return n=function(a,n){return n?a.removeClass("fa-star-o").addClass("fa-star"):a.removeClass("fa-star").addClass("fa-star-o")},a.fn.awesomeStarRating=function(r){var t,e,s,i,o,f,l,u,c,d;for(null==r&&(r=[]),e=this,o=r.name||e.attr("data-name")||"rating_score",l=parseInt(r.score||e.attr("data-score")||0),f=r.onChange,t=a("<input />",{type:"hidden",value:l,name:o}),e.append(t),u=[],d=[],i=c=1;c<=5;i=++c)s=a('<i class="fa"></i>'),n(s,i<=l),e.append(s),u[i]=s,d.push(s.on("click",{idx:i},function(a){var r,s,i;for(r=a.data.idx,t.val(r),s=i=1;i<=5;s=++i)n(u[s],s<=r);if(f)return f.call(e,r)}));return d}}(jQuery)}).call(this);
!function(a){"use strict";var b={common:{init:function(){},finalize:function(){}},home:{init:function(){}}},c={fire:function(a,c,d){var e=b;c=void 0===c?"init":c,""!==a&&e[a]&&"function"==typeof e[a][c]&&e[a][c](d)},loadEvents:function(){c.fire("common"),a.each(document.body.className.replace(/-/g,"_").split(/\s+/),function(a,b){c.fire(b)}),c.fire("common","finalize")}};a(document).ready(c.loadEvents)}(jQuery);
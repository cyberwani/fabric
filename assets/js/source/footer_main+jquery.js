// Based on its naming convention, this file will automatically be enqueued in the footer, and uses jQuery as a dependancy
// Modified http://paulirish.com/2009/markup-based-unobtrusive-comprehensive-dom-ready-execution/
// Only fires on body class (working off strictly WordPress body_class)

(function($) {

  "use strict";
  
  var ExampleSite = {
    // All pages
    common: {
      init: function() {
        // JS here
      },
      finalize: function() { }
    },
    // Home page
    home: {
      init: function() {
        // JS here
      }
    }
  };

  var UTIL = {
    fire: function(func, funcname, args) {
      var namespace = ExampleSite;
      funcname = (funcname === undefined) ? 'init' : funcname;
      if (func !== '' && namespace[func] && typeof namespace[func][funcname] === 'function') {
        namespace[func][funcname](args);
      }
    },
    loadEvents: function() {

      UTIL.fire('common');

      $.each(document.body.className.replace(/-/g, '_').split(/\s+/),function(i,classnm) {
        UTIL.fire(classnm);
      });

      UTIL.fire('common', 'finalize');
    }
  };

  $(document).ready(UTIL.loadEvents);

})(jQuery);
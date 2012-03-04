/*  */

var playSound = function(sid) {
  var url = '/dyn/CAPTCHA/Sound?sid='+sid;
  if (document.all && !window.opera && /*@cc_on!@*/false) {
    var el = document.createElement('bgsound');
    document.body.appendChild(el);
    el.src = url;
  } else {
    var el = document.createElement('iframe');
    el.width = 0;
    el.height = 0;
    el.frameBorder = 0;
    el.src = url;
    document.body.appendChild(el);          
  }
};

(function() {

  var uniqueId = "captcha" + Math.round(Math.random()*100000);

  var findFormParent = function(el) {
    while (el = el.parentNode) {
      if (el.tagName && el.tagName.toLowerCase() == 'form') {
        return el;
      }
    }
    return false;
  };
  
  var blankFunction = function() {};
  var getCaptcha = function() {
    var span = document.getElementById(uniqueId);
    if (!span) {
      throw new Error('could not find captcha span when trying to insert captcha');
    }
    
    new FORK.Ajax('GET', '/dyn/CAPTCHA/CAPTCHAAJAXDELAY', {
      on200: function(xhr) {
        span.innerHTML = xhr.responseText;
      }, 
      onComplete: function(xhr) {
        span.innerHTML = "we could not find the captcha";
      }
    })
    
    span.innerHTML = 'captcha'
    getCaptcha = blankFunction;
  };
  
  var getForm = function() {
    var span = document.getElementById(uniqueId);
    if (!span) {
      throw new Error('could not find captcha span');
    }
    var form = findFormParent(span);
    if (!form) {
      throw new Error('could not find form ancestor of captcha span');
    }
    return form;
  };
  
  var initCaptcha = function() {
    var form = getForm()
    var inputs = form.elements;
    for (var i=0, ilen=inputs.length; i<ilen; i++) {
        var input = inputs[i];
        if ((input.tagName.toLowerCase() == 'input' && input.type.toLowerCase() == 'text') ||
            input.tagName.toLowerCase() == 'textarea') {
          FORK.Event.addListener(input, 'focus', function() {
            getCaptcha();
          });
        }
    }
  };

  FORK.Event.addListener(window, 'load', function() {
    // if there is already text in the submission story textarea
    // then the user may have made a captcha mistake. So get the captcha immediately
    if (document.forms.htmlForm2.elements.submission_text.value.length > 0) {
      getCaptcha();
    }
    else {
      initCaptcha();    
    }
  });

  document.write('<div id="'+uniqueId+'"></div>');

})();

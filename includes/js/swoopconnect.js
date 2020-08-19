class SwoopConnect {
  constructor(iframeSource) {
    this.iframeSource = iframeSource || 'http://localhost:3000/auth/swoop';
  }

  iFrame(element, propertyInfo) {
    var self = this;
    return new Promise((resolve, reject) => {
      // Remove an existing iframe
      if(self.iframe) {
        self.iframe.parentNode.removeChild(self.iframe);
        self.iframe = null;
      }

      var propertyObj = btoa(JSON.stringify(propertyInfo));      
      self.iframe = document.createElement('iframe');
      self.iframe.setAttribute('src', this.iframeSource + '?state=' + propertyObj);
      self.iframe.setAttribute('id', 'swoop_frame');
      self.iframe.style.width = 600 + 'px';
      self.iframe.style.height = 600 + 'px';
      element.innerHTML = "";
      element.appendChild(this.iframe);

      // Listen to message from child window
      let that = self;
      self.bindEvent(window, 'message', function(e) {
        that.iframe.parentNode.removeChild(that.iframe);
        that.iframe = null;
        let property = JSON.parse(e.data);
        that.unbindEvent(window,'message',this);
        resolve(property);
      });
    });
  }

  // Private
  bindEvent(element, eventName, eventHandler) {
    if(element.addEventListener) {
      element.addEventListener(eventName, eventHandler, false);
    } else if(element.attachEvent) {
      element.attachEvent('on' + eventName, eventHandler);
    }
  }

  unbindEvent(element, eventName, eventHandler) {
    if(element.removeEventListener) {
      element.removeEventListener(eventName, eventHandler, false);
    } else if(element.detachEvent) {
      element.detachEvent(eventName, eventHandler);
    }
  }
}

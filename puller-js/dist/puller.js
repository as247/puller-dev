function _classCallCheck(instance, Constructor) {
  if (!(instance instanceof Constructor)) {
    throw new TypeError("Cannot call a class as a function");
  }
}
function _defineProperties(target, props) {
  for (var i = 0; i < props.length; i++) {
    var descriptor = props[i];
    descriptor.enumerable = descriptor.enumerable || false;
    descriptor.configurable = true;
    if ("value" in descriptor) descriptor.writable = true;
    Object.defineProperty(target, _toPropertyKey(descriptor.key), descriptor);
  }
}
function _createClass(Constructor, protoProps, staticProps) {
  if (protoProps) _defineProperties(Constructor.prototype, protoProps);
  if (staticProps) _defineProperties(Constructor, staticProps);
  Object.defineProperty(Constructor, "prototype", {
    writable: false
  });
  return Constructor;
}
function _extends() {
  _extends = Object.assign ? Object.assign.bind() : function (target) {
    for (var i = 1; i < arguments.length; i++) {
      var source = arguments[i];
      for (var key in source) {
        if (Object.prototype.hasOwnProperty.call(source, key)) {
          target[key] = source[key];
        }
      }
    }
    return target;
  };
  return _extends.apply(this, arguments);
}
function _toPrimitive(input, hint) {
  if (typeof input !== "object" || input === null) return input;
  var prim = input[Symbol.toPrimitive];
  if (prim !== undefined) {
    var res = prim.call(input, hint || "default");
    if (typeof res !== "object") return res;
    throw new TypeError("@@toPrimitive must return a primitive value.");
  }
  return (hint === "string" ? String : Number)(input);
}
function _toPropertyKey(arg) {
  var key = _toPrimitive(arg, "string");
  return typeof key === "symbol" ? key : String(key);
}

/**
 * Ajax client which try to use fetch api if available, then fallback to XMLHttpRequest.
 */
var Client = /*#__PURE__*/function () {
  function Client() {
    _classCallCheck(this, Client);
  }
  _createClass(Client, [{
    key: "post",
    value: function post(url, fields, options) {
      return this.request({
        method: 'POST',
        url: url,
        data: fields,
        headers: options ? options.headers : {}
      });
    }
  }, {
    key: "get",
    value: function get(url, options) {
      return this.request({
        method: 'GET',
        url: url,
        data: null,
        headers: options ? options.headers : {}
      });
    }
  }, {
    key: "request",
    value: function request(options) {
      var _this = this;
      return new Promise(function (resolve, reject) {
        //Default headers content type to json
        options.headers = options.headers || {};
        options.headers['Content-Type'] = options.headers['Content-Type'] || 'application/json';
        if (options.headers['Content-Type'] === 'application/json' && typeof options.data !== 'string') {
          options.data = JSON.stringify(options.data);
        }
        if (typeof fetch === 'function') {
          fetch(options.url, {
            method: options.method,
            body: options.data,
            headers: options.headers
          }).then(function (response) {
            if (response.status >= 200 && response.status < 400) {
              resolve(response.json());
            } else {
              reject(response.json());
            }
          })["catch"](function (error) {
            reject(error);
          });
        } else {
          var request = new XMLHttpRequest();
          request.open(options.method, options.url, true);
          for (var name in options.headers) {
            request.setRequestHeader(name, options.headers[name]);
          }
          request.onload = function () {
            var response = _this.parseJson(request.responseText);
            if (request.status >= 200 && request.status < 400) {
              resolve(response);
            } else {
              reject(response);
            }
          };
          request.onerror = function () {
            var response = _this.parseJson(request.responseText);
            reject(response);
          };
          try {
            request.send(options.data);
          } catch (e) {
            reject(e);
          }
        }
      });
    }
  }, {
    key: "parseJson",
    value: function parseJson(response) {
      try {
        return JSON.parse(response);
      } catch (e) {
        return response;
      }
    }
  }]);
  return Client;
}(); //Export instance of Client
var client = new Client();

var Channel = /*#__PURE__*/function () {
  /**
   * Create a new class instance.
   */
  function Channel(name, options) {
    _classCallCheck(this, Channel);
    this._defaultOptions = {
      error_delay: 10000,
      url: '/puller/messages',
      userAuthentication: {
        endpoint: '/broadcasting/user-auth',
        headers: {}
      }
    };
    this.started = false;
    this.stopped = false;
    this.name = name;
    //merge with default options
    this.options = _extends(this._defaultOptions, options);
  }
  /**
   * Listen for an event on the channel instance.
   */
  _createClass(Channel, [{
    key: "listen",
    value: function listen(event, callback) {
      this.events = this.events || {};
      this.events[event] = callback;
      this.start();
      return this;
    }
  }, {
    key: "start",
    value: function start() {
      var _this = this;
      if (!this.started) {
        this.started = true;
        this.stopped = false;
        if (this.isPrivate()) {
          this.auth().then(function (response) {
            _this.loop();
          });
        } else {
          this.loop();
        }
      }
    }
  }, {
    key: "stop",
    value: function stop() {
      this.stopped = true;
      this.started = false;
    }
  }, {
    key: "auth",
    value: function auth() {
      var _this2 = this;
      //get token from server and return promise
      return new Promise(function (resolve, reject) {
        client.post(_this2.options.userAuthentication.endpoint, {
          channel: _this2.name
        }, {
          headers: _this2.options.userAuthentication.headers
        }).then(function (response) {
          if (response.token) {
            _this2.token = response.token;
            resolve(response);
          }
        })["catch"](function (error) {
          reject(error);
        });
      });
    }
  }, {
    key: "isPrivate",
    value: function isPrivate() {
      //check if channel is private by checking prefix 'private'
      return this.name.indexOf('private') === 0;
    }
  }, {
    key: "loop",
    value: function loop() {
      var _this3 = this;
      if (this.stopped) {
        return;
      }
      var startTimestamp = new Date().getTime();
      client.post(this.options.url, {
        channel: this.name,
        token: this.token
      }).then(function (response) {
        if (response.messages) {
          response.messages.forEach(function (message) {
            if (_this3.events[message[0]]) {
              _this3.events[message[0]](message[1]);
            }
            if (_this3.events['*']) {
              _this3.events['*'](message[1], message[0]);
            }
          });
        }
        if (response.token) {
          _this3.token = response.token;
          _this3.loop();
        } else {
          setTimeout(function () {
            _this3.loop();
          }, Math.max(0, (_this3.options.error_delay || 10000) - (new Date().getTime() - startTimestamp)));
        }
      })["catch"](function (error) {
        setTimeout(function () {
          _this3.loop();
        }, Math.max(0, (_this3.options.error_delay || 10000) - (new Date().getTime() - startTimestamp)));
      });
    }
  }]);
  return Channel;
}();

/**
 * This class is the primary API for interacting with broadcasting.
 */
var Puller = /*#__PURE__*/function () {
  function Puller(options) {
    _classCallCheck(this, Puller);
    this.options = options;
  }
  _createClass(Puller, [{
    key: "channel",
    value: function channel(_channel) {
      return new Channel(_channel, this.options);
    }
  }]);
  return Puller;
}();

export { Puller as default };

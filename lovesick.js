// Polyfill for Function.prototype.bind
var isFunction = function(o) {
  return typeof o == 'function';
};

var bind,
  slice = [].slice,
  proto = Function.prototype,
  featureMap;

featureMap = {
  'function-bind': 'bind'
};

function has(feature) {
  var prop = featureMap[feature];
  return isFunction(proto[prop]);
}

// check for missing features
if (!has('function-bind')) {
  // adapted from Mozilla Developer Network example at
  // https://developer.mozilla.org/en/JavaScript/Reference/Global_Objects/Function/bind
  bind = function bind(obj) {
    var args = slice.call(arguments, 1),
      self = this,
      nop = function() {
      },
      bound = function() {
        return self.apply(this instanceof nop ? this : (obj || {}), args.concat(slice.call(arguments)));
      };
    nop.prototype = this.prototype || {}; // Firefox cries sometimes if prototype is undefined
    bound.prototype = new nop();
    return bound;
  };
  proto.bind = bind;
}

var page = require('webpage').create(),
    system = require('system'),
    fs = require('fs'),
    q = require('./queue.js');

if (system.args.length !== 3) {
    console.log('Missing username and password');
    phantom.exit();
}

var email = system.args[1];
var password = system.args[2];

page.onConsoleMessage = function(message) {
    if (message.indexOf('selfxss') < 0) {
        console.log(message);
    }
};

var url = 'https://www.facebook.com';

page.open(url + '/login.php');

page.onLoadFinished = function () {
    var path = page.url.replace(url, '');

    console.log(path + ' :: ' + page.title);

    if (path.indexOf('sk=friends&list=1') > -1) {
        setTimeout(function () {
            console.log('Rendering screenshot');
            page.render('friends.png');
            console.log('Finished rendering screenshot')
        }, 0);

        var onFinishedScrolling = function () {
            console.log('Selecting friends');
            var links = page.evaluate(function() {
                var friends = document.querySelectorAll('._698');
                console.log(friends.length);
                console.log(JSON.stringify(friends));

                var links = [];

                for (var i = 0; i < friends.length; i++) {
                    var item = friends.item(i);
                    var anchor = item.querySelector('a');

                    var href = anchor.getAttribute('href');

                    if (href != '#') {

                        if (href.indexOf('profile.php') > -1) {
                            href += '&sk=about&section=relationship';
                        } else {
                            href = href.split('?')[0];
                            href += '/about?section=relationship';
                        }

                        links.push(href);
                    }
                }

                console.log(links.length + ' links');

                return links;
            });

            getRelationshipStatusForFriends(links);
        };

        var scroll = function () {

            var pageSize = getPageSize(page);
            page.scrollPosition = {
                top: pageSize.height,
                left: 0
            };

            console.log('Scrolling to ' + pageSize.height);

            setTimeout(function () {
                var newPageSize = getPageSize(page);

                if (newPageSize.height > pageSize.height) {
                    scroll();
                } else {
                    console.log('Finished scrolling');
                    onFinishedScrolling();
                }
            }, 1000);
        };

        scroll();
        return;
    }

    switch (path) {
        case '/login.php':
            login(page);
            break;

        case '/':
            goToFriendsList(page);
            break;
    }

};

var login = function (page) {
    page.evaluate(function(email, password) {
        document.querySelector("input[name='email']").value = email;
        document.querySelector("input[name='pass']").value = password;
        document.querySelector("#login_form").submit();

        console.log("Login submitted!");
    }, email, password);
};

var goToFriendsList = function (page) {
    page.open(url + '/friends');
};

var getPageSize = function (page) {
    return {
        width: page.evaluate(function() { return document.body.offsetWidth }),
        height: page.evaluate(function() { return document.body.offsetHeight })
    };
};

var getRelationshipStatusForFriends = function (links) {
    var friends = {};

    for (var i in links) {
        friends[links[i]] = {
            fetched: false,
            in_relationship: false
        };
    }

    // Create jobs
    var queue = q.queue('friends');

    for (var url in friends) {
        var friend = friends[url];

        var job = q.job(function (url, friend) {
            var _this = this;

            setTimeout(function () {
                var page = require('webpage').create();
                page.open(url, function () {
                    var inRelationship = page.evaluate(function () {
                        var relationship = document.querySelector('[data-pnref="rel"]');
                        return relationship != null;
                    });

                    console.log('Loaded: ', url);

                    friend.fetched = true;
                    friend.in_relationship = inRelationship;

                    page.close();
                    _this.finish();
                });
            }, 0);
        }, url, friend);

        queue.add(job);
    }

    queue.setOnCompleteListener(function () {
        fs.write('friends.json', JSON.stringify(friends), 'w');
        phantom.exit();
    });

    queue.start();
};

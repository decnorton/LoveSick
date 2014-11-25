var fs = require('fs');
var path = require('path');
var childProcess = require('child_process');
var phantomjs = require('phantomjs');
var binPath = phantomjs.path;
var prompt = require('prompt');
var notifier = require('node-notifier');
var argv = require('optimist').argv;

var stats = function () {
    var results = fs.readdirSync('./results');
    console.log(results);

    // Global stats
    var total = 0;
    var single = 0;
    var inRelationship = 0;
    var openRelationship = 0;
    var married = 0;
    var engaged = 0;
    var itsComplicated = 0;
    var civilPartnership = 0;
    var separated = 0;
    var divorced = 0;
    var widowed = 0;

    // Timestamps
    var since = [];

    for (var i in results) {
        if (results[i].indexOf('json') == -1 && results[i].indexOf('all.json') == -1)
            continue;

        var friends = require('./results/' + results[i]);

        for (var i in friends) {
            var friend = friends[i];
            total++;

            if (friend.in_relationship.length > 0) {
                var relationship = friend.in_relationship.toLowerCase();

                if (relationship.indexOf('single') > -1) {
                    single++;
                } else {
                    if (relationship.indexOf('married') > -1) {
                        married++;
                    } else if (relationship.indexOf('engaged') > -1) {
                        engaged++;
                    } else if (relationship.indexOf('complicated') > -1) {
                        itsComplicated++;
                    } else if (relationship.indexOf('civil') > -1) {
                        civilPartnership++;
                    } else if (relationship.indexOf('open') > -1) {
                        openRelationship++;
                    } else if (relationship.indexOf('separated') > -1) {
                        separated++;
                    } else if (relationship.indexOf('divorced') > -1) {
                        divorced++;
                    } else if (relationship.indexOf('widowed') > -1) {
                        widowed++;
                    } else {
                        inRelationship++;
                    }

                    if (relationship.indexOf('since') > -1) {
                        var friendSince = relationship.split('since')[1].trim();
                        since.push(friendSince);
                    }
                }
            }
        }
    }


    console.log('Total: ' + total);

    console.log('Single: ' + single);
    console.log('In relationship: ' + inRelationship);
    console.log('Open relationship: ' + openRelationship);
    console.log('Married: ' + married);
    console.log('Engaged: ' + engaged);
    console.log('It\'s complicated: ' + itsComplicated);
    console.log('Civil partnership: ' + civilPartnership);
    console.log('Separated: ' + separated);
    console.log('Divorced: ' + divorced);
    console.log('Widowed: ' + widowed);

    var totalInRelationship = inRelationship + openRelationship + married + engaged + itsComplicated + civilPartnership;
    var percentageInRelationship = Math.round((totalInRelationship / total) * 100);
    console.log('\n');
    console.log('Percentage in relationships: ' + percentageInRelationship + '%');
};

if (!argv.stats) {
    var schema = {
        properties: {
            email: {
                required: true
            },
            password: {
                hidden: true,
                required: true
            }
        }
    };

    // Start the prompt
    prompt.start();

    // Get two properties from the user: email, password
    prompt.get(schema, function (err, result) {
        if (result) {
            // Log the results.
            startPhantom(result.email, result.password);
        } else {
            console.error(err);
        }
    });

    var startPhantom = function (email, password) {
        var childArgs = [
          path.join(__dirname, 'lovesick.phantom.js'),
          email,
          password
        ];

        var process = childProcess.execFile(binPath, childArgs, function(err, stdout, stderr) {
            console.log(err, stderr);
            notifier.notify({
              'title': 'LoveSick finished'
            });

            stats();
        });

        process.stdout.setEncoding('utf8');
        process.stdout.on('data', function(data) {
            var str = data.toString();
            var lines = str.split(/(\r?\n)/g);

            for (var i = 0; i < lines.length; i++) {
                var line = lines[i];
                // Process the line, noting it might be incomplete.

                if (line.trim().length > 0) {
                    console.log(lines[i]);
                }
            }
        });
    };
} else {
    stats();
}
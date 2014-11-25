var json = require('./results/all.json');
var fs = require('fs');

var csv = ',Relationship Status,Since\n';
var i = 0;

for (var key in json) {
    var relationship = json[key].in_relationship;
    var since = null;
    if (relationship.indexOf('since') > -1) {
        var parts = relationship.split('since');
        relationship = parts[0].trim();
        since = parts[1].trim();
    }

    csv += i + ',' + relationship + ',' + (since || '') + '\n';
    i++;
}

fs.writeFile('./results/all.csv', csv);

console.log(csv);
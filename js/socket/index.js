var xml2object = require('xml2object');
var request = require('request');

var parser = new xml2object(['feed']);

parser.on('object', function(name, obj){
	console.log('Found an object %s', name);
	console.log(obj);
});

parser.on('end', function(){
	console.log('Finished parsing xml');
});

request.get('http://feeds.esmas.com/data-feeds-esmas/applicaster/prog_dep.xml').pipe(parser.saxStream);
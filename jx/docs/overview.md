

## JS-XML (JX) Translation Module

JX is a PHP-based module for translating between
JavaScript- and XML-based data formats such as JSON, JSONP,
RSS 2.0, and generic XML.

In this document, "JS" refers to either of the JavaScript-
based formats (JSON and JSONP), and "XML" refers to any
XML-based format (including RSS).

### Requirements

The JX design balances competing requirements. It must be:

 1. Very quickly completed.
 2. General, allowing translation from any supported format to any supported format
    (modulo the semantic information available in the input).
 2. Allow the contents and structure of the input to be modified during
    the conversion.
 2. Reasonably flexible, so that different
    formats (including ones not currently used and future ones not yet even
    specified) can be accommodated.
 3. Developed in PHP, using as as many built-in or close-at-hand libraries
    as possible.

### Recipe-based Design

As a result, JX utilizes a recipe-based approach. Default rules and functions
can convert the simple *syntax* of JS files to and from XML files.

More useful
conversions, however, such as JSON-to-RSS, require genuine *semantic*
conversion. There is no general-purpose way to do this other than understanding
what's in the input, and how that maps to the desired output. Thus recipes.
Recipes are a set of specific transformation steps for converting from input
*X* to output *Y*.

The downside is that you will need a recipe for every semantically-transforming
conversion you wish to do--indeed, two of them, if you wish the translation to
be bidirectional. In theory, that will add up to a *lot* of individual recipes.

While true,
this issue is mitigated in several ways:

 *  There is no simple alternative that also promises quality semantic conversion.
 *  A supportive library of default techniques greatly reduces the complexity
    and length of each recipe. Default rules convert data
    fields for which there is a direct or 1:1 relationship between input and output.

    Fields which must be renamed or transformed during conversion are more fully
    specified, in PHP.

The traversal of fields from input to output, and the processing scheme,
resembles that of recursive-descent parsers widely used in programming language
compilers.

There are a number of commonalities among the way JS and XML represent data, but
their models is also genuinely different. XML, for example:

 *  offers
    defined (though quasi-optional) document types and schema;
 *  supports multiple 'namespaces' for element
    and attribute definitions;
 *  names every level of a structure with
    a specific element tag;
 *  gives the option of encoding data either within
    elements (as attributes), or as the text value of an element; and
 *  entertains
    the possibility of "mixed" documents in which structure and content are
    interwoven.

JavaScript formats (JSON and JSONP), in contrast, are:

 *  simpler;
 *  have several types XML does not, e.g. integers, maps, and floating point numbers,
    where XML contents are technically all strings;
 *  there are no namespaces;
 *  there are no element attributes, therefore no optional using them or not;
 *  there are no official document type definitions or schema, so any
    specification of document contents are a totally optional and separate affair;
 *  have many anonymous structure levels;
 *  do not allow mixed documents.

Any recipe system to convert between these will have to bridge these specific
gaps. Many of them have idiomatic translations, but the scale of the differences
mean that even at a syntax level, there are many variations.

JX provides foundation functions to make many of
these bridges easily stated, but some custom code is inevitable.

Another reality is that recipes are one-way. That is, a recipe for converting
from a content definition in JS to an equivalent RSS/XML file does not provide
the means for converting from the RSS/XML file to the equivalent JS notation.

Though two recipes are needed for bi-directional translation, it's recommended
that you develop and manage these recipes
side-by-side. The knowledge of how to convert in one direction will help
inform development of how to convert in the other.

### Common Issues

 * **Field encoding** Often data fields will have different ways of being
   encoded in the JS and XML realms. It's not uncommon for XML content to be
   wrapped in a CDATA section (`<![CDATA[something]]>`), for example. But that
   wrapper has no meaning in the JS world, and should be stripped. JS data may
   "escape" parts of its content fields, for example
   `href='http:\/\/www.facebook.com\/TelevisaDeportes'`. The "leaning toothpick"
   structures (`\/`) should perhaps be removed, or possibly re-encoded in an
   XML/HTML idiom, as data is converted to XML.

 * **Anonymous vs Named Arrays** JS uses anonymous array items, accessed
   via integer index. XML uses specific item elements.

        { "tracks": [ { "name": "One" }, { "name": "Two"} ] }

    in XML might be:

        <tracks>
            <track>
                <name>One</name>
            </track>
            <track>
                <name>Two</name>
            </track>
        </tracks>

    or in some cases:

        <tracks>
            <track name="One" />
            <track name="Two" />
        </tracks>

 *  **Different Tag Name Rules** JS structure keys are arbitrary strings. XML
    tags are identifiers, like variable names in programs, with a specific set
    of rules. They may not start with numbers or punctuation, for instance,
    making keys/tags such as "640x480" not legitimate, unlike in JS. Tags may
    not start with the letters "xml" either, and certain characters such as
    colons (":") have specific use in defining namespaces.

### Implementation Issues

 *  PHP contains multiple mechanisms for dealing with XML, including
    `DOMDocument` and `SimpleXML`. These are fundamentally different facilities,
    with different APIs and capabilities. `SimpleXML` objects can be
    conveniently parsed out of XML strings, and can be converted to JSON via
    `encode_json()`. They cannot be meaningfully altered or edited, however.
    Luckily, they can be imported into `DOMDocument` objects. `DOMDocument`
    objects are nominally editable, albeit at a very low level that requires
    notable care and considerable effort.

 *  XML contains the XPath query language for easily accessing components of a
    document. Reasonably good support is bundled with both of PHP's major XML
    mechanisms. There is a corresponding
    [JSONPath](http://goessner.net/articles/JsonPath/), but it's not a standard,
    it's different from XPath, and
    and not a standard part of PHP. Accessing and manipulating XML and JS
    structures is therefore fundamentally asymmetric. (A PHP implementation
    of JSONPath is contained within JX, but it's strictly experimental;
    it is not yet
    integrated or used.)

 *  PHP structures for object-style property access and array-style indexed
    access are not well-unified. It sometimes seems that one form or the other
    should be usable, but it's not. There is a very specific option to
    `json_decode()`, for example, that asks it to return an object that supports
    one style of access or the other.

 *  Given the above distinct domains of data access, it sometimes makes sense to
    modify data in one form or another. It is also common in format
    transformations that it's convenient to make modifications in one format or
    another. This can lead to converting a document through several steps, and
    even converting back and forth between formats, to accomplish a complete
    transformation. While this is not a high-performance approach, strictly
    speaking, there's always a tradeoff between programmer time and
    time-to-market and strict computer performance. And in most cases, even
    doing full document conversions a few times, you're still dealing with
    sub-second processing times.

### Structure

 * **I/O** The JX API assumes that a calling program will provide an input
   string directly, and accept a transformed string as a return value. When
   called as a script (as in a cron job), the script will accept a file path or
   source url as input, along with the name of the desired recipe; it
   will run the transformation and emit its output to the standard output.

 * **Recipe Discovery and Registration** For testing purposes, recipes can
   reside anywhere. As long as the calling program has done a `require file.php`
   (where `file.php` is the home of the recipe definition), the recipe will
   be available.
   But to be discovered by JX's command line function (`jxrun.php`),
   recipes must reside in the `jx/recipes` directory. When the recipe runner
   starts, it dynamically scans this directory and loads any recipe classes
   found there. They are then available for use in CLI transformations.

 * **Files** JX lives under the
   directory `jx`. Documentation is in `jx/docs`, specific recipes in `jx/recipes`,
   and tests in `jx/tests`. The remaining files are mostly php source files,
   with the occasional source directory.

    * `jxbase.php` Base code
    * `jxrun.php` Command line runner
    * `fdom.php`  Functional simplification of PHP's `DOMDocument`
    * `util.php` Utility functions

### Performance

Most of the low-level parsing and formatting of both JS and XML structures is
done by C-language libraries, which are among the fastest around. Therefore
conversion performance should be good. This is true even if a document
needs to be converted through several intermediate
stages/formats for programming convenience.

On a less theoretical level, a benchmark run of 7 input JS files on a
development laptop converted to XML 100 times each (700 total conversions) took
19.756 seconds. Doing the math, each conversion (including file input and
output) took an average of ~0.28 seconds. That translates into just over 35
conversions/second. While production platforms and real-world conditions may
vary, and while some recopies may impose slightly more workload, it seems
reasonable JX will be able to process dozens of files per second in production
runs.

Therefore, running JX-based conversions every 5, 10, or 15 minutes will produce
zero challenge. The time required to acquire feed data from network sources will
greatly outweigh the time required to convert feeds into alternate formats.

### Testing

Testing is managed in the `jx/tests` directory.

Files starting with `test_` are true unit tests. They are designed for,
and must be run from, the [phpunit](http://phpunit.de) testing
framework.

The more numerous files with `demo` in their name are also intended
to demonstrate that various JX functions work, and work correctly. They
may contain some assertions and checks, and are useful in JX development
and qualification. They lack,
however, the disciplined, fully-automated results checking
that would qualify them as true unit tests.

### Documentation

This documentation is available in the `jx/docs` directory, and is
written in the Markdown format.

### Command Line and Cron Job Usage

`jxrun.php` is JX's command-line runner. It takes two parameters:
`-i` to specify the input (file path or URL) and `-r` to specify the
desired recipe name. For example:

    php jxrun.php -i /path/to/gameinfo.json -r js2xml

would run the generic JS -> XML recipe on `gameinfo.json`. Output
is always sent to the standard output. Capture it or pipe it with
standard redirect or pipe operations (`>` or `|`).

It is very important that the input path have an absolute file path
at this time.

### Writing Recipes

A handful of general recipes for document conversion are provided.
Study these for insights into how to write further recipes.

#### JS -> XML

JS to XML is an easier conversion than the opposite, at least at a base level.
JS is the simpler encoding, so it presents simpler choices. To be sure, there
are things in JS that do not have direct XML equivalents (notably anonymous
arrays and scalar values such as numbers (ints and floats), booleans, and the
`null` value). Conversion programs must decide the best ways to encoding those
in XML. But, good news: There are reasonably clean ways of doing so that seem
sufficiently idoimatic to both JS and XML practitioners.

It is possible to produce custom recipes for each JS schema that will very
precisely produce XML. The `js2tracks` recipe in `recipes/js2tracks.php` is one
such example; it converts a simple list of music tracks.
`tests/js2tracks_demo.php` shows how it is used.

The generic `js2xml` recipe in `recipes/js2xml.php` shows how general
conversions can be made, regardless of input schema. See `tests/test_js2xml.php`
for a case study in how recipes are used and adjusted. It steps through the
stock conversion, then adds custom list item tags, and then custom
post-processing of element text.


TBD: explain array hoisting
TBD: explain custom value transforms


#### : XML -> JS

XML to JS is the more challenging direction, as there is more
complexity and fluidity in the input structure (especially
element attributes, namespaces, and entities), and because
there are no direct ways to translate some XML structures
(e.g. document type declarations, "mixed" content
containing interleaved text and elements). This sometimes happens
in JS to XML (e.g. anonymous array elements, non-string types),
but is much more pronounced going XML to JS.

Note, while there are easy ways in PHP to convert from XML to
JS through the use of `encode_json` and friends, they do not
handle any of these complexities. They will drop attributes
and other information in order to make their conversions.

Dropping information is a possible decision in any conversion
process, but it should be because a decision was made, not
because the tools simply and quietly threw the information away.
We will be more careful.

Therefore during the translation, choices will have to be made
about elements, attributes, and such are transformed. The core
of doing this automatically is a `NodeTransformer`. It accepts
an XML DOM node and amends a PHP data structure that will then
be later exported to JSON. Along the way, it must handle
attributes, child elements nodes, and text nodes.

For example, what should the following be translated as:

    <hotel type='premium'><name>Franklin<name><cost>140</cost></hotel>

A common approach might be:

    { "hotel": { "type": "premium",
                 "name": "Franklin",
                 "cost": "100" } }

Though this hoists the `cost` field to the same level
as the `type` field, it produces nicely idiomatic JS.

What about:

    <hotels>
        <hotel type='premium'><name>Franklin<name><cost>140</cost></hotel>
        <hotel type='economy'><name>Jones<name><cost>84</cost></hotel>
        <hotel type='economy'><name>Soman<name><cost>53</cost></hotel>
    </hotels>

The easy conversion would be:

    {
        "hotels": {
            "hotel": [
                {
                    "type": "premium",
                    "name": "Franklin",
                    "cost": "100"
                },
                {
                    "type": "economy",
                    "name": "Jones",
                    "cost": "84"
                },
                {
                    "type": "economy",
                    "name": "Soman",
                    "cost": "53"
                }
            ]
        }
    }

But that is not idiomatic JS. Better would be to collapse the "hotel" level:

    {
        "hotels": [
            {
                "type": "premium",
                "name": "Franklin",
                "cost": "100"
            },
            {
                "type": "economy",
                "name": "Jones",
                "cost": "84"
            },
            {
                "type": "economy",
                "name": "Soman",
                "cost": "53"
            }
        ]
    }

Because this is more idiomatic and closer to what JS consumers will expect.

The trick to doing such transforms easily is having a `NodeTransformer` that
is prepared to make such conversions simply by specifying (rather than coding)
them. And happily, we do.

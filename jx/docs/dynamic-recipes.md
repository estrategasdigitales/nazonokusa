
Based on discussions on July 28, it's become clear that ED
seeks a much more dynamic format converter than I had
previously understood.

Users should be able to specify which fields in an input
document they want mapped to which fields in the output
document, and they should be able to change those choices
day by day.

JX can support that, though it is a more sophisticated mode
of operation. The updates I have been making to the JX
helpers and machinery were intended to make it easier for
programmers to create custom recipes. Now we must take it
up another level, so that users may specify these conversions
from a graphical or web user interface.

The basic JX recipe idea works. It's just that now the
recipes are not just encoded in PHP programs, but must
be storable in a database or other repository, and
executable on the fly.

Designing that GUI or WUI is ED's responsibility. JX's
responsibility is executing conversion recipes.

Where we meet in the middle is that we need a specification
language for the dynamic recipes. I propose we develop a
JSON-based schema.

A key requirement is that the choices the user has made--such
as "field `publidate` will renamed `pubDate` in the output"--be
easily specified.

A first example:

    {
        "recipe": {
            "name": "example",
            "description": "An example for discussion",
            "from-format": "JSONP",
            "to-format": "XML",
            "mappings": [
                {
                    "from": "/category/program[]/videos",
                    "to": "/resources"
                },
                {
                    "from": "/category/program[]/videos/[]",
                    "to": "/resources/resource",
                },
                {
                    "from": "/category/program[]/videos/[]/title",
                    "to": "/resources/resource/attributes/title",
                },
                {
                    "from": "/category/program[]/videos/[]/description",
                    "to:": "/resources/resource/attributes/description"
                },
                // ...and so on...
            ]
        }
    }

That type of schema-oriented description would work, but has
the downside of being quite low-level and verbose. Another
approach might be:

    {
        "category": [{
            "program": [{
                    "videos": [{
                            "title": "/resources/resource/attributes/title",
                            "description": "/resources/resource/attributes/description",
                            "publidate": "/resources/resource/attributes/pubDate",
                            "guid": "/resources/resource/attributes/description/external_id",
                            "urls": {
                                "m3u8": "/resources/resource/attributes/enabled",
                            }
                        },
            // ... and so on ...

This type of format is much less verbose. Only fields that the user wants to be
mapped into the output format need actually be mentioned. We would probably need
some way of specifying that "this data goes into this attribute, rather than into
a pure XML node," or "this data should be wrapped as CDATA." Other types of
special processing might be needed.

But make no mistake, whatever the format for dynamic recipes we choose, there must be a format.
The front end user interface must be able to communicate with the back end transformation
engine to tell the converters what the user wanted.
#!/bin/bash

for f in test_*.php
do
    echo
    echo -n "$f "
    phpunit $f | egrep -v "Sebastian Bergmann|Time:|^\.+$|^\s*$"
done

echo
echo "DONE"
echo

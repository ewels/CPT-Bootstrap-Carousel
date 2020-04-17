#!/usr/bin/env python
import re
import sys

if len(sys.argv) != 2:
    print("Usage: md_to_wp.py README.md (outputs readme.txt)")
    sys.exit(1)

# Load the GitHub markdown
with open(sys.argv[1], 'r') as fh:
    wp_readme = fh.read()

# Convert headers
wp_readme = re.sub(re.compile(r"^###(.*)$", re.MULTILINE), r"=\1 =", wp_readme)
wp_readme = re.sub(re.compile(r"^##(.*)$", re.MULTILINE), r"==\1 ==", wp_readme)
wp_readme = re.sub(re.compile(r"^#(.*)$", re.MULTILINE), r"===\1 ===", wp_readme)

# Remove images
wp_readme = re.sub(r'!\[.*\]\(.*\)', '', wp_readme)

# Code blocks
wp_readme = re.sub(r"```[^\n`]*(\n[^`]*\n)```", r"`\1`", wp_readme)

# Comment block delimiters
wp_readme = wp_readme.replace("<!--\n", '')
wp_readme = wp_readme.replace('-->', '')

# Multiple newlines
wp_readme = re.sub(r"\n\n+", "\n\n", wp_readme)

# Traling whitespace
wp_readme = re.sub(r" +\n", "\n", wp_readme)

with open('readme.txt', 'w') as fh:
    fh.write(wp_readme)

print("Finished! Wordpress readme ended up looking like this:\n\n\n\n{}".format(wp_readme))

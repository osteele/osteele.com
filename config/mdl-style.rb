# This is a configuration file for mdl.

all

# I don't believe in these
exclude_rule 'MD013' # Line length
exclude_rule 'MD014' # Dollar signs used before commands without showing output
exclude_rule 'MD026' # Trailing punctuation in header
exclude_rule 'MD029' # Ordered list item prefix
exclude_rule 'MD039' # Spaces inside link text

# These aren't compatible with Jekyll
exclude_rule 'MD002' # First header should be a top level header
exclude_rule 'MD041' # First line in file should be a top level header

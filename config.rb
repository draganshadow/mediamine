# Require any additional sass plugins here.
# require "bootstrap-sass"
require "bourbon"
require "sass-globbing"

# Set this to the root of your project when deployed:
http_path = "/"
css_dir = "public/assets/css"
sass_dir = "public/scss"
images_dir = "assets/images"
fonts_dir = "assets/fonts"

# You can select your preferred output style here (can be overridden via the command line):
# output_style = :expanded or :nested or :compact or :compressed
output_style = (environment == :production) ? :compressed : :expanded

# To enable relative paths to assets via compass helper functions. Uncomment:
relative_assets = false

# To disable debugging comments that display the original location of your selectors. Uncomment:
line_comments = false


# If you prefer the indented syntax, you might want to regenerate this
# project again passing --syntax sass, or you can uncomment this:
# preferred_syntax = :sass
# and then run:
# sass-convert -R --from scss --to sass sass scss && rm -rf sass && mv scss sass
preferred_syntax = :scss
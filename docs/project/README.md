<!-- @file Overview for maintaining and developing the Drupal Bootstrap project. -->
<!-- @defgroup -->
# Project Development

Generally speaking, these topics will not be very helpful to you unless you are
a maintainer for this project. If you're just curious about our process or even
want to help improve this aspect of the project, all suggestions will be
appreciated!

## Prerequisites
For development, this project relies heavily on NodeJS/Grunt to automate some
very time consuming tasks and to ensure consistent output. If you do not have
these CLI tools, please install them now:

* https://nodejs.org
* http://gruntjs.com

## Installation
This project's installation may initially take a while to complete. Please read
through the entire topic before continuing and be aware of what to expect.
Suffice it to say: you will not have to manually update this project again.

After you have installed the prerequisite CLI tools, run `npm install` in this
directory. This will install the necessary NodeJS modules inside the
`node_modules` folder.

After NodeJS has finished installing its own modules, it will automatically
invoke `grunt install` for you. This is a grunt task that is specifically
designed to keep the project in sync amongst maintainers.

## Grunt
Refer to the @link project_grunt Grunt Tasks @endlink sub-topic.


## Releases
Refer to the @link project_releases Releases @endlink sub-topic.

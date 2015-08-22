# Varsity-Engine
Engine page for Varsity

## Installation

1. Set up the config file in inc as per the sample, also include constants NODE_URL to point to the LiveBlog app instance and API_KEY.
2. Set the blog id in index.php - must exist in the blogs table

Varsity matches can be set in the varsity table.

## Known limitations

* Any valid login can use the app - **you must fix this**
* Twitter support is likely dead due to retirement of API v1
* The User model has changed slightly since this code was written so the User class may need updating
* PHP mysql library is discontinued and should be replaced with mysqli

## See also
* LiveBlog node app: https://github.com/FelixOnline/LiveBlog
* Frontend: https://github.com/FelixOnline/Varsity
* Version for TEDx blog (no matches) in the TEDx branch (this is really old)

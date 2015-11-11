pizza_blog
==========

Pizza Blog is a very simple blog engine that allows you to see your changes live. 

A sample blog can be seen here: http://pizza.darrenbeckett.com


Features:
---------
* drag & drop sortable categories
* drag & drop sortable article list (defaults to newest at the top)
* scheduled article posting
* basic WYSIWYG editing
* mobile browser compatible
* generates static pages for each article
* generates RSS feed automatically
* simple about page
* sharing links for facebook, google+, twitter, and pinterest


Instructions:
-------------
1. copy all files to web root
2. create database (see: db_schema.sql)
3. in a browser go to: http://yourdomain.com/oven/setup/
4. rename sample_htaccess to .htaccess
5. rename sample_robots.txt to robots.txt
6. customize CSS as needed (_custom.css and _icustom.css for mobile)
7. add /oven/cron.php to crontab


Tips:
-----
* hitting carriage return four times consecutively creates a new text block with an image placeholder
* hitting delete from the first character position in a text block, merges the current text block with the previous block
* the article date accepts natural language (e.g. tomorrow, next week, now) or standard date formats
* rename an article to "DELETE ME" in order to delete the article
* there is a hidden link to the admin under the "RSS Feed" link on the live site

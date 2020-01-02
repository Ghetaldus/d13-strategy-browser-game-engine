d13 - open-source strategy browser game engine
==========================================================================================

Updates & Latest version available at Sourceforge and Github:

https://sourceforge.net/projects/d13/
https://github.com/CriticalHit-d13/d13

News & project documentation can now be found at my website:

http://www.critical-hit.biz

RELEASE CANDIDATE 0
------------------------------------------------------------------------------------------
This is an early release of the d13 open source engine. 

It is NOT recommended to start a full-fledged project based on d13 now, as there will
be more changes to the sourcecode in the near future. I recommend to wait until the
project matures into the first stable release version (Release Candidate 1).

------------------------------------------------------------------------------------------

INSTALL
------------------------------------------------------------------------------------------
Hosting Requirements: http web server, mysql database system (mysqli capable 5.1/5.5), php (5.6/7.0).
Before using this code make sure you have the latest versions of the above mentioned software requirements.

Installation steps:
1. create a database in mysql locally or on your webhost.
2. import the install/d13_install.sql file in the database you just created.
3. edit the database connection data in the 'd13/core/d13_config.inc.php' file;
4. rename the d13_config.inc.php file by removing CHANGEME in its name.
5. go to the install page in your browser "http://localhost/d13/install/install.php"
6. add the admin account and map data to the database;
- optionally, you can edit the map you'll be using by changing the "install/grid.png" image;
- each pixel represents one map sector;
- blue (RGB: 0 0 255) is for water, green (RGB: 0 255 0) is for land;
7. delete the "install" folder;
8. further customization is done by editing the files in the "data" folder.
9. even more customization is possible by duplicating the "default" directory in
"templates" and editing the images and template files within.

End User Requirements: Up to date browser with HTML5 / CSS3 capability.

CREDITS
------------------------------------------------------------------------------------------
2008-2013 Andrei Busuioc (Devman)
2015-2016 Weaver (Fhizban)

with help from:

BlackScorp
Harald
Xugro

and many others

LICENSE
------------------------------------------------------------------------------------------

This software is provided 'as-is', without any express or implied
warranty. In no event will the authors be held liable for any damages
arising from the use of this software.

Permission is granted to anyone to use this software for any purpose,
including commercial applications, and to alter it and redistribute it
freely, subject to the following restrictions:

1. The origin of this software must not be misrepresented; you must not
claim that you wrote the original software. If you use this software
in a product an acknowledgment of the original author in the product documentation is required.

2. Altered source versions must be plainly marked as such, and must not be
misrepresented as being the original software.

3. This notice may not be removed or altered from any source distribution.


d13 - strategy browser game engine
==========================================================================================

RELEASE CANDIDATE 0
------------------------------------------------------------------------------------------

V0XX	--- PLANNED UPCOMING UPDATE(S) ---
		
		* Map System
		
		* Finalize existing combat system:
		
			- Sabotage Reports
			- Sabotage Checks
			
		* Trade Building (Tradepost): Trade resources/components with other users

V0XX	--- NEXT SCHEDULED UPDATE ---

		* Shop Building (Blackmarket): Buy resources, components, buff's, shield's
		
		* Buff System: Add temporary speed/efficiency buff's to the current node (town)
		
		* Lottery Building: Allows to collect a random reward every X hours

V017	--- SECOND STABLE VERSION

		* Another bunch of more or less critical bugfixes and corrections
		* Reducing the remaining HTML in files (not much left anyways)
		* corrected several CSS glitches
		* expanded functionality of SCOUTing

V016	* Fixed a few more bugs
		* Added autoload to classes
		* Added LINUX line breaks
		* most object classes are now derived from one base class
		* Even more bugfixes, refactoring and streamlining of the code
		* renamed hybrid building/unit class into "turret"
		
		* Added Scout Checks to Combat System
		* Added Scout Report to Combat System
		* Enabled SCOUT Attack type

V015	* Project now also on Github: https://github.com/CriticalHit-d13/d13
		* Added pagination to league ranking list			
		* Frontend Checking of Army Integrity finished
		* Fixed a bug that allowed to build more buildings than possible
		* Fixed a few alliance bugs
		* Technologies costs and requirements can now scale according to upgrades
		* Fixed calculation of attribute upgrades
		* Users can now select an avatar according to their level
		* Alliances can now select an avatar and TAG according to their level
		* Added dynamic loading of data and language files
		* CKEditor messages are now limited, limit is shown during message edit
		
		* Refactored all remaining procedural pages into their own classes
		* Added Technology class
		* Added Component class
		
		* Moved top-navbar to bottom - layout is now "full window"

V014	* Added CKEditor for messages
		* Added extended tooltip system
		* Rows in the visual town display are now irregular to break up the design a bit
		* Tweaked a few CSS styles
		* Fixed a few minor bugs
		* Minor GUI adjustments
		* Minor Performance issue
		* Messages now filterable in groups
		* Sliders now disabled if you cannot craft/train anymore
		* Double-click Slider to set it to the maximum amount
		* Researches are removed from the build list if you have researched max level.
		* Modules are removed from the build list if you have built max instances.
		* Lots of combat system bug fixes
		
		* Preperations for next update
		* Preperations for refactoring
	
V013 	* Basic Combat System implemented, this covers:

		* Different types of attacking an enemy:
			- Basic Attack (Resource Raiding in progress)
			- Raze
			- Conquer
		* Gain/Lose Trophies when attacking/defending / Gain Experience via Attacks
		* Resource Raiding: Gain/Lose Resourcen when attacking/defending
		* Conquer (capture) enemy towns implemented
		* Raze (destroy) enemy towns implemented
		* Leader (hero) units implemented
		* Leader required for certain attack types implemented
		* Limited units implemented
		* Critical Damage implemented
		* Situational combat modifiers implemented
			(units can now acquire special bonus when attacking/defending)
			(some units can grant a special bonus to the whole army)
		* Flexible attack types implemented
			(the admin can define new and unique attack types, the basic game comes
			with several different attack types).
		* Units feature a movement type (ex: ground, sea, air) and combat types can
		  be limited to certain movement types as well.
		* Players can now optionally gain experience by crafting components or training
		  units.
		* Optional (Fuel) resource cost for armies can now also be a component (that
		  must be crafted first).
		
V012	* player accounts now gain experience through building, upgrades & researches
		* player accounts now level up - this affects league ranking
		* player accounts are now divided into leagues to measure their power
		* Added player status and league pages
		* Total Army stats now shown during army setup
		* Added optional Fuel resource cost to army movement
		* Added randomized terrain tiles to the city view
		* Marching units now shown in the Warfare type building as well
		* Players can send one army marching per Warfare building they have, multiple only
		  possible with multiple warfare buildings
		* Shield system and Newbie Protection system implemented
		  (inclusive configuration options for various shield types)
		* Refactored all build queues into own class
		* Restyled the representation of queues utilizing a overlay panel
		* All active tasks (craft/train/research/armies) an now be cancelled, either from
		  the queue overview in the town view or directly from the building itself. buildings
		  and building upgrades can also be cancelled - but only from the main town view.
		* Some resources are now automagically produced if the admin wants to.
		* Indirect/Multi upgrades now possible
			(each technology can now affect an upgrade, that upgrade can affect either
			 one or more units or one or more buildings)
		* Lots of Bugfixes
		* Optimized/Optional templates caching
		* Added caching of template files

V011 	
		* Added new attributes for units (vision, capacity, critical)
		* started project documentation at http://www.critical-hit.biz
		* workers inside a building cannot be modified anymore when a task is running
		* implemented class based OP caching of the JSON files to increase performance
		* cleaned up some templates
		* multiple research/craft/train projects can now run at the same time when the
		  player has enough extra buildings of the same type available (aka 2+ Barracks)
		* buildings can now have a unique pending image, defined in modules.data
		* Users now have a level, experience and trophies (no effect yet)
		* Usernames are now unique (mixed lowercase, uppercase etc. registration disabled)
		* Hybrid harvest/storage buildings implemented
		* Unlimited resources that require no storage building implemented
		* Fixed a minor security issue
		* Replaced most text inputs with HTML input slider (Mobile First)
		* Restyled resource bar a bit

V010 --- FIRST STABLE VERSION ---

V010 * New building type: 'storvest' a hybrid building of a storage and a harvest building
allows to both produce and store resources.

V009 * Building/Upgrading duration can now be accelerated by investing more Workers (or InputResource
in general).

------------------------------------------------------------------------------------------
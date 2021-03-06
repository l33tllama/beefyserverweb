#!/usr/bin/python

# Purpose of this: 
#	- Send text files to MONGODB
# 	- Prcatice using MONGODB

import MySQLdb as mdb
import os
import sys
import re
import pymongo

from pymongo import MongoClient
from pprint import pprint
#AN EDIT!!!

# File locations
base_folder = "/home/leo/public_html/pages/misc/games_lists/"
cur = "current_games/"
new_puap_linux = "new-pickupandplay-games.txt"
new_puap_win = "new-pickupandplay-games-windows.txt"
fav_puap_linux = "favourite-pickupandplay-games.txt"
fav_puap_win = "favourite-pickupandplay-games-windows.txt"
other_puap_linux = "other-pickupandplay-games.txt"
other_puap_win = "other-pickupandplay-games-windows.txt"
cur_new_puap_linux = cur + "new-linux.txt"
cur_new_puap_win = cur + "new-win.txt"
cur_fav_puap_linux = cur + "fav-linux.txt"
cur_fav_puap_win = cur + "fav-win.txt"
cur_other_puap_linux = cur + "other-linux.txt"
cur_other_puap_win = cur + "other-win.txt"
played_games = "played-games.txt"

# Open Files
# List of new, favourite and other games
new_puap_linux_file = open(base_folder + new_puap_linux, "r")
new_puap_win_file = open(base_folder + new_puap_win, "r")
fav_puap_linux_file = open(base_folder + fav_puap_linux, "r")
fav_puap_win_file = open(base_folder+ fav_puap_win, "r")
other_puap_linux_file = open(base_folder + other_puap_linux, "r")
other_puap_win_file = open(base_folder + other_puap_win, "r")

#Text files containing current game for each category to play
cur_new_puap_linux_file = open(base_folder + cur_new_puap_linux, "r")
cur_new_puap_win_file = open(base_folder + cur_new_puap_win, "r")
cur_fav_puap_linux_file = open(base_folder + cur_fav_puap_linux, "r")
cur_fav_puap_win_file = open(base_folder + cur_fav_puap_win, "r")
cur_other_puap_linux_file = open(base_folder + cur_other_puap_linux, "r")
cur_other_puap_win_file = open(base_folder + cur_other_puap_win, "r")

played_games_file = open(played_games, "r")

# Connect to mongo server
mongo_client = MongoClient('localhost', 27017)
# Get reference to "games_lists" DB
users_games_lists = mongo_client.games_lists['users']

games_lists = [	[new_puap_linux_file, "unplayed", "new", "linux" ],
			[new_puap_win_file, "unplayed", "new", "win" ],
			[fav_puap_linux_file, "unplayed", "fav", "linux" ],
			[fav_puap_win_file, "unplayed", "fav", "win" ],
			[other_puap_linux_file, "unplayed", "other", "linux" ],
			[other_puap_win_file, "unplayed", "other", "win" ],
			[cur_new_puap_linux_file, "current", "new", "linux" ],
			[cur_new_puap_win_file, "current", "new", "win" ],
			[cur_fav_puap_linux_file, "current", "fav", "linux" ],
			[cur_fav_puap_win_file, "current", "fav", "win" ],
			[cur_other_puap_linux_file, "current", "other", "linux" ],
			[cur_other_puap_win_file, "current", "other", "win" ],
			[played_games_file, "played" ],
			[ ["dummy"], "dummy" ] ]

# Unplayed, current and played game lists
users_games_lists.update({'username' : 'leo'}, {		'username' : 'leo',
								'password_md5': '3bb7b584635d792fd74778558371bf37', 
								'session_id': '',
								'steam_puap_games' : []},True)

#Test asdasd

# Get mongoDB collection named current games list
tmp_current_game_list = []
tmp_played_game_list = []
tmp_unplayed_game_list = []

tmp_game_list = []

print "################################"
print "#### STARTING NEW GAME DUMP ####"
print "################################"

# For each of the games lists (name, file holding list of games)
for game_list in games_lists:
	
	# Without dummy, last list data gets mangled for some unknown reason D:
	if game_list[1] is not "dummy":
		
		# Loop through each game in each game list
		for game_name in game_list[0]:
			
			# Strip newline char from textfile and replace single quotes with two single quotes for MySQL formatting
			game_name = game_name.strip().replace("'", "''")
			game_name_clean = re.sub('[^A-Za-z0-9 ]','', game_name).lower()
			game_name_clean = re.sub(" ", "-", game_name_clean)
			
			game_added = False
			list_name = "None"
			
			# If list is unplayed, add to unplayed list with 
			if (game_list[1] == "unplayed") or (game_list[1] == "current"):
				if game_name == "":
					print ("Empty game")
				else:
					print ("Adding game " + game_name + " to list " + game_list[1] + " (" + game_list[2] + "/" + game_list[3] + ")")
					if game_list[1] == "unplayed":
						tmp_game_list.append( {		"GameName" : game_name, 
													"GameNameClean" : game_name_clean, 
													"Category" : game_list[2], 
													"Platform" : game_list[3],
													"PlayedState": game_list[1]} )
					elif game_list[1] == "current":
						date_added_str = "19-3-2014 12:00"
						tmp_game_list.append( {		"GameName" : game_name, 
													"GameNameClean" : game_name_clean, 
													"Category" : game_list[2], 
													"Platform" : game_list[3],
													"Starred" : False,
													"PlayedState": game_list[1],
													"DateAdded" : date_added_str} )

			# If list is played, no platform info available atm, also category and stared fields are not needed
			elif game_list[1] == "played":
				print("Adding game " + game_name + " to played list")
				tmp_game_list.append({		"GameName" : game_name, 
											"GameNameClean" : game_name_clean,
												"PlayedState": game_list[1]})	
		# Close file
		game_list[0].close()

# Update steam games list
users_games_lists.update({'username' : 'leo'}, {'$set' : {'steam_puap_games' : tmp_game_list}}, False)



'''
MySQL = unproductive, annoying, fiddly and overkill for many of my projects

#create_table = 'CREATE TABLE IF NOT EXISTS {0}(\n`ID` INT PRIMARY KEY AUTO_INCREMENT NOT NULL,\n`GameName` VARCHAR(255) NOT NULL, `GameNameClean` VARCHAR(255) NOT NULL)'.format(game_list[0])
	#
	#print("Creating table: '{0}' using command: \n{1}".format(game_list[0], create_table))
	#cur.execute(create_table)

# ----- Old painful MySQL stuff -----
#insert_game = "REPLACE INTO {0}\nSET `GameName` = '{1}'".format(game_list[0], unicode(game_name, 'utf-8'))	
# Awesome unique entry code: http://stackoverflow.com/questions/3164505/mysql-insert-record-if-not-exists-in-table
#insert_game = "INSERT INTO {0} (GameName, GameNameClean)\nSELECT * FROM (SELECT '{1}', '{2}') as tmp\nWHERE NOT EXISTS (\n\tSELECT GameName\n\tFROM {0}\n\tWHERE\n\tGameName = '{1}'\n)\nLIMIT #1".format(game_list[0], unicode(game_name), unicode(game_name_clean, 'utf-8')) 
#insert_game = "REPLACE INTO {0} (GameName, GameNameClean)\nVALUES ('{1}', '{2}')".format(game_list[0], unicode(game_name), unicode(game_name_clean, 'utf-8')) 
#print("Inserting game: '{0}' (cleaned: '{1}') into table: '{2}' using command:\n{3}").format(game_name, game_name_clean, game_list[0], insert_game)
#cur.execute(insert_game)
# -----------------------------------

con = None
try:
	con = mdb.connect('localhost', 'leo', 'gaemsmysql', 'games_lists')
	cur = con.cursor()
	insert_game = ""
			
except mdb.Error, e:
	print "Error %d: %s" % (e.args[0], e.args[1])
	sys.exit(1)
	
finally:
	if con:
		con.close()
'''
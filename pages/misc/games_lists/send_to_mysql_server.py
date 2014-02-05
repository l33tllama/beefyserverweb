#!/usr/bin/python

import MySQLdb as mdb
import os
import sys
import re
import pymongo

from pymongo import MongoClient
from pprint import pprint

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
mongo_games_lists = mongo_client.games_lists

games_lists = [	["New_linux", new_puap_linux_file],
			["New_win", new_puap_win_file],
			["Fav_linux", fav_puap_linux_file],
			["Fav_win", fav_puap_win_file],
			["Other_linux", other_puap_linux_file],
			["Other_win", other_puap_win_file],
			["Current_new_linux", cur_new_puap_linux_file],
			["Current_new_win", cur_new_puap_win_file],
			["Current_fav_linux", cur_fav_puap_linux_file],
			["Current_fav_win", cur_fav_puap_win_file],
			["Current_other_linux", cur_other_puap_linux_file],
			["Current_other_win", cur_other_puap_win_file],
			["Played_games", played_games_file],
			["dummy", None]	]
	#
for game_list in games_lists:
	#create_table = 'CREATE TABLE IF NOT EXISTS {0}(\n`ID` INT PRIMARY KEY AUTO_INCREMENT NOT NULL,\n`GameName` VARCHAR(255) NOT NULL, `GameNameClean` VARCHAR(255) NOT NULL)'.format(game_list[0])
	#
	#print("Creating table: '{0}' using command: \n{1}".format(game_list[0], create_table))
	#cur.execute(create_table)
	tmp_game_list = []
	for game_name in game_list[1]:
		# Strip newline char from textfile and replace single quotes with two single quotes for MySQL formatting
		game_name = game_name.strip().replace("'", "''")
		game_name_clean = re.sub('[^A-Za-z0-9 ]','', game_name).lower()
		game_name_clean = re.sub(" ", "-", game_name_clean)
		tmp_game_list.append({"GameName" : game_name, "GameNameClean" : game_name_clean})
		
		
		
		#insert_game = "REPLACE INTO {0}\nSET `GameName` = '{1}'".format(game_list[0], unicode(game_name, 'utf-8'))	
		# Awesome unique entry code: http://stackoverflow.com/questions/3164505/mysql-insert-record-if-not-exists-in-table
		#insert_game = "INSERT INTO {0} (GameName, GameNameClean)\nSELECT * FROM (SELECT '{1}', '{2}') as tmp\nWHERE NOT EXISTS (\n\tSELECT GameName\n\tFROM {0}\n\tWHERE\n\tGameName = '{1}'\n)\nLIMIT #1".format(game_list[0], unicode(game_name), unicode(game_name_clean, 'utf-8')) 
		#insert_game = "REPLACE INTO {0} (GameName, GameNameClean)\nVALUES ('{1}', '{2}')".format(game_list[0], unicode(game_name), unicode(game_name_clean, 'utf-8')) 
		#print("Inserting game: '{0}' (cleaned: '{1}') into table: '{2}' using command:\n{3}").format(game_name, game_name_clean, game_list[0], insert_game)
		#cur.execute(insert_game)
	#data_to_insert = { tmp_game_list }
	print ( game_list[0] + " data to insert: ")
	pprint ( tmp_game_list )
	mongo_game_list = mongo_games_lists[game_list[0]]
	mongo_game_list.insert(tmp_game_list)
	game_list[1].close()

'''
MySQL = headache
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
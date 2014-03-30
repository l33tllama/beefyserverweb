 #!/usr/bin/env python

# About this program:
# Looks through text files, manually set with games from Steam library (and maybe others places)
# Selects new games to play, each week, using cron
# Adds played games to a list so it doesn't pick them again

import os
import math
import random
import datetime

# File Locations
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
cur_new_puap_linux_file = open(base_folder + cur_new_puap_linux, "a+r")
cur_new_puap_win_file = open(base_folder + cur_new_puap_win, "a+r")
cur_fav_puap_linux_file = open(base_folder + cur_fav_puap_linux, "a+r")
cur_fav_puap_win_file = open(base_folder + cur_fav_puap_win, "a+r")
cur_other_puap_linux_file = open(base_folder + cur_other_puap_linux, "a+r")
cur_other_puap_win_file = open(base_folder + cur_other_puap_win, "a+r")

played_games_file = open(base_folder + played_games, "r+a")
                    
# Week of month
day_of_month = datetime.datetime.now().day
week_number = (day_of_month - 1) // 7 + 1

# Get first item in array (Not needed to check if file empty now?)
def get_first(iterable, default=None):
    if iterable:
        for item in iterable:
            return item
    return default

# Get the full games list by name
def get_games_file_by_name(name):
    return {
        'new_linux': new_puap_linux_file,
        'new_win': new_puap_win_file,
        'fav_linux' : fav_puap_linux_file,
        'fav_win' : fav_puap_win_file,
        'other_linux' : other_puap_linux_file,
        'other_win' : other_puap_win_file        
    }.get(name, None)

# Get the current games list by name
def get_cur_games_file_by_name(name):
    return {
        'new_linux': cur_new_puap_linux_file,
        'new_win': cur_new_puap_win_file,
        'fav_linux' : cur_fav_puap_linux_file,
        'fav_win' : cur_fav_puap_win_file,
        'other_linux' : cur_other_puap_linux_file,
        'other_win' : cur_other_puap_win_file
    }.get(name, None)

def pick_random_game(games_list):
    return games_list[random.randint(0, len(games_list) - 1)]

# Pick this week's new puap Linux game
def choose_cur_new_game(list_name):
    # Get the games list by name
    games_list_file = get_games_file_by_name(list_name)
    cur_games_list_file = get_cur_games_file_by_name(list_name)
    potential_games = []
    skip = False
    for game_name in games_list_file:
        skip = False
        # Check against all played games
        for played_game in played_games_file:
            if game_name is played_game:
                skip = True
        # If not found in played list, add to new games to play list
        # Pick this week's new puap Windows Game
        if skip is False:
            if game_name[-1:] == "\n":
                game_name = game_name[:-1]   
            print "Adding {} to list of potential games to add to {}".format(game_name, "current-" + list_name)
            potential_games.append(game_name)
    new_game = pick_random_game(potential_games)
    print "Picked random game: " + new_game + "!!"
    current_games_file = get_cur_games_file_by_name(list_name)
    #current_games_file.seek(0)
    current_games_file.write(new_game[0:] + "\n")
    

# Check if current games file is empty 
def check_if_empty(list_name):
    print "checking " + list_name
    games_list_file = get_cur_games_file_by_name(list_name);
    games_list_file.seek(0)
    if not games_list_file.read(1):
        print("Adding new file to {} for first time.".format(list_name))
        choose_cur_new_game(list_name)
    else:
        print("{} is not empty, skipping".format(list_name))

# If files empty (one time check?)

check_if_empty('new_linux')
check_if_empty('new_win')
check_if_empty('fav_linux')
check_if_empty('fav_win')
check_if_empty('other_linux')
check_if_empty('other_win')

# TODO: when looking for new games, check if not played first, then add to array
# Then pick one at random from array, and send to current file

# From the web, I can click a thing that says "Finished for now" and it will add
# the game to played list, so next time it won't be added again

    
    

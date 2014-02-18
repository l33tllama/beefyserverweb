#!/usr/bin/env python

import os
import math
import random

games_file_string = "/home/leo/public_html/txt/games.txt"
played_games_file_string = "/home/leo/public_html/txt/played_games.txt"
this_weeks_games_file_string = "/home/leo/public_html/txt/this_weeks_casual_games.txt"

games_list_file = open(games_file_string, "r")

potential_games = []

skip = False
game_count = 0

#Iterate through each game to play
for game_line in games_list_file:
    skip = False
    played_list_file = open(played_games_file_string, "r")

    #check against all played games
    for played_line in played_list_file:
        #If current game is onthe played list, mark skip as true
        if played_line == game_line:
            skip = True
    #If game not played: add to potential games list
    if skip == False:
        potential_games.append(game_line)
    
num_potentials = len(potential_games)   
#print "Number of potential games: "+ str(num_potentials) + " vs " + str(game_count)
#for potential_game in potential_games:
    #print potential_game   
#print " 0th game: " + potential_games[0] + "52nd game: " + potential_games[num_potentials - 1]    
#for number in range(0, 500):
 #   game_number = random.randint(1, num_potentials)
  #  if game_number == 1:
   #     print "Reached game 1"
    #elif game_number == num_potentials:
     #   print "Reached game " + str(num_potentials) 

#Pick to potential games
random_game_1 = potential_games[random.randint(1, num_potentials)]
random_game_2 = potential_games[random.randint(1, num_potentials)]

#If random game 2 == rnd game 1, try randoming again
while random_game_1 == random_game_2:
    random_game_2 = potential_games[random.randint(1, num_potentials)]

print "Random game 1 " + random_game_1
print "Random game 2 " + random_game_2

#Append two games to played games list
played_list_file = open(played_games_file_string, "a")
played_list_file.write(random_game_1)
played_list_file.write(random_game_2)

#clear this weeks' games list and add the two games to it
this_weeks_file = open(this_weeks_games_file_string, "w")
this_weeks_file.write(random_game_1 + random_game_2)
